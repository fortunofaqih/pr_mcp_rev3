<?php
// ============================================================
// proses_simpan_besar.php
// Simpan PR Besar + langsung buat draft PO
// ============================================================
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if ($_SESSION['status'] != "login") { header("location:../../login.php?pesan=belum_login"); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("location:pr_besar.php"); exit; }

// ── 1. GENERATE NOMOR REQUEST ────────────────────────────────
$bulan_romawi = ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
$bln    = (int)date('n');
$thn    = date('y');
$prefix = "PRB/" . $bulan_romawi[$bln] . "/" . $thn;
$cek_no = mysqli_query($koneksi, "SELECT no_request FROM tr_request WHERE no_request LIKE '$prefix/%' AND kategori_pr='BESAR' ORDER BY id_request DESC LIMIT 1");
$urut   = 1;
if (mysqli_num_rows($cek_no) > 0) {
    $last  = mysqli_fetch_array($cek_no);
    $parts = explode('/', $last['no_request']);
    $urut  = (int)end($parts) + 1;
}
$no_request = $prefix . '/' . str_pad($urut, 4, '0', STR_PAD_LEFT);

<<<<<<< HEAD
// ── 2. SANITASI HEADER PR ────────────────────────────────────
$tgl_request  = mysqli_real_escape_string($koneksi, $_POST['tgl_request']);
$nama_pemesan = mysqli_real_escape_string($koneksi, strtoupper($_POST['nama_pemesan']));
$nama_pembeli = mysqli_real_escape_string($koneksi, strtoupper($_POST['nama_pembeli']));
$keterangan   = mysqli_real_escape_string($koneksi, strtoupper($_POST['keterangan']));
$created_by   = mysqli_real_escape_string($koneksi, $_SESSION['username']);

// ── 3. SIMPAN HEADER tr_request ──────────────────────────────
$sql_header = "INSERT INTO tr_request
    (no_request, tgl_request, nama_pemesan, nama_pembeli, status_request, kategori_pr, status_approval, keterangan, created_by)
    VALUES
    ('$no_request','$tgl_request','$nama_pemesan','$nama_pembeli','PENDING','BESAR','MENUNGGU APPROVAL','$keterangan','$created_by')";

if (!mysqli_query($koneksi, $sql_header)) { die("Gagal simpan header: ".mysqli_error($koneksi)); }
$id_request = mysqli_insert_id($koneksi);

// ── 4. SIMPAN DETAIL ITEM ────────────────────────────────────
$id_barang_arr   = $_POST['id_barang']          ?? [];
$nama_arr        = $_POST['nama_barang_manual'] ?? [];
$kategori_arr    = $_POST['kategori_request']   ?? [];
$kwalifikasi_arr = $_POST['kwalifikasi']        ?? [];
$id_mobil_arr    = $_POST['id_mobil']           ?? [];
$tipe_arr        = $_POST['tipe_request']       ?? [];
$jumlah_arr      = $_POST['jumlah']             ?? [];
$satuan_arr      = $_POST['satuan']             ?? [];
$harga_arr       = $_POST['harga']              ?? [];
$ket_item_arr    = $_POST['keterangan_item']    ?? [];
=======
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Tangkap Data Header
    $tgl_form      = $_POST['tgl_request'];
    $tgl_kode      = date('Ymd', strtotime($tgl_form));
    $user_login    = $_SESSION['nama'];
    $nama_pemesan  = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_pemesan']));
    
    // Sesuaikan dengan name="keterangan_umum" di form
    $keterangan_investasi = strtoupper(mysqli_real_escape_string($koneksi, $_POST['keterangan_umum'])); 

    // LOCK TABLES
    mysqli_query($koneksi, "LOCK TABLES tr_request WRITE, tr_request_detail WRITE");

    // 2. Generate Nomor Request (PRB = Purchase Request Besar)
    $query_no = mysqli_query($koneksi, "SELECT MAX(no_request) as max_code FROM tr_request WHERE no_request LIKE 'PRB-$tgl_kode%'");
    $data_no  = mysqli_fetch_array($query_no);
    $last_no  = $data_no['max_code'] ?? '';
    $sort_no  = (int) substr($last_no, -3);
    $new_no   = "PRB-" . $tgl_kode . "-" . str_pad(($sort_no + 1), 3, "0", STR_PAD_LEFT);

    // 3. Simpan Header
    $query_header = "INSERT INTO tr_request (
                        no_request, tgl_request, nama_pemesan, keterangan, 
                        status_request, kategori_pr, status_approval, created_by
                    ) VALUES (
                        '$new_no', '$tgl_form', '$nama_pemesan', '$keterangan_investasi', 
                        'PENDING', 'BESAR', 'PENDING', '$user_login'
                    )";
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612

