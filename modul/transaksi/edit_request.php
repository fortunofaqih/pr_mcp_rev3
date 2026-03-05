<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if ($_SESSION['status'] != "login") {
    header("location:../../login.php?pesan=belum_login");
    exit;
}

<<<<<<< HEAD
=======
// Ambil ID dan data Header
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query_h = mysqli_query($koneksi, "SELECT * FROM tr_request WHERE id_request = '$id'");
$h = mysqli_fetch_array($query_h);

if (!$h) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='pr.php';</script>";
    exit;
}
<<<<<<< HEAD
=======

if ($h['status_request'] != 'PENDING') {
    echo "<script>alert('Data sudah diproses, tidak bisa diedit!'); window.location='pr.php';</script>";
    exit;
}

$nama_user_login = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : "USER";
?>
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612

if (in_array($h['status_request'], ['SELESAI', 'BATAL'])) {
    echo "<script>alert('Data sudah selesai/dibatalkan, tidak bisa diedit!'); window.location='pr.php';</script>";
    exit;
}

if ($h['status_request'] == 'PROSES') {
    $cek_pending = mysqli_num_rows(mysqli_query($koneksi,
        "SELECT id_detail FROM tr_request_detail 
         WHERE id_request = '$id' AND status_item = 'PENDING'"
    ));
    if ($cek_pending == 0) {
        echo "<script>alert('Semua item sudah diproses, tidak ada yang bisa diedit!'); window.location='pr.php';</script>";
        exit;
    }
}

