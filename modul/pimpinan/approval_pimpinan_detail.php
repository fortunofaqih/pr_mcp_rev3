<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

// Proteksi Halaman
if ($_SESSION['status'] != "login" || $_SESSION['role'] != 'manager') {
    header("location:../../login.php?pesan=bukan_pimpinan");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("location:approval_pimpinan.php"); exit; }

// Ambil data PR - Gunakan JOIN jika ingin ambil nama pimpinan yang approve sekalian
$query_pr = mysqli_query($koneksi, "SELECT * FROM tr_request WHERE id_request='$id'");
$pr = mysqli_fetch_array($query_pr);

if (!$pr) { 
    header("location:approval_pimpinan.php?pesan=data_tidak_ditemukan"); 
    exit; 
}

// Ambil detail item dengan alias yang lebih bersih
$details = mysqli_query($koneksi, "SELECT d.*, b.nama_barang as nama_master, m.plat_nomor
    FROM tr_request_detail d
    LEFT JOIN master_barang b ON d.id_barang = b.id_barang
    LEFT JOIN master_mobil m ON d.id_mobil = m.id_mobil
    WHERE d.id_request = '$id'
    ORDER BY d.id_detail ASC");

// Hitung Grand Total (Gunakan Type Cast ke float agar aman untuk number_format)
$total_est = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(subtotal_estimasi) as total FROM tr_request_detail WHERE id_request='$id'"));
$grand_total = (float)($total_est['total'] ?? 0);

// Ambil data PO draft dengan penanganan null yang lebih baik
$po_query = mysqli_query($koneksi, "SELECT p.*, s.nama_supplier, s.alamat, s.kota, s.telp, s.atas_nama 
    FROM tr_purchase_order p 
    LEFT JOIN master_supplier s ON p.id_supplier = s.id_supplier 
    WHERE p.id_request = '$id' LIMIT 1");
$po = mysqli_fetch_array($po_query);

// Cek status dengan string yang konsisten
$sudah_diproses = in_array($pr['status_approval'], ['DISETUJUI', 'DITOLAK']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Review PR <?= $pr['no_request'] ?> - MCP</title>
=======
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pengajuan - MCP</title>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    <link rel="icon" type="image/png" href="/pr_mcp/assets/img/logo_mcp.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --mcp-blue: #0000FF; }
        body { background-color: #f8f9fa; font-size: 0.875rem; }
        .navbar-mcp { background: var(--mcp-blue); }

        /* INFO HEADER */
        .info-header { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 20px 24px; margin-bottom: 20px; }
        .info-label  { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px; }
        .info-value  { font-size: 0.9rem; font-weight: 600; color: #1a1a2e; }

        /* STATUS BANNER */
        .banner-pending  { background: #fff8e1; border: 1px solid #ffc107; border-radius: 10px; padding: 12px 18px; }
        .banner-approved { background: #d1e7dd; border: 1px solid #198754; border-radius: 10px; padding: 12px 18px; }
        .banner-rejected { background: #f8d7da; border: 1px solid #dc3545; border-radius: 10px; padding: 12px 18px; }

        /* TABLE DETAIL */
        .table-detail thead { background: #1a1a2e; color: white; font-size: 0.75rem; text-transform: uppercase; }
        .table-detail tbody { font-size: 0.82rem; }
        .table-detail tfoot { background: #f1f4f9; font-weight: bold; font-size: 0.85rem; }

        /* TOMBOL AKSI */
        .btn-approve { background: #198754; color: white; border: none; padding: 10px 32px; font-size: 0.9rem; font-weight: 700; border-radius: 8px; }
        .btn-approve:hover { background: #157347; color: white; }
        .btn-reject  { background: #dc3545; color: white; border: none; padding: 10px 32px; font-size: 0.9rem; font-weight: 700; border-radius: 8px; }
        .btn-reject:hover  { background: #b02a37; color: white; }

        .action-box { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 24px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark navbar-mcp shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="approval_pimpinan.php">
            <i class="fas fa-rotate-left me-2"></i> KEMBALI KE ANTREAN
        </a>
        <span class="navbar-text text-white">
            <i class="fas fa-user-tie me-1"></i> Manager: <strong><?= strtoupper($_SESSION['nama'] ?? $_SESSION['username']) ?></strong>
        </span>
    </div>
</nav>

<div class="container-fluid px-4">

    <!-- STATUS BANNER -->
    <?php if ($pr['status_approval'] === 'MENUNGGU APPROVAL'): ?>
    <div class="banner-pending mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-clock text-warning fs-5"></i>
        <span><strong>Menunggu Review Anda.</strong> PR ini belum mendapat keputusan.</span>
    </div>
    <?php elseif ($pr['status_approval'] === 'DISETUJUI'): ?>
    <div class="banner-approved mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-check-circle text-success fs-5"></i>
        <span><strong>PR ini telah DISETUJUI</strong> oleh <?= $pr['approve_by'] ?> pada <?= date('d/m/Y H:i', strtotime($pr['tgl_approval'])) ?></span>
    </div>
    <?php elseif ($pr['status_approval'] === 'DITOLAK'): ?>
    <div class="banner-rejected mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-times-circle text-danger fs-5"></i>
        <span><strong>PR ini telah DITOLAK</strong> oleh <?= $pr['approve_by'] ?> pada <?= date('d/m/Y H:i', strtotime($pr['tgl_approval'])) ?>.
        Catatan: <em><?= $pr['catatan_pimpinan'] ?: '-' ?></em></span>
    </div>
    <?php endif; ?>

    <!-- INFO HEADER PR -->
    <div class="info-header">
        <div class="row g-3">
            <div class="col-6 col-md-2">
                <div class="info-label">No. Request</div>
                <div class="info-value text-primary"><?= $pr['no_request'] ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-label">Tanggal</div>
                <div class="info-value"><?= date('d/m/Y', strtotime($pr['tgl_request'])) ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-label">Dibuat Oleh</div>
                <div class="info-value"><?= $pr['nama_pemesan'] ?></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-label">Petugas Pembelian</div>
                <div class="info-value"><?= $pr['nama_pembeli'] ?: '-' ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Keperluan / Tujuan Pembelian</div>
                <div class="info-value"><?= $pr['keterangan'] ?: '-' ?></div>
            </div>
        </div>
    </div>

    <!-- TABEL DETAIL ITEM -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius:12px;">
        <div class="card-header bg-white fw-bold py-3" style="border-radius:12px 12px 0 0;">
            <i class="fas fa-list me-2 text-primary"></i> Detail Item Barang
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 table-detail">
                    <thead>
                        <tr class="text-center">
                            <th width="4%">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Kwalifikasi</th>
                            <th>Unit/Mobil</th>
                            <th>Tipe</th>
                            <th class="text-center">Qty</th>
                            <th>Satuan</th>
                            <th class="text-end">Harga Est.</th>
                            <th class="text-end">Subtotal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    mysqli_data_seek($details, 0);
                    while ($d = mysqli_fetch_array($details)):
                        $nama = !empty($d['nama_master']) ? $d['nama_master'] : $d['nama_barang_manual'];
                        $unit = !empty($d['plat_nomor']) ? $d['plat_nomor'] : '-';
                    ?>
                    <tr>
                        <td class="text-center text-muted"><?= $no++ ?></td>
                        <td class="fw-bold"><?= strtoupper($nama) ?></td>
                        <td><small><?= $d['kategori_barang'] ?></small></td>
                        <td><small><?= $d['kwalifikasi'] ?: '-' ?></small></td>
                        <td class="text-center">
                            <?php if ($unit !== '-'): ?>
                                <span class="badge bg-light text-dark border"><?= $unit ?></span>
                            <?php else: echo '-'; endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $d['tipe_request'] === 'LANGSUNG' ? 'bg-danger' : 'bg-primary' ?>" style="font-size:0.7rem;">
                                <?= $d['tipe_request'] ?>
                            </span>
                        </td>
                        <td class="text-center fw-bold"><?= (float)$d['jumlah'] + 0 ?></td>
                        <td class="text-center"><small><?= $d['satuan'] ?></small></td>
                        <td class="text-end">Rp <?= number_format((float)$d['harga_satuan_estimasi'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">Rp <?= number_format((float)$d['subtotal_estimasi'], 0, ',', '.') ?></td>
                        <td><small><?= $d['keterangan'] ?: '-' ?></small></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="text-end pe-3">TOTAL ESTIMASI</td>
                            <td class="text-end text-danger fw-bold">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    <!-- PREVIEW PO DRAFT -->
    <?php if ($po): ?>
    <div class="card shadow-sm border-0 mb-4" style="border-radius:12px; border:1px solid #c7d2fe !important;">
        <div class="card-header fw-bold py-3" style="background:#f0f4ff; border-radius:12px 12px 0 0; color:#1e3a8a;">
            <i class="fas fa-file-invoice me-2 text-primary"></i> Preview Purchase Order (PO)
            <span class="badge bg-warning text-dark ms-2 fw-normal" style="font-size:0.72rem;">DRAFT — Menunggu Approval</span>
            <span class="float-end fw-normal" style="font-size:0.8rem; color:#4b5563;">No. PO: <strong><?= $po["no_po"] ?></strong></span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="info-label">Supplier / Vendor</div>
                    <div class="info-value text-primary"><?= strtoupper($po["nama_supplier"] ?? "-") ?></div>
                    <small class="text-muted"><?= ($po["alamat"] ?? "") . ($po["kota"] ? ", ".$po["kota"] : "") ?></small>
                </div>
                <div class="col-md-2">
                    <div class="info-label">Tanggal PO</div>
                    <div class="info-value"><?= date("d/m/Y", strtotime($po["tgl_po"])) ?></div>
                </div>
                <div class="col-md-2">
                    <div class="info-label">U/P</div>
                    <div class="info-value"><?= $po["atas_nama"] ?: "-" ?></div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Disiapkan Oleh</div>
                    <div class="info-value"><?= $po["prepared_by"] ?></div>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded text-center">
                        <div style="font-size:0.7rem;color:#6c757d;text-transform:uppercase;font-weight:700;">Subtotal</div>
                        <div style="font-size:1rem;font-weight:700;">Rp <?= number_format($po["subtotal"],0,",",".") ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded text-center">
                        <div style="font-size:0.7rem;color:#6c757d;text-transform:uppercase;font-weight:700;">Diskon</div>
                        <div style="font-size:1rem;font-weight:700;color:#dc3545;">(Rp <?= number_format($po["diskon"],0,",",".") ?>)</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded text-center">
                        <div style="font-size:0.7rem;color:#6c757d;text-transform:uppercase;font-weight:700;">PPN <?= $po["ppn_persen"] ?>%</div>
                        <div style="font-size:1rem;font-weight:700;">Rp <?= number_format($po["ppn_nominal"],0,",",".") ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded text-center text-white" style="background:#1e3a8a;">
                        <div style="font-size:0.7rem;opacity:0.8;text-transform:uppercase;font-weight:700;">Grand Total</div>
                        <div style="font-size:1.1rem;font-weight:800;">Rp <?= number_format($po["grand_total"],0,",",".") ?></div>
                    </div>
                </div>
            </div>
            <?php if ($po["catatan"]): ?>
            <div class="mt-3 p-3 bg-light rounded" style="font-size:0.8rem;">
                <strong>Catatan / Ketentuan:</strong><br>
                <?= nl2br(htmlspecialchars($po["catatan"])) ?>
            </div>
            <?php endif; ?>
            <?php if ($pr["status_approval"] === "DISETUJUI"): ?>
            <div class="mt-3">
                <a href="cetak_po.php?id_po=<?= $po["id_po"] ?>" target="_blank" class="btn btn-primary btn-sm fw-bold">
                    <i class="fas fa-file-invoice me-1"></i> Lihat &amp; Cetak PO
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- TOMBOL AKSI APPROVE / REJECT -->
    <?php if (!$sudah_diproses): ?>
    <div class="action-box mb-4">
        <h6 class="fw-bold mb-1">Keputusan Anda</h6>
        <p class="text-muted small mb-3">Tinjau detail barang di atas sebelum memberikan keputusan.</p>
        <div class="d-flex gap-3 flex-wrap">
            <button class="btn-approve" onclick="approvePR(<?= $id ?>, '<?= $pr['no_request'] ?>')">
                <i class="fas fa-check me-2"></i> SETUJUI PR INI
            </button>
            <button class="btn-reject" onclick="rejectPR(<?= $id ?>, '<?= $pr['no_request'] ?>')">
                <i class="fas fa-times me-2"></i> TOLAK PR INI
            </button>
        </div>
    </div>
    <?php else: ?>
    <div class="action-box mb-4 text-center text-muted">
        <i class="fas fa-lock me-2"></i> PR ini sudah diproses dan tidak dapat diubah kembali.
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function approvePR(id, no) {
    Swal.fire({
        title: 'Setujui PR ' + no + '?',
        html: 'PR akan berstatus <b>DISETUJUI</b> dan tim pembelian dapat generate PO.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: '<i class="fas fa-check me-1"></i> Ya, Setujui!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => Swal.showLoading() });
            window.location.href = 'proses_approval_besar.php?action=approve&id=' + id + '&redirect=pimpinan';
        }
    });
}

function rejectPR(id, no) {
    Swal.fire({
        title: 'Tolak PR ' + no + '?',
        input: 'textarea',
        inputLabel: 'Alasan penolakan (wajib diisi)',
        inputPlaceholder: 'Contoh: Anggaran belum tersedia, mohon ajukan kembali bulan depan...',
        inputValidator: v => { if (!v) return 'Alasan penolakan wajib diisi!'; },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '<i class="fas fa-times me-1"></i> Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => Swal.showLoading() });
            window.location.href = 'proses_approval_besar.php?action=reject&id=' + id + '&catatan=' + encodeURIComponent(r.value) + '&redirect=pimpinan';
        }
    });
}
</script>
</body>
</html>
