<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if ($_SESSION['status'] != "login") {
    header("location:../../login.php?pesan=belum_login");
    exit;
}

$tgl_awal  = isset($_GET['tgl_awal'])  ? $_GET['tgl_awal']  : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
$search    = isset($_GET['search'])    ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$nama_bulan = [
    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
    '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
    '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
];

<<<<<<< HEAD
// QUERY GABUNGAN
$query_sql = "SELECT 
                id_transaksi, driver_tetap, plat_nomor, jenis_kendaraan, 
                nama_item, tgl_beli, harga_satuan, total_per_item, 
                kategori, nama_barang_asli
              FROM (
                SELECT 
                    rd.id_detail as id_transaksi,
                    m.driver_tetap, 
                    m.plat_nomor, 
                    m.jenis_kendaraan,
                    CONCAT(rd.nama_barang_manual, ' (', rd.jumlah, ' ', rd.satuan, ')') as nama_item,
                    IFNULL(p.tgl_beli_barang, r.tgl_request) as tgl_beli,
                CASE 
                    WHEN IFNULL(p.harga, 0) > 0 THEN p.harga 
                    ELSE IFNULL(mb_ref.harga_barang_stok, 0) 
                END as harga_satuan,
                (rd.jumlah * (
                    CASE 
                        WHEN IFNULL(p.harga, 0) > 0 THEN p.harga 
                        ELSE IFNULL(mb_ref.harga_barang_stok, 0) 
                    END
                )) as total_per_item,
                    'BELI' as kategori,
                    rd.nama_barang_manual as nama_barang_asli
=======
$query_sql = "SELECT 
                id_transaksi, driver_tetap, plat_nomor, jenis_kendaraan, nama_item, tgl_beli, 
                harga_satuan, total_per_item, kategori
              FROM (
                SELECT 
                    rd.id_detail as id_transaksi,
                    m.driver_tetap, m.plat_nomor, m.jenis_kendaraan,
                    CONCAT(rd.nama_barang_manual, ' (', rd.jumlah, ' ', rd.satuan, ')') as nama_item,
                    IFNULL(p.tgl_final, r.tgl_request) as tgl_beli,
                    CASE 
                        WHEN IFNULL(p.harga_final, 0) > 0 THEN p.harga_final 
                        ELSE IFNULL(mb_ref.harga_barang_stok, 0) 
                    END as harga_satuan,
                    (rd.jumlah * (
                        CASE 
                            WHEN IFNULL(p.harga_final, 0) > 0 THEN p.harga_final 
                            ELSE IFNULL(mb_ref.harga_barang_stok, 0) 
                        END
                    )) as total_per_item,
                    'BELI' as kategori
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                FROM master_mobil m
                INNER JOIN tr_request_detail rd ON m.id_mobil = rd.id_mobil
                INNER JOIN tr_request r ON rd.id_request = r.id_request
                LEFT JOIN master_barang mb_ref ON rd.nama_barang_manual = mb_ref.nama_barang
<<<<<<< HEAD
                LEFT JOIN pembelian p ON p.id_request_detail = rd.id_detail
                    AND REPLACE(p.plat_nomor, ' ', '') = REPLACE(m.plat_nomor, ' ', '')
                WHERE (IFNULL(p.tgl_beli_barang, r.tgl_request) BETWEEN '$tgl_awal' AND '$tgl_akhir')
=======
                LEFT JOIN (
                    SELECT id_request, nama_barang_beli, MAX(harga) as harga_final, MAX(tgl_beli_barang) as tgl_final 
                    FROM pembelian GROUP BY id_request, nama_barang_beli
                ) p ON (rd.id_request = p.id_request AND rd.nama_barang_manual = p.nama_barang_beli)
                WHERE (IFNULL(p.tgl_final, r.tgl_request) BETWEEN '$tgl_awal' AND '$tgl_akhir')
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                " . ($search != '' ? " AND (m.driver_tetap LIKE '%$search%' OR m.plat_nomor LIKE '%$search%')" : "") . "

                UNION ALL

                SELECT 
                    b.id_bon as id_transaksi,
<<<<<<< HEAD
                    m.driver_tetap, 
                    m.plat_nomor, 
                    m.jenis_kendaraan,
=======
                    m.driver_tetap, m.plat_nomor, m.jenis_kendaraan,
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                    CONCAT('[STOK] ', mb.nama_barang, ' (', b.qty_keluar, ' ', mb.satuan, ')') as nama_item,
                    DATE(b.tgl_keluar) as tgl_beli, 
                    IFNULL(mb.harga_barang_stok, 0) as harga_satuan,
                    (b.qty_keluar * IFNULL(mb.harga_barang_stok, 0)) as total_per_item,
<<<<<<< HEAD
                    'STOK' as kategori,
                    mb.nama_barang as nama_barang_asli
=======
                    'STOK' as kategori
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                FROM master_mobil m
                INNER JOIN bon_permintaan b ON REPLACE(m.plat_nomor,' ','') = REPLACE(b.plat_nomor,' ','')
                INNER JOIN master_barang mb ON b.id_barang = mb.id_barang
                WHERE (DATE(b.tgl_keluar) BETWEEN '$tgl_awal' AND '$tgl_akhir')
                " . ($search != '' ? " AND (m.driver_tetap LIKE '%$search%' OR m.plat_nomor LIKE '%$search%')" : "") . "
              ) AS gabungan
<<<<<<< HEAD
              ORDER BY driver_tetap ASC, plat_nomor ASC, tgl_beli ASC, kategori DESC";

$result = mysqli_query($koneksi, $query_sql);

$data_by_driver = [];
while ($row = mysqli_fetch_assoc($result)) {
    $driver_key = ($row['driver_tetap'] != '' && $row['driver_tetap'] != null && $row['driver_tetap'] != '-') ? $row['driver_tetap'] : 'TANPA DRIVER';
    $plat_key = $row['plat_nomor'];
    if (!isset($data_by_driver[$driver_key])) { $data_by_driver[$driver_key] = []; }
    if (!isset($data_by_driver[$driver_key][$plat_key])) {
        $data_by_driver[$driver_key][$plat_key] = [
=======
              ORDER BY plat_nomor ASC, tgl_beli ASC, kategori DESC";

$result = mysqli_query($koneksi, $query_sql);

$data_mobil = [];
while ($row = mysqli_fetch_assoc($result)) {
    $key = $row['plat_nomor'];
    if (!isset($data_mobil[$key])) {
        $data_mobil[$key] = [
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
            'driver' => $row['driver_tetap'],
            'jenis'  => $row['jenis_kendaraan'],
            'items'  => []
        ];
    }
<<<<<<< HEAD
    $data_by_driver[$driver_key][$plat_key]['items'][] = $row;
}
uksort($data_by_driver, function($a, $b) {
    if ($a === 'TANPA DRIVER') return 1;
    if ($b === 'TANPA DRIVER') return -1;
    return strcmp($a, $b);
});

// FUNGSI CEK TOTAL PEMBELIAN (Untuk highlight keduanya)
function isDuplicateItem($koneksi, $nama_barang, $plat_nomor) {
    $query = "SELECT COUNT(*) as total FROM (
                SELECT rd.nama_barang_manual as nama FROM tr_request_detail rd
                INNER JOIN master_mobil m ON rd.id_mobil = m.id_mobil
                WHERE rd.nama_barang_manual = '$nama_barang' AND m.plat_nomor = '$plat_nomor'
                UNION ALL
                SELECT mb.nama_barang as nama FROM bon_permintaan b
                INNER JOIN master_barang mb ON b.id_barang = mb.id_barang
                WHERE mb.nama_barang = '$nama_barang' AND b.plat_nomor = '$plat_nomor'
            ) AS gabung";
    $res = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($res);
    return ($data['total'] > 1);
}

// FUNGSI CEK TANGGAL SEBELUMNYA
function getLastPurchaseDate($koneksi, $nama_barang, $tgl_sekarang, $plat_nomor) {
    $query = "SELECT MAX(tgl_beli) as tgl_terakhir FROM (
                SELECT IFNULL(p.tgl_beli_barang, r.tgl_request) as tgl_beli
                FROM tr_request_detail rd
                INNER JOIN tr_request r ON rd.id_request = r.id_request
                LEFT JOIN pembelian p ON rd.id_request = p.id_request AND rd.nama_barang_manual = p.nama_barang_beli
                WHERE rd.nama_barang_manual = '$nama_barang'
                AND rd.id_mobil = (SELECT id_mobil FROM master_mobil WHERE plat_nomor = '$plat_nomor' LIMIT 1)
                AND IFNULL(p.tgl_beli_barang, r.tgl_request) < '$tgl_sekarang'
                UNION ALL
                SELECT DATE(b.tgl_keluar) as tgl_beli
                FROM bon_permintaan b
                INNER JOIN master_barang mb ON b.id_barang = mb.id_barang
                WHERE mb.nama_barang = '$nama_barang'
                AND b.plat_nomor = '$plat_nomor'
                AND DATE(b.tgl_keluar) < '$tgl_sekarang'
            ) AS history";
    $res = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($res);
    return $row['tgl_terakhir'];
=======
    $data_mobil[$key]['items'][] = $row;
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rincian Mobil - MCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
<<<<<<< HEAD
        body { background-color: #f8f9fa; font-family: 'Times New Roman', Times, serif; }
        .table-laporan { width: 100%; border-collapse: collapse !important; background: white; border: 2px solid #000 !important; }
        .table-laporan th, .table-laporan td { border: 1px solid #000 !important; padding: 6px; vertical-align: middle; color: #000 !important; }
        .table-laporan th { background-color: #f2f2f2 !important; text-align: center; font-size: 10pt; }
        .badge-stok { color: #198754; font-weight: bold; border: 1px solid #198754; padding: 1px 4px; border-radius: 3px; font-size: 8pt; }
        .badge-jenis { background-color: #333; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 7pt; text-transform: uppercase; }
        .baris-total { background-color: #343a40 !important; color: #fff !important; }
        .baris-total td { color: #fff !important; }
        .badge-periode { background-color: #000; color: #fff; padding: 1px 5px; border-radius: 3px; font-size: 7pt; font-weight: bold; display: inline-block; margin-top: 2px; }
        .driver-header { background-color: #e9ecef !important; font-weight: bold; font-size: 10pt; }
        
        /* HIGHLIGHT KUNING */
        .highlight-pernah { 
            background-color: #ffff00 !important; 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
=======
        /* ── TAMPILAN LAYAR ── */
        body { background-color: #f8f9fa; font-family: 'Times New Roman', Times, serif; }

        .table-laporan {
            width: 100%;
            border-collapse: collapse !important;
            background: white;
            border: 2px solid #000 !important;
        }
        .table-laporan th,
        .table-laporan td {
            border: 1px solid #000 !important;
            padding: 6px;
            vertical-align: middle;
            color: #000 !important;
        }
        .table-laporan th {
            background-color: #f2f2f2 !important;
            text-align: center;
            font-size: 10pt;
        }

        .header-laporan h4  { font-weight: bold; text-decoration: underline; margin-bottom: 2px; }
        .sub-total          { background-color: #f9f9f9 !important; font-weight: bold; }

        .badge-stok {
            color: #198754; font-weight: bold;
            border: 1px solid #198754;
            padding: 1px 4px; border-radius: 3px; font-size: 8pt;
        }
        .badge-jenis {
            background-color: #333; color: #fff;
            padding: 2px 6px; border-radius: 4px;
            font-size: 7pt; text-transform: uppercase;
            margin-bottom: 3px; display: inline-block;
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        }
        .baris-total { background-color: #343a40 !important; color: #fff !important; font-weight: bold; }
        .baris-total td { color: #fff !important; }

        /* ── CETAK: A4 PORTRAIT ── */
        @media print {
<<<<<<< HEAD
            @page { size: F4; margin: 1cm; }
            body { background-color: white; font-size: 9pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .highlight-pernah { background-color: #ffff00 !important; }
=======
            @page {
                size: A4 portrait;
                margin: 1cm 1.2cm;
            }

            body {
                background-color: white;
                margin: 0; padding: 0;
                font-family: Arial, sans-serif; /* Arial lebih rapi cetak dibanding Times */
                font-size: 8pt;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Sembunyikan elemen non-cetak */
            .no-print { display: none !important; }

            /* Container utama full width */
            .container-fluid { padding: 0 !important; }

            /* ── HEADER ── */
            .header-laporan h4  { font-size: 11pt; }
            .header-laporan p   { font-size: 8pt; }

            /* ── TABEL ── */
            .table-laporan      { font-size: 7.5pt; width: 100%; }
            .table-laporan th   { font-size: 7pt; padding: 3px 4px; }
            .table-laporan td   { font-size: 7.5pt; padding: 3px 4px; }

            /* Info kendaraan dalam sel rowspan */
            .badge-jenis        { font-size: 6pt; padding: 1px 4px; }
            .badge-stok         { font-size: 7pt; }

            /* Nomor plat — tidak perlu 11pt */
            .fw-bold            { font-size: 8pt !important; }

            /* Baris subtotal & total */
            .sub-total td       { font-size: 7pt; padding: 2px 4px; }
            .baris-total td     { font-size: 7.5pt; padding: 3px 4px; }

            /* TTD di bawah laporan */
            .d-none.d-print-flex { display: flex !important; margin-top: 16px; }
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        }
    </style>
</head>
<body>

<div class="container-fluid py-3">
<<<<<<< HEAD
    <div class="card mb-3 no-print shadow-sm border-0">
        <div class="card-body bg-light p-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold">Periode</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
=======

    <!-- Filter (tidak ikut cetak) -->
    <div class="card mb-4 no-print shadow-sm border-0">
        <div class="card-body bg-light">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-secondary text-uppercase">Periode Realisasi</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="tgl_awal"  class="form-control" value="<?= $tgl_awal ?>">
                        <span class="input-group-text">s/d</span>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                        <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                    </div>
                </div>
                <div class="col-md-3">
<<<<<<< HEAD
                    <input type="text" name="search" class="form-control form-control-sm" value="<?= htmlspecialchars($search) ?>" placeholder="Plat/Driver...">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-dark">Cetak</button>
                    <a href="../../index.php" class="btn btn-sm btn-danger">Kembali</a>
=======
                    <label class="small fw-bold text-secondary text-uppercase">Cari Mobil/Driver</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($search) ?>" placeholder="Plat nomor atau nama...">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-filter me-1"></i> Tampilkan
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-dark px-3">
                        <i class="fas fa-print me-1"></i> Cetak 
                    </button>
                    <a href="?" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    <a href="../../index.php" class="btn btn-sm btn-danger px-3">Kembali</a>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                </div>
            </form>
        </div>
    </div>

<<<<<<< HEAD
    <div class="text-center mb-3">
        <h4 class="text-uppercase fw-bold" style="text-decoration: underline;">Laporan Mobil PT. MCP</h4>
        <p>Periode: <?= date('d/m/Y', strtotime($tgl_awal)) ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir)) ?></p>
    </div>

    <table class="table-laporan">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="15%">KENDARAAN / DRIVER</th>
                <th>NAMA BARANG / ITEM</th>
                <th width="8%">TGL BELI</th>
                <th width="10%">TGL TERAKHIR</th>
                <th width="10%">HARGA</th>
                <th width="10%">SUBTOTAL</th>
                <th width="4%" class="no-print">AKSI</th>
=======
    <!-- Header laporan -->
    <div class="text-center mb-3 header-laporan">
        <h4 class="text-uppercase">Laporan Pemeliharaan &amp; Pengeluaran Mobil</h4>
        <p class="mb-0">
            Periode: <b><?= date('d/m/Y', strtotime($tgl_awal)) ?></b>
            s/d <b><?= date('d/m/Y', strtotime($tgl_akhir)) ?></b>
        </p>
    </div>

    <!-- Tabel laporan -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="18%">KENDARAAN / DRIVER</th>
                <th>NAMA BARANG / ITEM PEMELIHARAAN</th>
                <th width="9%">TGL BELI</th>
                <th width="12%">HARGA</th>
                <th width="12%">SUBTOTAL</th>
                <th width="5%" class="no-print">AKSI</th>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
            </tr>
        </thead>
        <tbody>
            <?php
<<<<<<< HEAD
            $no = 0; $grand_total = 0;
            foreach ($data_by_driver as $driver_name => $data_mobil) :
            ?>
                <tr class="driver-header">
                    <td colspan="8" class="ps-3"><i class="fas fa-user me-2"></i> DRIVER: <?= ($driver_name === 'TANPA DRIVER') ? '-' : strtoupper($driver_name) ?></td>
                </tr>
                <?php
                foreach ($data_mobil as $plat => $m) :
                    $sub_total_mobil = 0; $rowspan = count($m['items']);
                    foreach ($m['items'] as $index => $item) :
                        $sub_total_mobil += $item['total_per_item'];
                        $grand_total += $item['total_per_item'];
                        
                        // LOGIKA BARU: Jika barang ini pernah muncul > 1 kali untuk plat ini, beri highlight kuning
                        $is_duplikat = isDuplicateItem($koneksi, $item['nama_barang_asli'], $plat);
                        $class_highlight = ($is_duplikat) ? 'highlight-pernah' : '';
                        
                        // Cari tanggal terakhir (khusus baris yang punya riwayat lama)
                        $tgl_terakhir_db = getLastPurchaseDate($koneksi, $item['nama_barang_asli'], $item['tgl_beli'], $plat);
                        $tgl_tampil = '-'; $badge_periode = '';
                        if ($tgl_terakhir_db) {
                            $tgl_tampil = date('d/m/y', strtotime($tgl_terakhir_db));
                            $diff = (new DateTime($item['tgl_beli']))->diff(new DateTime($tgl_terakhir_db))->days;
                            $badge_periode = '<br><span class="badge-periode">'.$diff.' Hari</span>';
                        }
                ?>
                    <tr class="<?= $class_highlight ?>">
                        <?php if ($index === 0) : ?>
                            <td rowspan="<?= $rowspan ?>" class="text-center fw-bold"><?= ++$no ?></td>
                            <td rowspan="<?= $rowspan ?>" class="align-top">
                                <span class="badge-jenis"><?= $m['jenis'] ?></span><br>
                                <b><?= $plat ?></b><br>
                                <small class="text-muted"><?= $m['driver'] ?></small>
                            </td>
                        <?php endif; ?>
                        <td><?= str_replace('[STOK]', '<span class="badge-stok">STOK</span>', $item['nama_item']) ?></td>
                        <td class="text-center">
                            <a href="javascript:void(0)" class="text-decoration-none no-print" onclick="editTanggal('<?= $item['id_transaksi'] ?>', '<?= $item['kategori'] ?>', '<?= $item['tgl_beli'] ?>')">
                                <?= date('d/m/y', strtotime($item['tgl_beli'])) ?>
                            </a>
                            <span class="d-none d-print-inline"><?= date('d/m/y', strtotime($item['tgl_beli'])) ?></span>
                        </td>
                        <td class="text-center fw-bold"><?= $tgl_tampil . $badge_periode ?></td>
                        <td class="text-end">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">Rp <?= number_format($item['total_per_item'], 0, ',', '.') ?></td>
                        <td class="text-center no-print">
                            <a href="hapus_item_mobil.php?id=<?= $item['id_transaksi'] ?>&kat=<?= $item['kategori'] ?>" class="text-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="bg-light">
                    <td colspan="6" class="text-end small fw-bold">SUBTOTAL <?= $plat ?> :</td>
                    <td colspan="2" class="text-end fw-bold">Rp <?= number_format($sub_total_mobil, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; endforeach; ?>
            <tr class="baris-total">
                <td colspan="6" class="text-end fw-bold">TOTAL KESELURUHAN :</td>
                <td colspan="2" class="text-end fw-bold">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-7">
            <div style="border: 1px solid #000; padding: 5px; font-size: 8pt; display: inline-block; background: #ffff00; -webkit-print-color-adjust: exact;">
                <strong>INFO:</strong> Baris Kuning = Barang/Jasa yang merupakan pengulangan (pernah dibeli sebelumnya).
            </div>
        </div>
        <div class="col-5 text-end d-none d-print-block">
            Surabaya, <?= date('d') ?> <?= $nama_bulan[date('m')] ?> <?= date('Y') ?><br><br><br><br>
            <strong>( ____________________ )</strong><br>Manager
        </div>
    </div>
</div>

<div class="modal fade no-print" id="modalEditTgl" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="update_tgl_laporan.php" method="POST" class="modal-content">
            <div class="modal-header p-2"><h6>Edit Tanggal</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-2">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="kat" id="edit_kat">
                <input type="date" name="tgl_baru" id="edit_tgl" class="form-control form-control-sm">
            </div>
            <div class="modal-footer p-1"><button type="submit" class="btn btn-sm btn-primary w-100">Update</button></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editTanggal(id, kat, tgl) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_kat').value = kat;
    document.getElementById('edit_tgl').value = tgl;
=======
            $no          = 1;
            $grand_total = 0;
            if (!empty($data_mobil)) :
                foreach ($data_mobil as $plat => $m) :
                    $sub_total_mobil = 0;
                    $rowspan         = count($m['items']);
                    foreach ($m['items'] as $index => $item) :
                        $sub_total_mobil += $item['total_per_item'];
                        $grand_total     += $item['total_per_item'];

                        $nama_item_display = $item['nama_item'];
                        if (strpos($nama_item_display, '[STOK]') !== false) {
                            $nama_item_display = str_replace('[STOK]', '<span class="badge-stok">STOK</span>', $nama_item_display);
                        }
            ?>
            <tr>
                <?php if ($index === 0) : ?>
                    <td rowspan="<?= $rowspan + 1 ?>" class="text-center fw-bold"><?= $no++ ?></td>
                    <td rowspan="<?= $rowspan + 1 ?>" class="align-top pt-2">
                        <span class="badge-jenis"><?= ($m['jenis'] ?: 'Unit') ?></span><br>
                        <span class="fw-bold" style="font-size:11pt;"><?= $plat ?></span><br>
                        <small class="text-uppercase text-muted"><?= $m['driver'] ?></small>
                    </td>
                <?php endif; ?>

                <td><?= $nama_item_display ?></td>
                <td class="text-center">
                    <?php
                    $tgl_val     = $item['tgl_beli'];
                    $tgl_display = ($tgl_val != '' && $tgl_val != '0000-00-00')
                        ? date('d/m/y', strtotime($tgl_val)) : '-';
                    ?>
                    <a href="javascript:void(0)"
                       class="text-decoration-none <?= ($tgl_val == '' || $tgl_val == '0000-00-00') ? 'text-danger' : 'text-primary' ?> no-print"
                       onclick="editTanggal('<?= $item['id_transaksi'] ?>', '<?= $item['kategori'] ?>', '<?= $tgl_val ?>')">
                        <?= $tgl_display ?>
                    </a>
                    <span class="d-none d-print-inline"><?= $tgl_display ?></span>
                </td>
                <td class="text-end">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                <td class="text-end fw-bold">Rp <?= number_format($item['total_per_item'], 0, ',', '.') ?></td>
                <td class="text-center no-print">
                    <a href="hapus_item_mobil.php?id=<?= $item['id_transaksi'] ?>&kat=<?= $item['kategori'] ?>"
                       class="text-danger" onclick="return confirm('Hapus item ini?')">
                       <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- Subtotal per mobil -->
            <tr class="sub-total">
                <td colspan="3" class="text-end text-uppercase" style="font-size:8pt;">
                    Subtotal Biaya <?= $plat ?> :
                </td>
                <td colspan="2" class="text-end">Rp <?= number_format($sub_total_mobil, 0, ',', '.') ?></td>
                <td class="no-print"></td>
            </tr>

            <?php endforeach; ?>

            <!-- Grand total -->
            <tr class="baris-total">
                <td colspan="5" class="text-end py-2">TOTAL KESELURUHAN :</td>
                <td class="text-end py-2">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                <td class="no-print"></td>
            </tr>

            <?php else : ?>
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    Belum ada data pemeliharaan untuk periode ini.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TTD (hanya saat cetak) -->
    <div class="row mt-4 d-none d-print-flex">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5">
                Surabaya, <?= date('d') ?> <?= $nama_bulan[date('m')] ?> <?= date('Y') ?>
            </p>
            <p class="fw-bold mb-0">( ____________________ )</p>
            <p>Manager</p>
        </div>
    </div>

</div><!-- /container -->

<!-- Modal edit tanggal (tidak ikut cetak) -->
<div class="modal fade no-print" id="modalEditTgl" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="update_tgl_laporan.php" method="POST" class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Sesuaikan Tanggal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id"  id="edit_id">
                <input type="hidden" name="kat" id="edit_kat">
                <div class="mb-2">
                    <label class="small fw-bold">Tanggal Nota/Beli</label>
                    <input type="date" name="tgl_baru" id="edit_tgl"
                           class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="modal-footer py-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">Update Data</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editTanggal(id, kat, tgl) {
    document.getElementById('edit_id').value  = id;
    document.getElementById('edit_kat').value = kat;
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('edit_tgl').value = (tgl === '' || tgl === '0000-00-00') ? today : tgl;
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    new bootstrap.Modal(document.getElementById('modalEditTgl')).show();
}
</script>
</body>
</html>