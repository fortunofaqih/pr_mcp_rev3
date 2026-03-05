<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

// Proteksi Halaman: Pastikan hanya Manager yang bisa masuk
if ($_SESSION['status'] != "login" || ($_SESSION['role'] != 'manager')) {
    header("location:../../login.php?pesan=bukan_pimpinan");
    exit;
}

$nama_pimpinan = isset($_SESSION['nama']) ? $_SESSION['nama'] : "PIMPINAN";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Approval PR Besar - MCP System</title>
=======
    <title>Antrean Approval - MCP</title>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    <link rel="icon" type="image/png" href="/pr_mcp/assets/img/logo_mcp.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --mcp-blue: #1e3a8a; --mcp-accent: #3b82f6; }
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-mcp { background: var(--mcp-blue); color: white; padding: 15px 0; }
        .card { border-radius: 12px; overflow: hidden; }
        .table thead { background-color: #2d3748; color: white; }
        .status-badge { font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; font-weight: 600; }
        .bg-waiting { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-mcp shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="../../index.php">
            <i class="fas fa-arrow-left me-3"></i> DASHBOARD PIMPINAN
        </a>
        <span class="navbar-text text-white">
            <i class="fas fa-user-tie me-2"></i> Hello, <strong><?= htmlspecialchars($nama_pimpinan) ?></strong>
        </span>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">Daftar Antrean PR (Besar)</h3>
            <p class="text-muted small">Menampilkan pengajuan barang besar/investasi yang menunggu persetujuan Anda.</p>
        </div>
        <span class="badge bg-primary px-3 py-2">Kategori: BARANG BESAR</span>
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3" width="180">No. Request</th>
                            <th width="140">Tanggal</th>
                            <th width="180">Pemesan</th>
                            <th>Keperluan / Tujuan</th>
                            <th width="200">Status</th>
                            <th class="text-center" width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // PERBAIKAN: Query disesuaikan dengan isi database (MENUNGGU APPROVAL)
                        $sql = "SELECT * FROM tr_request 
                                WHERE kategori_pr = 'BESAR' 
                                AND status_approval = 'MENUNGGU APPROVAL' 
                                ORDER BY tgl_request ASC";
                                
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-primary"><?= $data['no_request'] ?></span>
                            </td>
                            <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?= date('d/m/Y', strtotime($data['tgl_request'])) ?></td>
                            <td class="text-uppercase small fw-semibold text-secondary"><?= $data['nama_pemesan'] ?></td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    <?= htmlspecialchars($data['keterangan']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge bg-waiting text-uppercase">
                                    <i class="fas fa-clock me-1"></i> <?= $data['status_approval'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="approval_pimpinan_detail.php?id=<?= $data['id_request'] ?>" class="btn btn-outline-primary btn-sm px-3 fw-bold shadow-sm">
                                    Detail <i class="fas fa-search ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            // PERBAIKAN: Colspan menjadi 6 agar menutup seluruh lebar tabel
                            echo "<tr><td colspan='6' class='text-center py-5'>
                                    <img src='https://cdn-icons-png.flaticon.com/512/7486/7486744.png' width='80' class='opacity-25 mb-3'><br>
                                    <span class='text-muted d-block'>Tidak ada antrean persetujuan saat ini.</span>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-white border rounded shadow-sm">
        <small class="text-muted">
            <i class="fas fa-info-circle text-primary me-2"></i>
            <strong>Catatan:</strong> Klik tombol <strong>Detail</strong> untuk melihat rincian item barang, total biaya, dan data PO sebelum memberikan keputusan Setuju atau Tolak.
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>