<?php
include '../../config/koneksi.php';
include '../../auth/check_session.php';
// 1. Ambil ID dengan aman
$id_bon = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Query: Ambil SEMUA barang dengan no_permintaan yang sama
// Menggunakan subquery untuk menemukan no_permintaan dari id_bon yang dipilih
$sql = "SELECT b.*, m.nama_barang, m.satuan, m.stok_akhir 
        FROM bon_permintaan b 
        LEFT JOIN master_barang m ON b.id_barang = m.id_barang 
        WHERE b.no_permintaan = (SELECT no_permintaan FROM bon_permintaan WHERE id_bon = ?)
        ORDER BY b.id_bon ASC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_bon);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

<<<<<<< HEAD
// Fetch semua items ke dalam array
$data_items = [];
$bon_info = null; // Untuk menyimpan info header (penerima, tgl, dll)

while($row = mysqli_fetch_array($result)) {
    if ($bon_info === null) {
        $bon_info = $row; // Simpan info header dari item pertama
    }
    $data_items[] = $row;
}

mysqli_stmt_close($stmt);

if (!$bon_info || empty($data_items)) {
    echo "<div style='text-align:center; padding:50px; font-family:Arial;'><h3>Data Bon Tidak Ditemukan</h3><a href='javascript:window.close()'>Tutup</a></div>";
=======
if (!$data) {
    echo "<div style='text-align:center; padding:50px;'><h3>Data Tidak Ditemukan</h3></div>";
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Bon - <?= htmlspecialchars($bon_info['no_permintaan']) ?></title>
    <style>
        /* PENGATURAN KERTAS LANDSCAPE (16.5cm x 10.5cm) */
        @page {
            size: 165mm 105mm;
=======
    <title>Bon - <?= $data['no_permintaan'] ?></title>
    <style>
        /* PENGATURAN KERTAS LANDSCAPE (16.5cm x 10.5cm) */
        @page {
            size: 165mm 105mm; /* Width x Height - LANDSCAPE */
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
            margin: 3mm;
            orientation: landscape;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { 
            font-family: 'Arial', sans-serif; 
            background-color: #fff;
            font-size: 8pt;
            width: 165mm;
            height: 105mm;
            color: #000;
        }

        .bon-wrapper {
<<<<<<< HEAD
            width: 159mm;
            height: 99mm;
=======
            width: 159mm;   /* 165mm - margin (3mm x 2) */
            height: 99mm;   /* 105mm - margin (3mm x 2) */
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
            padding: 3mm;
            position: relative;
        }

        /* HEADER */
        .header-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 7pt;
            border: 1px solid #000;
            padding: 2px 5px;
        }
<<<<<<< HEAD
        .header-top-left { font-weight: bold; }
        .header-top-right { font-weight: bold; }
=======
        .header-top-left {
            font-weight: bold;
        }
        .header-top-right {
            font-weight: bold;
        }
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612

        .header-title {
            text-align: center;
            margin: 5px 0;
        }
        .header-title h3 { 
            font-size: 10pt; 
            text-transform: uppercase; 
            margin-bottom: 2px;
            font-weight: 800;
        }
<<<<<<< HEAD
        .header-title .no-bon { font-size: 8pt; }

        /* INFO */
        .info-section { margin-bottom: 5px; font-size: 8pt; }
        .info-row { display: flex; margin-bottom: 2px; }
        .info-label { width: 100px; }
        .info-separator { width: 20px; text-align: center; }
        .info-value { flex: 1; border-bottom: 1px dotted #000; }

        /* TABEL BARANG */
=======
        .header-title .no-bon {
            font-size: 8pt;
        }

        /* INFO */
        .info-section {
            margin-bottom: 5px;
            font-size: 8pt;
        }
        .info-row {
            display: flex;
            margin-bottom: 2px;
        }
        .info-label {
            width: 100px;
        }
        .info-separator {
            width: 20px;
            text-align: center;
        }
        .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
        }

        /* TABEL BARANG - Seperti di gambar */
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 5px;
        }
        .table-items th, .table-items td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
        }
        .table-items th { 
            background: #f0f0f0; 
            font-size: 7.5pt; 
            font-weight: bold;
            text-align: center;
        }
<<<<<<< HEAD
        .table-items td.center { text-align: center; }

        /* TTD */
=======
        .table-items td.center {
            text-align: center;
        }

        /* TTD - Seperti di gambar */
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        .ttd-section {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 7.5pt;
        }
<<<<<<< HEAD
        .ttd-box { text-align: center; width: 30%; }
        .ttd-name { margin-bottom: 30px; }
        .ttd-line { padding-top: 2px; margin-top: 20px; }
=======
        .ttd-box {
            text-align: center;
            width: 30%;
        }
        .ttd-name {
            margin-bottom: 30px;
        }
        .ttd-line {
            
            padding-top: 2px;
            margin-top: 20px;
        }
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612

        @media print {
            .no-print { display: none !important; }
            body { width: 165mm; height: 105mm; }
        }

        .no-print {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            margin-top: 15px;
        }
        .btn-print {
            background: #0000FF;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10pt;
        }
        .info-cetak {
            font-size: 8.5pt;
            color: #d9534f;
            margin-top: 10px;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
        }
    </style>
</head>
<body onload="window.print()">

<div class="bon-wrapper">

    <!-- HEADER TOP -->
    <div class="header-top">
        <div class="header-top-left">MCP/FN /01</div>
<<<<<<< HEAD
        <div class="header-top-right">:01,TGL : <?= date('d/m/Y', strtotime($bon_info['tgl_keluar'])) ?></div>
=======
        <div class="header-top-right">:01,TGL : <?= date('d/m/Y', strtotime($data['tgl_keluar'])) ?></div>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    </div>

    <!-- JUDUL -->
    <div class="header-title">
        <h3>PERMINTAAN BARANG</h3>
<<<<<<< HEAD
        <div class="no-bon">NO: <?= htmlspecialchars($bon_info['no_permintaan']) ?></div>
=======
        <div class="no-bon">NO: <?= $data['no_permintaan'] ?></div>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    </div>

    <!-- INFO NAMA & TANGGAL -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">NAMA</div>
            <div class="info-separator">:</div>
<<<<<<< HEAD
            <div class="info-value"><?= htmlspecialchars(strtoupper($bon_info['penerima'])) ?></div>
=======
            <div class="info-value"><?= strtoupper($data['penerima']) ?></div>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        </div>
        <div class="info-row">
            <div class="info-label">Hari / tanggal</div>
            <div class="info-separator">:</div>
<<<<<<< HEAD
            <div class="info-value"><?= date('l, d/m/Y', strtotime($bon_info['tgl_keluar'])) ?></div>
        </div>
    </div>

    <!-- TABEL BARANG (LOOP MULTI-ITEM) -->
=======
            <div class="info-value"><?= date('l, d/m/Y', strtotime($data['tgl_keluar'])) ?></div>
        </div>
    </div>

    <!-- TABEL BARANG - Sesuai gambar -->
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    <table class="table-items">
        <thead>
            <tr>
                <th width="5%">No.</th>
<<<<<<< HEAD
                <th width="28%">Nama barang</th>
                <th width="10%">Jml</th>
                <th width="10%">Stok</th>
                <!--<th width="12%">Unit</th>-->
                <th>Keperluan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($data_items as $item): 
            ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td><?= htmlspecialchars(strtoupper($item['nama_barang'])) ?></td>
                <td class="center"><strong><?= number_format((float)$item['qty_keluar'], 2) ?></strong></td>
                <td class="center"><strong><?= number_format((float)$item['stok_akhir'], 2) ?></strong></td>
               <!-- <td class="center small"><?= $item['plat_nomor'] ? htmlspecialchars($item['plat_nomor']) : '-' ?></td>-->
                <td><?= htmlspecialchars(strtoupper($item['keperluan'])) ?></td>
            </tr>
            <?php endforeach; ?>
            
            <!-- Baris kosong dinamis (maks 5 baris total) -->
            <?php 
            $sisa_baris = 5 - count($data_items);
            for($i = 0; $i < $sisa_baris; $i++): 
            ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               <!-- <td>&nbsp;</td>-->
                <td>&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TTD -->
=======
                <th width="30%">Nama barang</th>
                <th width="12%">Jumlah</th>
                <th width="12%">Stok Gudang</th>
                <th>Untuk keperluan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td><?= strtoupper($data['nama_barang']) ?></td>
                <td class="center"><strong><?= (float)$data['qty_keluar'] ?></strong></td>
                <td class="center">-</td>
                <td><?= strtoupper($data['keperluan']) ?></td>
            </tr>
            <!-- Baris kosong untuk mengisi -->
            <tr><td class="center">2</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="center">3</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="center">4</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="center">5</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        </tbody>
    </table>

    <!-- TTD - Sesuai gambar -->
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-name">Yang Menyerahkan,</div>
            <div class="ttd-line">( ________________ )</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-name">Yang Menerima,</div>
            <div class="ttd-line">( ________________ )</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-name">Mengetahui,</div>
            <div class="ttd-line">( ________________ )</div>
        </div>
    </div>

</div>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Cetak Sekarang</button>
<<<<<<< HEAD
=======
    
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    <div class="info-cetak">
        <strong>⚠️ PENTING - Pengaturan Cetak LANDSCAPE:</strong><br><br>
        1. Ukuran Kertas: <strong>16.5cm x 10.5cm</strong> (Custom)<br>
        2. Orientation: <strong>Landscape</strong> (Mendatar)<br>
<<<<<<< HEAD
        3. Scale: <strong>100%</strong><br>
        4. Margins: <strong>Minimum / None</strong><br>
=======
        3. Scale: <strong>100%</strong> (Jangan Fit to Page)<br>
        4. Margins: <strong>Minimum / None</strong><br>
        5. Posisi Kertas: Sisi 16.5cm (panjang) di ATAS-BAWAH, sisi 10.5cm di KIRI-KANAN<br>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    </div>
</div>

</body>
</html>