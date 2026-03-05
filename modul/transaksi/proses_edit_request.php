<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if ($_POST) {
    $id_request   = mysqli_real_escape_string($koneksi, $_POST['id_request']);
    $tgl_request  = mysqli_real_escape_string($koneksi, $_POST['tgl_request']);
    $nama_pemesan = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_pemesan']));
<<<<<<< HEAD
    $nama_pembeli = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_pembeli']));
    $user_login   = $_SESSION['username']; 
    $now          = date('Y-m-d H:i:s');

    mysqli_begin_transaction($koneksi);
    try {
        // 1. UPDATE HEADER
        $query_h = "UPDATE tr_request SET 
                    tgl_request  = '$tgl_request', 
                    nama_pemesan = '$nama_pemesan',
                    nama_pembeli = '$nama_pembeli',
                    updated_by   = '$user_login',
                    updated_at   = '$now' 
                    WHERE id_request = '$id_request'";
        if (!mysqli_query($koneksi, $query_h)) throw new Exception(mysqli_error($koneksi));

        // 2. Kumpulkan id_detail yang dikirim dari form (yang tidak dihapus user)
        $id_detail_array  = $_POST['id_detail'] ?? [];
        $id_barang_array  = $_POST['id_barang'] ?? [];

        // Pisahkan id_detail yang valid (existing) vs kosong (baris baru)
        $id_detail_dikirim = array_filter($id_detail_array, fn($v) => !empty($v) && intval($v) > 0);

        // 3. Hapus hanya baris yang DIHAPUS user DAN statusnya masih PENDING/APPROVED/REJECTED
        //    Baris TERBELI dan MENUNGGU VERIFIKASI TIDAK ikut dihapus
        if (!empty($id_detail_dikirim)) {
            $ids_aman = implode(',', array_map('intval', $id_detail_dikirim));
            $query_del = "DELETE FROM tr_request_detail 
                          WHERE id_request = '$id_request' 
                          AND id_detail NOT IN ($ids_aman)
                          AND status_item IN ('PENDING', 'APPROVED', 'REJECTED')";
        } else {
            $query_del = "DELETE FROM tr_request_detail 
                          WHERE id_request = '$id_request'
                          AND status_item IN ('PENDING', 'APPROVED', 'REJECTED')";
        }
        if (!mysqli_query($koneksi, $query_del)) throw new Exception(mysqli_error($koneksi));

        // 4. LOOP: UPDATE existing atau INSERT baru
        foreach ($id_barang_array as $key => $val) {
            if (empty($val)) continue;

            $id_detail = intval($id_detail_array[$key] ?? 0);
            $id_brg    = intval($val);
            $nama_m    = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_barang_manual'][$key] ?? ''));
            $kat       = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kategori_request'][$key] ?? ''));
            $kwal      = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kwalifikasi'][$key] ?? ''));
            $mobil     = intval($_POST['id_mobil'][$key] ?? 0);
            $tipe      = strtoupper(mysqli_real_escape_string($koneksi, $_POST['tipe_request'][$key] ?? 'STOK'));
            $qty       = floatval($_POST['jumlah'][$key] ?? 0);
            $sat       = strtoupper(mysqli_real_escape_string($koneksi, $_POST['satuan'][$key] ?? ''));
            $hrg       = floatval($_POST['harga'][$key] ?? 0);
            $ket       = strtoupper(mysqli_real_escape_string($koneksi, $_POST['keterangan'][$key] ?? ''));
            $subtotal  = $qty * $hrg;

            if ($id_detail > 0) {
                // UPDATE baris existing — HANYA jika statusnya masih PENDING
                $query_d = "UPDATE tr_request_detail SET
                                nama_barang_manual    = '$nama_m',
                                id_barang             = '$id_brg',
                                id_mobil              = '$mobil',
                                jumlah                = '$qty',
                                satuan                = '$sat',
                                harga_satuan_estimasi = '$hrg',
                                subtotal_estimasi     = '$subtotal',
                                kategori_barang       = '$kat',
                                kwalifikasi           = '$kwal',
                                tipe_request          = '$tipe',
                                keterangan            = '$ket'
                            WHERE id_detail    = '$id_detail'
                            AND id_request     = '$id_request'
                            AND status_item    = 'PENDING'";
            } else {
                // INSERT baris baru
                $query_d = "INSERT INTO tr_request_detail 
                                (id_request, nama_barang_manual, id_barang, id_mobil, jumlah, satuan, 
                                 harga_satuan_estimasi, subtotal_estimasi, kategori_barang, kwalifikasi, 
                                 tipe_request, keterangan, status_item) 
                            VALUES 
                                ('$id_request', '$nama_m', '$id_brg', '$mobil', '$qty', '$sat', 
                                 '$hrg', '$subtotal', '$kat', '$kwal', '$tipe', '$ket', 'PENDING')";
            }
            if (!mysqli_query($koneksi, $query_d)) throw new Exception(mysqli_error($koneksi));
        }

=======
    $nama_pembeli = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_pembeli'])); // Ambil data pembeli
    $user_login   = $_SESSION['username']; 
    $now          = date('Y-m-d H:i:s');

    // MULAI TRANSAKSI DATABASE
    mysqli_begin_transaction($koneksi);

    try {
        // 1. UPDATE HEADER
        $query_h = "UPDATE tr_request SET 
                    tgl_request  = '$tgl_request', 
                    nama_pemesan = '$nama_pemesan',
                    nama_pembeli = '$nama_pembeli',
                    updated_by   = '$user_login',
                    updated_at   = '$now' 
                    WHERE id_request = '$id_request'";
        
        if (!mysqli_query($koneksi, $query_h)) throw new Exception(mysqli_error($koneksi));

        // 2. HAPUS DETAIL LAMA
        if (!mysqli_query($koneksi, "DELETE FROM tr_request_detail WHERE id_request = '$id_request'")) {
            throw new Exception(mysqli_error($koneksi));
        }

        // 3. INSERT ULANG DETAIL
        $id_barang_array = $_POST['id_barang'];
        foreach ($id_barang_array as $key => $val) {
            if(empty($val)) continue;

            $id_brg = (int)$val;
            $nama_m = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_barang_manual'][$key] ?? ''));
            $kat    = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kategori_request'][$key] ?? ''));
            $kwal   = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kwalifikasi'][$key] ?? ''));
            $mobil  = (int)($_POST['id_mobil'][$key] ?? 0);
            $tipe   = strtoupper(mysqli_real_escape_string($koneksi, $_POST['tipe_request'][$key] ?? 'STOK'));
            $qty    = (float)($_POST['jumlah'][$key] ?? 0);
            $sat    = strtoupper(mysqli_real_escape_string($koneksi, $_POST['satuan'][$key] ?? ''));
            $hrg    = (float)($_POST['harga'][$key] ?? 0);
            $ket    = strtoupper(mysqli_real_escape_string($koneksi, $_POST['keterangan'][$key] ?? ''));
            $subtotal = $qty * $hrg;

            $query_d = "INSERT INTO tr_request_detail 
                        (id_request, nama_barang_manual, id_barang, id_mobil, jumlah, satuan, harga_satuan_estimasi, subtotal_estimasi, kategori_barang, kwalifikasi, tipe_request, keterangan) 
                        VALUES 
                        ('$id_request', '$nama_m', '$id_brg', '$mobil', '$qty', '$sat', '$hrg', '$subtotal', '$kat', '$kwal', '$tipe', '$ket')";
            
            if (!mysqli_query($koneksi, $query_d)) throw new Exception(mysqli_error($koneksi));
        }

        // JIKA SEMUA BERHASIL, SIMPAN PERMANEN
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        mysqli_commit($koneksi);
        header("location:pr.php?pesan=update_sukses");
        exit;

    } catch (Exception $e) {
<<<<<<< HEAD
=======
        // JIKA ADA SATU SAJA YANG ERROR, BATALKAN SEMUA (Data detail tidak jadi terhapus)
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        mysqli_rollback($koneksi);
        echo "Gagal menyimpan perubahan: " . $e->getMessage();
    }
}
?>