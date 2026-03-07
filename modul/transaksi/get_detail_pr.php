<?php
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if (!isset($_GET['id'])) {
    exit("<div class='p-4 text-center text-danger'>ID tidak ditemukan.</div>");
}

if (!isset($_GET['id'])) {
    exit("<div class='p-4 text-center text-danger'>ID tidak ditemukan.</div>");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// 1. Ambil data header
$query_header = mysqli_query($koneksi, "SELECT * FROM tr_request WHERE id_request = '$id'");
$h = mysqli_fetch_array($query_header);

if (!$h) {
    echo "<div class='p-4 text-center text-danger'>Data tidak ditemukan.</div>";
    exit;
}

// Helper: badge status item
function badge_status_item($status) {
    switch ($status) {
        case 'TERBELI':
            return '<span class="badge bg-success" style="font-size:10px;">TERBELI</span>';
        case 'APPROVED':
            return '<span class="badge bg-primary" style="font-size:10px;">APPROVED</span>';
        case 'MENUNGGU VERIFIKASI':
            return '<span class="badge bg-warning text-dark" style="font-size:10px;">MENUNGGU VERIFIKASI</span>';
        case 'REJECTED':
            return '<span class="badge bg-danger" style="font-size:10px;">REJECTED</span>';
        case 'PENDING':
        default:
            return '<span class="badge bg-secondary" style="font-size:10px;">PENDING</span>';
    }
}
?>

<div class="p-3 bg-light border-bottom">
    <div class="row small fw-bold text-uppercase">
        <div class="col-md-4">
            <span class="text-muted d-block" style="font-size: 10px;">No. Request:</span>
            <span class="text-primary" style="font-size: 14px;"><?= $h['no_request'] ?></span>
        </div>
        <div class="col-md-4 text-center border-start border-end">
            <span class="text-muted d-block" style="font-size: 10px;">Pemesan:</span>
            <span><?= strtoupper($h['nama_pemesan']) ?></span>
        </div>
        <div class="col-md-4 text-end">
            <span class="text-muted d-block" style="font-size: 10px;">Tanggal:</span>
            <span><?= date('d/m/Y', strtotime($h['tgl_request'])) ?></span>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover mb-0" style="font-size: 0.8rem;">
        <thead class="table-dark text-uppercase" style="font-size: 0.7rem;">
            <tr>
                <th class="text-center" width="40">NO</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Unit/Mobil</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Qty</th>
                <th>Keterangan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            // Hitung ringkasan status
            $summary = [
                'PENDING'              => 0,
                'APPROVED'             => 0,
                'MENUNGGU VERIFIKASI'  => 0,
                'REJECTED'             => 0,
                'TERBELI'              => 0,
            ];

            $sql_detail = "SELECT d.*, m.plat_nomor, b.nama_barang as nama_barang_master
                           FROM tr_request_detail d
                           LEFT JOIN master_mobil m ON d.id_mobil = m.id_mobil
                           LEFT JOIN master_barang b ON d.id_barang = b.id_barang
                           WHERE d.id_request = '$id' 
                           ORDER BY d.id_detail ASC";

            $query_detail = mysqli_query($koneksi, $sql_detail);

            if (mysqli_num_rows($query_detail) == 0) {
                echo '<tr><td colspan="8" class="text-center py-3">Tidak ada detail item.</td></tr>';
            }

            // Tampung hasil agar bisa dipakai untuk summary
            $rows = [];
            while ($d = mysqli_fetch_array($query_detail)) {
                $rows[] = $d;
                $status_key = $d['status_item'] ?? 'PENDING';
                if (array_key_exists($status_key, $summary)) {
                    $summary[$status_key]++;
                }
            }

            foreach ($rows as $d):
                $nama_tampil = !empty($d['nama_barang_master']) ? $d['nama_barang_master'] : $d['nama_barang_manual'];
                $unit_tampil = (!empty($d['plat_nomor'])) ? $d['plat_nomor'] : "-";
                $status_item = $d['status_item'] ?? 'PENDING';

                // Warna baris berdasarkan status
                $row_class = '';
                switch ($status_item) {
                    case 'TERBELI':            $row_class = 'table-success'; break;
                    case 'APPROVED':           $row_class = 'table-primary'; break;
                    case 'MENUNGGU VERIFIKASI':$row_class = 'table-warning'; break;
                    case 'REJECTED':           $row_class = 'table-danger';  break;
                    default:                   $row_class = '';              break;
                }
            ?>
            <tr class="<?= $row_class ?>">
                <td class="text-center text-muted"><?= $no++ ?></td>
                <td class="fw-bold text-dark"><?= strtoupper($nama_tampil) ?></td>
                <td><small><?= strtoupper($d['kategori_barang']) ?></small></td>
                <td class="text-center">
                    <?php if ($unit_tampil != "-"): ?>
                        <span class="badge bg-light text-dark border"><?= $unit_tampil ?></span>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <span class="badge <?= $d['tipe_request'] == 'LANGSUNG' ? 'bg-outline-danger text-danger' : 'bg-outline-primary text-primary' ?> border" style="font-size: 10px;">
                        <?= $d['tipe_request'] ?>
                    </span>
                </td>
                <td class="text-center fw-bold">
                    <?= (float)$d['jumlah'] ?> <small class="text-muted"><?= $d['satuan'] ?></small>
                </td>
                <td><small><?= $d['keterangan'] ?: '-' ?></small></td>
                <td class="text-center"><?= badge_status_item($status_item) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Ringkasan Status Item -->
<div class="p-3 bg-white border-top">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <small class="text-muted fw-bold me-1">Ringkasan Status Item:</small>
        <?php if ($summary['TERBELI'] > 0): ?>
            <span class="badge bg-success"><?= $summary['TERBELI'] ?> Terbeli</span>
        <?php endif; ?>
        <?php if ($summary['APPROVED'] > 0): ?>
            <span class="badge bg-primary"><?= $summary['APPROVED'] ?> Approved</span>
        <?php endif; ?>
        <?php if ($summary['MENUNGGU VERIFIKASI'] > 0): ?>
            <span class="badge bg-warning text-dark"><?= $summary['MENUNGGU VERIFIKASI'] ?> Menunggu Verifikasi</span>
        <?php endif; ?>
        <?php if ($summary['PENDING'] > 0): ?>
            <span class="badge bg-secondary"><?= $summary['PENDING'] ?> Pending</span>
        <?php endif; ?>
        <?php if ($summary['REJECTED'] > 0): ?>
            <span class="badge bg-danger"><?= $summary['REJECTED'] ?> Rejected</span>
        <?php endif; ?>
    </div>
    <div class="mt-2 text-end">
        <small class="text-muted fst-italic">* Tampilan ini adalah ringkasan item Purchase Request tanpa menampilkan estimasi harga.</small>
    </div>
</div>