$nama_user_login = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : "USER";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request - <?= $h['no_request'] ?></title>
    <link rel="icon" type="image/png" href="/pr_mcp/assets/img/logo_mcp.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --mcp-blue: #0000FF; }
        body { background-color: #f4f7f6; font-size: 0.85rem; }
        .card-header { background: white; border-bottom: 2px solid #eee; }
        .table-input thead { background: var(--mcp-blue); color: white; font-size: 0.75rem; text-transform: uppercase; }
        .table-responsive { border-radius: 8px; overflow-x: auto; }
        .table-input { min-width: 1000px; table-layout: fixed; }
<<<<<<< HEAD
=======
        
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        .col-brg { width: 220px; }
        .col-kat { width: 140px; }
        .col-mbl { width: 130px; }
        .col-tip { width: 100px; }
        .col-qty { width: 80px; }
        .col-sat { width: 110px; }
        .col-ket { width: 350px; }
<<<<<<< HEAD
        .col-aks { width: 80px; }
        input, select, textarea { text-transform: uppercase; font-size: 0.8rem !important; }
        .info-audit { font-size: 0.75rem; color: #6c757d; background: #eee; padding: 5px 10px; border-radius: 5px; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 31px !important; padding: 2px 5px !important; }
        /* Row locked styling */
        .row-locked { background-color: #f8f9fa; }
        .row-locked select, .row-locked input, .row-locked textarea { 
            pointer-events: none; background-color: #e9ecef !important; color: #6c757d;
=======
        .col-aks { width: 50px; }

        input, select, textarea { text-transform: uppercase; font-size: 0.8rem !important; }
        .info-audit { font-size: 0.75rem; color: #6c757d; background: #eee; padding: 5px 10px; border-radius: 5px; }
        
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 31px !important;
            padding: 2px 5px !important;
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
        }
    </style>
</head>

<body class="py-4">
<div class="container-fluid">
    <form action="proses_edit_request.php" method="POST">
        <input type="hidden" name="id_request" value="<?= $h['id_request'] ?>">
<<<<<<< HEAD

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 text-primary">
                            <i class="fas fa-edit me-2"></i> EDIT PURCHASE REQUEST
                        </h5>
                        <div class="info-audit">
                            <i class="fas fa-user me-1"></i> Dibuat oleh: <strong><?= $h['created_by'] ?></strong>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">NOMOR REQUEST</label>
                                <input type="text" class="form-control bg-light fw-bold" value="<?= $h['no_request'] ?>" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">TANGGAL REQUEST</label>
                                <input type="date" name="tgl_request" class="form-control" value="<?= $h['tgl_request'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">ADMIN BAUT (PEMBUAT)</label>
                                <input type="text" name="nama_pemesan" class="form-control bg-light" value="<?= $nama_user_login ?>" readonly required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">PETUGAS PEMBELIAN</label>
                                <select name="nama_pembeli" class="form-select select-pembeli" required>
                                    <option value="">-- PILIH PEMBELI --</option>
                                    <?php
                                    $user_beli = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE status_aktif='AKTIF' AND (role='bagian_pembelian' OR bagian='Pembelian') ORDER BY nama_lengkap ASC");
                                    while ($u = mysqli_fetch_array($user_beli)) {
                                        $val_u = strtoupper($u['nama_lengkap']);
                                        $selected = ($h['nama_pembeli'] == $val_u) ? 'selected' : '';
                                        echo "<option value='$val_u' $selected>$val_u</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

=======
        
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 text-primary"><i class="fas fa-edit me-2"></i> EDIT PURCHASE REQUEST</h5>
                        <div class="info-audit">
                            <i class="fas fa-user me-1"></i> Dibuat oleh: <strong><?= $h['created_by'] ?></strong> 
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">NOMOR REQUEST</label>
                                <input type="text" class="form-control bg-light fw-bold" value="<?= $h['no_request'] ?>" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">TANGGAL REQUEST</label>
                                <input type="date" name="tgl_request" class="form-control" value="<?= $h['tgl_request'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">ADMIN BAUT (PEMBUAT)</label>
                                <input type="text" name="nama_pemesan" class="form-control bg-light" value="<?= $nama_user_login ?>" readonly required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">PETUGAS PEMBELIAN</label>
                                <select name="nama_pembeli" class="form-select select-pembeli" required>
                                    <option value="">-- PILIH PEMBELI --</option>
                                    <?php
                                    $user_beli = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE status_aktif='AKTIF' AND (role='bagian_pembelian' OR bagian='Pembelian') ORDER BY nama_lengkap ASC");
                                    while($u = mysqli_fetch_array($user_beli)){
                                        $val_u = strtoupper($u['nama_lengkap']);
                                        $selected = ($h['nama_pembeli'] == $val_u) ? 'selected' : '';
                                        echo "<option value='$val_u' $selected>$val_u</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-input align-middle" id="tableItem">
                                <thead>
                                    <tr class="text-center">
                                        <th class="col-brg">Nama Barang</th>
                                        <th class="col-kat">Kategori</th>
                                        <th class="col-mbl">Unit/Mobil</th>
                                        <th class="col-tip">Tipe</th>
                                        <th class="col-qty">Qty</th>
                                        <th class="col-sat">Satuan</th>
                                        <th class="col-ket">Keperluan / Ket. nama driver jika beda</th>
<<<<<<< HEAD
                                        <th class="col-aks">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Tampilkan PENDING dulu, lalu yang locked
                                $query_d = mysqli_query($koneksi, "SELECT * FROM tr_request_detail WHERE id_request = '$id' ORDER BY FIELD(status_item, 'PENDING', 'APPROVED', 'REJECTED', 'MENUNGGU VERIFIKASI', 'TERBELI') ASC");
                                while ($d = mysqli_fetch_array($query_d)) {
                                    $is_locked    = in_array($d['status_item'], ['TERBELI', 'MENUNGGU VERIFIKASI']);
                                    $status_label = $d['status_item'];
                                    $row_class    = $is_locked ? 'item-row row-locked' : 'item-row';
                                ?>
                                <tr class="<?= $row_class ?>">
                                    
                                    <input type="hidden" name="id_detail[]" class="input-id-detail" value="<?= $d['id_detail'] ?>">
                                    

                                    <td>
                                        <select name="id_barang[]" class="form-select form-select-sm select-barang" <?= $is_locked ? 'disabled' : '' ?> required>
                                            <option value="">-- PILIH BARANG --</option>
                                            <?php
                                            $brg = mysqli_query($koneksi, "SELECT * FROM master_barang WHERE status_aktif='AKTIF' ORDER BY nama_barang ASC");
                                            while ($b = mysqli_fetch_array($brg)) {
                                                $sel_b = ($b['id_barang'] == $d['id_barang']) ? 'selected' : '';
                                                echo "<option value='" . $b['id_barang'] . "'
                                                        data-nama='" . strtoupper($b['nama_barang']) . "'
                                                        data-satuan='" . strtoupper($b['satuan']) . "'
                                                        data-merk='" . strtoupper($b['merk']) . "'
                                                        data-kategori='" . strtoupper($b['kategori']) . "'
                                                        data-harga='" . $b['harga_beli'] . "' $sel_b>" . $b['nama_barang'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" name="nama_barang_manual[]" class="input-nama-barang" value="<?= $d['nama_barang_manual'] ?>">
                                        <?php if ($is_locked): ?>
                                            <!-- Kirim nilai id_barang tetap meski disabled -->
                                            <input type="hidden" name="id_barang_locked[]" value="<?= $d['id_barang'] ?>">
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <select name="kategori_request[]" class="form-select form-select-sm select-kategori" <?= $is_locked ? 'disabled' : '' ?> required>
                                            <option value="">- PILIH -</option>
                                            <optgroup label="BENGKEL">
                                                <option value="BENGKEL MOBIL"    <?= $d['kategori_barang'] == 'BENGKEL MOBIL'    ? 'selected' : '' ?>>BENGKEL MOBIL</option>
                                                <option value="BENGKEL LISTRIK"  <?= $d['kategori_barang'] == 'BENGKEL LISTRIK'  ? 'selected' : '' ?>>BENGKEL LISTRIK</option>
                                                <option value="BENGKEL DINAMO"   <?= $d['kategori_barang'] == 'BENGKEL DINAMO'   ? 'selected' : '' ?>>BENGKEL DINAMO</option>
                                                <option value="BENGKEL BUBUT"    <?= $d['kategori_barang'] == 'BENGKEL BUBUT'    ? 'selected' : '' ?>>BENGKEL BUBUT</option>
                                                <option value="MESIN"            <?= $d['kategori_barang'] == 'MESIN'            ? 'selected' : '' ?>>MESIN</option>
                                                <option value="LAS"              <?= $d['kategori_barang'] == 'LAS'              ? 'selected' : '' ?>>LAS</option>
                                            </optgroup>
                                            <optgroup label="UMUM">
                                                <option value="KANTOR"           <?= $d['kategori_barang'] == 'KANTOR'           ? 'selected' : '' ?>>KANTOR</option>
                                                <option value="BANGUNAN"         <?= $d['kategori_barang'] == 'BANGUNAN'         ? 'selected' : '' ?>>BANGUNAN</option>
                                                <option value="UMUM"             <?= $d['kategori_barang'] == 'UMUM'             ? 'selected' : '' ?>>UMUM</option>
                                            </optgroup>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="id_mobil[]" class="form-select form-select-sm select-mobil" <?= $is_locked ? 'disabled' : '' ?>>
                                            <option value="0">NON MOBIL</option>
                                            <?php
                                            $mbl = mysqli_query($koneksi, "SELECT id_mobil, plat_nomor FROM master_mobil WHERE status_aktif='AKTIF' ORDER BY plat_nomor ASC");
                                            while ($m = mysqli_fetch_array($mbl)) {
                                                $sel_m = ($m['id_mobil'] == $d['id_mobil']) ? 'selected' : '';
                                                echo "<option value='" . $m['id_mobil'] . "' $sel_m>" . $m['plat_nomor'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="tipe_request[]" class="form-select form-select-sm select-tipe" <?= $is_locked ? 'disabled' : '' ?>>
                                            <option value="STOK"     <?= $d['tipe_request'] == 'STOK'     ? 'selected' : '' ?>>STOK</option>
                                            <option value="LANGSUNG" <?= $d['tipe_request'] == 'LANGSUNG' ? 'selected' : '' ?>>LANGSUNG</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control form-control-sm input-qty text-center"
                                            step="0.01" value="<?= (float)$d['jumlah'] ?>"
                                            <?= $is_locked ? 'readonly' : '' ?> required>
                                    </td>

                                    <td>
                                        <select name="satuan[]" class="form-select form-select-sm select-satuan" <?= $is_locked ? 'disabled' : '' ?> required>
                                            <option value="">- PILIH -</option>
                                            <?php
                                            $sats = ["PCS","DUS","KG","ONS","LITER","METER","CM","LONJOR","SET","ROLL","PACK","UNIT","DRUM","SAK","PAIL","CAN","BOTOL","TUBE","GALON","IKAT","LEMBAR","TABUNG","KALENG","BATANG","KOTAK","COLT","JURIGEN"];
                                            foreach ($sats as $s) {
                                                $sel_s = ($d['satuan'] == $s) ? 'selected' : '';
                                                echo "<option value='$s' $sel_s>$s</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <input type="hidden" name="kwalifikasi[]" class="input-kwalifikasi" value="<?= $d['kwalifikasi'] ?>">
                                    <input type="hidden" name="harga[]"       class="input-harga"       value="<?= $d['harga_satuan_estimasi'] ?>">

                                    <td>
                                        <textarea name="keterangan[]" class="form-control form-control-sm" rows="1"
                                            <?= $is_locked ? 'readonly' : '' ?>><?= $d['keterangan'] ?></textarea>
                                    </td>

                                    <td class="text-center">
                                        <?php if (!$is_locked): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif ($status_label == 'TERBELI'): ?>
                                            <span class="badge bg-success" style="font-size:0.65rem;">TERBELI</span>
                                        <?php elseif ($status_label == 'MENUNGGU VERIFIKASI'): ?>
                                            <span class="badge bg-warning text-dark" style="font-size:0.65rem; white-space:normal;">
                                                MENUNGGU<br>VERIFIKASI
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

=======
                                        <th class="col-aks"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_d = mysqli_query($koneksi, "SELECT * FROM tr_request_detail WHERE id_request = '$id'");
                                    while($d = mysqli_fetch_array($query_d)) {
                                    ?>
                                    <tr class="item-row">
                                        <td>
                                            <select name="id_barang[]" class="form-select form-select-sm select-barang" required>
                                                <option value="">-- PILIH BARANG --</option>
                                                <?php
                                                $brg = mysqli_query($koneksi, "SELECT * FROM master_barang WHERE status_aktif='AKTIF' ORDER BY nama_barang ASC");
                                                while($b = mysqli_fetch_array($brg)){
                                                    $sel_b = ($b['id_barang'] == $d['id_barang']) ? 'selected' : '';
                                                    echo "<option value='".$b['id_barang']."' 
                                                            data-nama='".strtoupper($b['nama_barang'])."'
                                                            data-satuan='".strtoupper($b['satuan'])."' 
                                                            data-merk='".strtoupper($b['merk'])."' 
                                                            data-kategori='".strtoupper($b['kategori'])."'
                                                            data-harga='".$b['harga_beli']."' $sel_b>".$b['nama_barang']."</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="nama_barang_manual[]" class="input-nama-barang" value="<?= $d['nama_barang_manual'] ?>">
                                        </td>
                                        <td>
                                            <select name="kategori_request[]" class="form-select form-select-sm select-kategori" required>
                                                <option value="">- PILIH -</option>
                                                <optgroup label="BENGKEL">
                                                    <option value="BENGKEL MOBIL" <?= $d['kategori_barang'] == 'BENGKEL MOBIL' ? 'selected' : '' ?>>BENGKEL MOBIL</option>
                                                    <option value="BENGKEL LISTRIK" <?= $d['kategori_barang'] == 'BENGKEL LISTRIK' ? 'selected' : '' ?>>BENGKEL LISTRIK</option>
                                                    <option value="BENGKEL DINAMO" <?= $d['kategori_barang'] == 'BENGKEL DINAMO' ? 'selected' : '' ?>>BENGKEL DINAMO</option>
                                                    <option value="BENGKEL BUBUT" <?= $d['kategori_barang'] == 'BENGKEL BUBUT' ? 'selected' : '' ?>>BENGKEL BUBUT</option>
                                                    <option value="MESIN" <?= $d['kategori_barang'] == 'MESIN' ? 'selected' : '' ?>>MESIN</option>
                                                    <option value="LAS" <?= $d['kategori_barang'] == 'LAS' ? 'selected' : '' ?>>LAS</option>
                                                </optgroup>
                                                <optgroup label="UMUM">
                                                    <option value="KANTOR" <?= $d['kategori_barang'] == 'KANTOR' ? 'selected' : '' ?>>KANTOR</option>
                                                    <option value="BANGUNAN" <?= $d['kategori_barang'] == 'BANGUNAN' ? 'selected' : '' ?>>BANGUNAN</option>
                                                    <option value="UMUM" <?= $d['kategori_barang'] == 'UMUM' ? 'selected' : '' ?>>UMUM</option>
                                                </optgroup>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="id_mobil[]" class="form-select form-select-sm select-mobil">
                                                <option value="0">NON MOBIL</option>
                                                <?php
                                                $mbl = mysqli_query($koneksi, "SELECT id_mobil, plat_nomor FROM master_mobil WHERE status_aktif='AKTIF' ORDER BY plat_nomor ASC");
                                                while($m = mysqli_fetch_array($mbl)){
                                                    $sel_m = ($m['id_mobil'] == $d['id_mobil']) ? 'selected' : '';
                                                    echo "<option value='".$m['id_mobil']."' $sel_m>".$m['plat_nomor']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="tipe_request[]" class="form-select form-select-sm select-tipe">
                                                <option value="STOK" <?= $d['tipe_request'] == 'STOK' ? 'selected' : '' ?>>STOK</option>
                                                <option value="LANGSUNG" <?= $d['tipe_request'] == 'LANGSUNG' ? 'selected' : '' ?>>LANGSUNG</option>
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah[]" class="form-control form-control-sm input-qty text-center" step="0.01" value="<?= (float)$d['jumlah'] ?>" required></td>
                                        <td>
                                            <select name="satuan[]" class="form-select form-select-sm select-satuan" required>
                                                <option value="">- PILIH -</option>
                                                <?php 
                                                $sats = ["PCS", "DUS", "KG", "ONS", "LITER", "METER", "CM", "LONJOR", "SET", "ROLL", "PACK", "UNIT", "DRUM", "SAK", "PAIL", "CAN", "BOTOL", "TUBE", "GALON", "IKAT", "LEMBAR", "TABUNG", "KALENG","BATANG","KOTAK","COLT","JURIGEN"];
                                                foreach($sats as $s) {
                                                    $sel_s = ($d['satuan'] == $s) ? 'selected' : '';
                                                    echo "<option value='$s' $sel_s>$s</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <input type="hidden" name="kwalifikasi[]" class="input-kwalifikasi" value="<?= $d['kwalifikasi'] ?>">
                                        <input type="hidden" name="harga[]" class="input-harga" value="<?= $d['harga_satuan_estimasi'] ?>">
                                        <td>
                                            <textarea name="keterangan[]" class="form-control form-control-sm" rows="1"><?= $d['keterangan'] ?></textarea>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
                        <button type="button" id="addRow" class="btn btn-sm btn-success fw-bold px-3 mt-2 shadow-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="card-footer bg-white py-3">
                        <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm">
                            <i class="fas fa-save me-1"></i> SIMPAN PERUBAHAN
                        </button>
                        <a href="pr.php" class="btn btn-danger fw-bold px-4">BATAL</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<<<<<<< HEAD
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {

    function initSelect2(context) {
        $(context).find('.select-barang, .select-kategori, .select-mobil, .select-tipe, .select-satuan, .select-pembeli').select2({
=======
<script>
$(document).ready(function(){
    function initSelect2() {
        $('.select-barang, .select-kategori, .select-mobil, .select-tipe, .select-satuan, .select-pembeli').select2({
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: "-- PILIH --"
        });
    }
<<<<<<< HEAD

    // Init semua baris yang ada
    initSelect2('body');

    // Nonaktifkan select2 pada baris locked agar tidak bisa diklik
    $('.row-locked').find('.select2-container').css('pointer-events', 'none');

    // Auto-fill saat pilih barang
    $(document).on('change', '.select-barang', function () {
        var row     = $(this).closest('tr');
        var selected = $(this).find(':selected');
        row.find('.input-nama-barang').val(selected.data('nama'));
        row.find('.input-kwalifikasi').val(selected.data('merk'));
        row.find('.input-harga').val(selected.data('harga'));
        if (selected.data('kategori')) row.find('.select-kategori').val(selected.data('kategori')).trigger('change.select2');
        if (selected.data('satuan'))   row.find('.select-satuan').val(selected.data('satuan')).trigger('change.select2');
    });

    // Tambah baris baru
    $("#addRow").click(function () {
        // Clone baris PENDING pertama (bukan locked)
        var sourceRow = $('.item-row:not(.row-locked):first');
        if (sourceRow.length === 0) sourceRow = $('.item-row:first');

        var newRow = sourceRow.clone();
        newRow.removeClass('row-locked');
        newRow.find('.select2-container').remove();
        newRow.find('.select2-hidden-accessible').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
        newRow.find('input:not([type=hidden]), textarea').val('');
        newRow.find('.input-id-detail').val('');      // kosong = baris baru
        newRow.find('.input-qty').val('1');
        newRow.find('select').val('').prop('disabled', false);
        newRow.find('.select-mobil').val('0');
        newRow.find('.select-tipe').val('STOK');
        newRow.find('input, textarea').prop('readonly', false);
        // Pastikan tombol hapus tampil, bukan badge
        newRow.find('td:last').html('<button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="fas fa-times"></i></button>');

        $("#tableItem tbody").append(newRow);
        initSelect2(newRow);
    });

    // Hapus baris
    $(document).on('click', '.remove-row', function () {
        var jumlahBarisBisaDihapus = $("#tableItem tbody tr:not(.row-locked)").length;
        if (jumlahBarisBisaDihapus > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Bisa Dihapus',
                text: 'Minimal harus ada 1 item yang bisa diedit.',
                confirmButtonColor: '#0000FF'
            });
        }
=======
    initSelect2();

    $(document).on('change', '.select-barang', function(){
        var row = $(this).closest('tr');
        var selected = $(this).find(':selected');
        row.find('.input-nama-barang').val(selected.data('nama')); 
        row.find('.input-kwalifikasi').val(selected.data('merk'));
        row.find('.input-harga').val(selected.data('harga'));
        if(selected.data('kategori')) row.find('.select-kategori').val(selected.data('kategori')).trigger('change.select2');
        if(selected.data('satuan')) row.find('.select-satuan').val(selected.data('satuan')).trigger('change.select2');
    });

    $("#addRow").click(function(){
        var newRow = $('.item-row:last').clone(); 
        newRow.find('.select2-container').remove();
        newRow.find('.select2-hidden-accessible').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
        newRow.find('input, textarea').val('');
        newRow.find('.input-qty').val('1');
        newRow.find('select').val('').trigger('change');
        newRow.find('.select-mobil').val('0');
        newRow.find('.select-tipe').val('STOK');
        $("#tableItem tbody").append(newRow);
        initSelect2();
    });

    $(document).on('click', '.remove-row', function(){
        if($("#tableItem tbody tr").length > 1) $(this).closest('tr').remove();
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
    });

});
</script>
</body>
</html>