$subtotal_total = 0;
for ($i = 0; $i < count($id_barang_arr); $i++) {
    $id_barang   = (int)($id_barang_arr[$i] ?? 0);
    $nama_manual = mysqli_real_escape_string($koneksi, strtoupper($nama_arr[$i] ?? ''));
    $kategori    = mysqli_real_escape_string($koneksi, strtoupper($kategori_arr[$i] ?? ''));
    $kwalifikasi = mysqli_real_escape_string($koneksi, strtoupper($kwalifikasi_arr[$i] ?? ''));
    $id_mobil    = (int)($id_mobil_arr[$i] ?? 0);
    $tipe        = mysqli_real_escape_string($koneksi, strtoupper($tipe_arr[$i] ?? 'LANGSUNG'));
    $jumlah      = (float)($jumlah_arr[$i] ?? 0);
    $satuan      = mysqli_real_escape_string($koneksi, strtoupper($satuan_arr[$i] ?? ''));
    $harga       = (float)($harga_arr[$i] ?? 0);
    $subtotal    = $jumlah * $harga;
    $ket_item    = mysqli_real_escape_string($koneksi, strtoupper($ket_item_arr[$i] ?? ''));

<<<<<<< HEAD
    if ($jumlah <= 0) continue;

    // Ambil nama dari master jika belum ada
    if ($id_barang > 0 && empty($nama_manual)) {
        $qnama = mysqli_query($koneksi, "SELECT nama_barang FROM master_barang WHERE id_barang=$id_barang");
        if ($rnama = mysqli_fetch_array($qnama)) {
            $nama_manual = mysqli_real_escape_string($koneksi, strtoupper($rnama['nama_barang']));
        }
=======
        // 4. Looping Detail
        foreach ($nama_barang_array as $key => $val) {
            if(empty(trim($val))) continue; 
            
            $input_barang = strtoupper(mysqli_real_escape_string($koneksi, $val));
            $hrg          = (float)($_POST['harga'][$key] ?? 0);
            $qty          = (float)($_POST['jumlah'][$key] ?? 0);
            $subtotal     = $qty * $hrg;
            
            $kwalifikasi  = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kwalifikasi'][$key] ?? ''));
            $satuan       = strtoupper(mysqli_real_escape_string($koneksi, $_POST['satuan'][$key] ?? ''));
            $id_mobil     = (int)($_POST['id_mobil'][$key] ?? 0);
            $kategori_brg = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kategori_request'][$key] ?? ''));
            $tipe_req     = strtoupper(mysqli_real_escape_string($koneksi, $_POST['tipe_request'][$key] ?? 'LANGSUNG'));
            
            // Tangkap keterangan per item (dari textarea di setiap baris)
            $ket_item     = strtoupper(mysqli_real_escape_string($koneksi, $_POST['keterangan_item'][$key] ?? ''));

            $query_detail = "INSERT INTO tr_request_detail (
                                id_request, nama_barang_manual, id_barang, id_mobil, 
                                jumlah, satuan, harga_satuan_estimasi, subtotal_estimasi, 
                                kategori_barang, tipe_request, kwalifikasi, keterangan
                            ) VALUES (
                                '$id_header', '$input_barang', 0, '$id_mobil', 
                                '$qty', '$satuan', '$hrg', '$subtotal', 
                                '$kategori_brg', '$tipe_req', '$kwalifikasi', '$ket_item'
                            )";
            mysqli_query($koneksi, $query_detail);
        }

        mysqli_query($koneksi, "UNLOCK TABLES");
        // Kita kirim pesan 'berhasil' DAN nomor request 'no'
        header("location:pr.php?pesan=berhasil&no=" . $new_no);
        } else {
        mysqli_query($koneksi, "UNLOCK TABLES");
        echo "Error: " . mysqli_error($koneksi);
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    }

    mysqli_query($koneksi, "INSERT INTO tr_request_detail
        (id_request, nama_barang_manual, id_barang, id_mobil, jumlah, satuan, harga_satuan_estimasi, subtotal_estimasi, kategori_barang, kwalifikasi, tipe_request, keterangan, status_item)
        VALUES
        ('$id_request','$nama_manual','$id_barang','$id_mobil','$jumlah','$satuan','$harga','$subtotal','$kategori','$kwalifikasi','$tipe','$ket_item','PENDING')");

    $subtotal_total += $subtotal;
}

// ── 5. SIMPAN DRAFT PO ───────────────────────────────────────
$id_supplier  = (int)($_POST['id_supplier'] ?? 0);
$tgl_po       = mysqli_real_escape_string($koneksi, $_POST['tgl_po']);
$diskon       = (float)($_POST['diskon'] ?? 0);
$ppn_persen   = (float)($_POST['ppn_persen'] ?? 0);
$catatan_po   = mysqli_real_escape_string($koneksi, $_POST['catatan_po'] ?? '');

$total_po     = $subtotal_total - $diskon;
$ppn_nominal  = $total_po * ($ppn_persen / 100);
$grand_total  = $total_po + $ppn_nominal;
$prepared_by  = strtoupper($nama_pembeli);
$approved_by  = ''; // akan diisi saat manager approve

// Generate nomor PO (DRAFT, belum final — akan dikunci saat approve)
$bulan_romawi2 = ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
$bln_po  = (int)date('n', strtotime($tgl_po));
$thn_po  = date('y', strtotime($tgl_po));
$suf_po  = "/" . $bulan_romawi2[$bln_po] . "/" . $thn_po;
$cek_po  = mysqli_fetch_array(mysqli_query($koneksi, "SELECT no_po FROM tr_purchase_order WHERE no_po LIKE 'MCP-%{$suf_po}' ORDER BY id_po DESC LIMIT 1"));
$urut_po = 1;
if ($cek_po) { preg_match('/MCP-(\d+)/', $cek_po['no_po'], $m); $urut_po = (int)($m[1]??0) + 1; }
$no_po   = "MCP-" . str_pad($urut_po, 4, '0', STR_PAD_LEFT) . $suf_po;

mysqli_query($koneksi, "INSERT INTO tr_purchase_order
    (no_po, id_request, id_supplier, tgl_po, subtotal, diskon, total, ppn_persen, ppn_nominal, grand_total, catatan, prepared_by, approved_by, status_po, created_by)
    VALUES
    ('$no_po','$id_request','$id_supplier','$tgl_po','$subtotal_total','$diskon','$total_po','$ppn_persen','$ppn_nominal','$grand_total','$catatan_po','$prepared_by','$approved_by','DRAFT','$created_by')");

// ── 6. REDIRECT ──────────────────────────────────────────────
header("location:tambah_request_besar.php?pesan=berhasil_kirim");
exit;
