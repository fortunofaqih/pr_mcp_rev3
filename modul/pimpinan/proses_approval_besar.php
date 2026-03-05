<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

// Proteksi akses
if ($_SESSION['status'] != "login" || ($_SESSION['role'] ?? '') !== 'manager') {
    header("location:../../login.php?pesan=akses_ditolak");
    exit;
}

$action     = $_GET['action'] ?? '';
$id         = (int)($_GET['id'] ?? 0);
$catatan    = mysqli_real_escape_string($koneksi, $_GET['catatan'] ?? '');
$approve_by = mysqli_real_escape_string($koneksi, $_SESSION['nama'] ?? $_SESSION['username']); // Gunakan Nama agar lebih user-friendly
$tgl_now    = date('Y-m-d H:i:s');
$redirect   = ($_GET['redirect'] ?? '') === 'pimpinan' ? 'approval_pimpinan.php' : 'list_pr_besar.php';

if (!$id || !in_array($action, ['approve','reject'])) {
    header("location:{$redirect}");
    exit;
}

// Mulai Transaksi (Agar data konsisten)
mysqli_begin_transaction($koneksi);

try {
    // Cek PR masih MENUNGGU APPROVAL
    $cek = mysqli_query($koneksi, "SELECT id_request FROM tr_request WHERE id_request='$id' AND kategori_pr='BESAR' AND status_approval='MENUNGGU APPROVAL' FOR UPDATE");
    
    if (mysqli_num_rows($cek) == 0) {
        throw new Exception("Data tidak ditemukan atau sudah diproses.");
    }

    if ($action === 'approve') {
        // 1. Update PR
        mysqli_query($koneksi, "UPDATE tr_request SET 
            status_approval = 'DISETUJUI', 
            status_request  = 'PROSES', 
            approve_by      = '$approve_by', 
            tgl_approval    = '$tgl_now',
            updated_by      = '$approve_by',
            updated_at      = '$tgl_now'
            WHERE id_request = '$id'");

        // 2. Update PO: DRAFT -> TERKIRIM
        mysqli_query($koneksi, "UPDATE tr_purchase_order SET 
            status_po   = 'TERKIRIM', 
            approved_by = '$approve_by',
            tgl_approve = '$tgl_now' 
            WHERE id_request = '$id'");

        $pesan = "approved";

    } elseif ($action === 'reject') {
        // 1. Update PR
        mysqli_query($koneksi, "UPDATE tr_request SET 
            status_approval  = 'DITOLAK', 
            status_request   = 'DITOLAK', 
            catatan_pimpinan = '$catatan', 
            approve_by       = '$approve_by', 
            tgl_approval     = '$tgl_now',
            updated_by       = '$approve_by',
            updated_at       = '$tgl_now'
            WHERE id_request = '$id'");

        // 2. Update Detail Item
        mysqli_query($koneksi, "UPDATE tr_request_detail SET status_item='REJECTED' WHERE id_request='$id'");

        // 3. Batalkan PO
        mysqli_query($koneksi, "UPDATE tr_purchase_order SET status_po='BATAL' WHERE id_request='$id'");

        $pesan = "rejected";
    }

    // Jika sampai sini tidak ada error, simpan permanen
    mysqli_commit($koneksi);
    header("location:{$redirect}?pesan={$pesan}");

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan data
    mysqli_rollback($koneksi);
    header("location:{$redirect}?pesan=gagal&error=" . urlencode($e->getMessage()));
}

exit;