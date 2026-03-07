<?php
session_start();
include '../../config/koneksi.php';
include '../../auth/check_session.php';

if ($_SESSION['status'] != "login") {
    header("location:../../login.php?pesan=belum_login");
    exit;
}

$nama_user_login = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : "USER";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form PR Barang Besar - MCP System</title>
    <link rel="icon" type="image/png" href="/pr_mcp/assets/img/logo_mcp.png">

    <title>Buat Request Baru - MCP System</title>
   <link rel="icon" type="image/png" href="/pr_mcp/assets/img/logo_mcp.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --red: #dc3545; --red-dark: #b02a37; --bg: #f4f6f9; }
        body { background: var(--bg); font-size: 0.85rem; }
        .page-header { background: linear-gradient(135deg, var(--red-dark), var(--red)); color: white; border-radius: 12px 12px 0 0; padding: 18px 24px; }
        .page-header h5 { font-size: 1rem; margin: 0; }
        .info-alert { background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 6px; padding: 10px 14px; font-size: 0.8rem; }
        .section-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px; margin-bottom: 4px; }
        .table-input thead { background: var(--red); color: white; font-size: 0.72rem; text-transform: uppercase; }
        .table-responsive { border-radius: 8px; overflow-x: auto; }
        .table-input { min-width: 1500px; table-layout: fixed; }
        .col-no{width:40px;}.col-brg{width:220px;}.col-kat{width:150px;}.col-kwal{width:160px;}
        .col-mbl{width:130px;}.col-tip{width:100px;}.col-qty{width:80px;}.col-sat{width:110px;}
        .col-hrg{width:140px;}.col-tot{width:140px;}.col-ket{width:280px;}.col-aks{width:50px;}
        input, select, textarea { text-transform: uppercase; font-size: 0.8rem !important; }
        .bg-autonumber { background: #e9ecef; border-style: dashed; color: #00008B; font-weight: 700; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 31px !important; padding: 2px 5px !important; }
        textarea.input-keterangan { resize: vertical; min-height: 34px; }
        textarea.input-keterangan:focus { min-height: 70px; transition: 0.2s; }
        .total-box { background: white; border-radius: 10px; border: 1px solid #dee2e6; padding: 16px; }
        .total-box .grand-label { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; font-weight: 700; }
        .total-box .grand-value { font-size: 1.4rem; font-weight: 800; color: var(--red); }
        .po-section { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 22px 24px; margin-top: 8px; }
        .po-section-title { font-size: 0.92rem; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
        .po-section-title .divider { width: 4px; height: 22px; background: #3b82f6; border-radius: 2px; flex-shrink: 0; }
        .grand-po-box { background: #1e3a8a; color: white; border-radius: 10px; padding: 14px 18px; height: 100%; display: flex; flex-direction: column; justify-content: center; }
        .grand-po-box .label { font-size: 0.7rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
        .grand-po-box .value { font-size: 1.4rem; font-weight: 800; }
        .card-footer { background: white; border-top: 1px solid #dee2e6; border-radius: 0 0 12px 12px; }
        .row-number { color: #aaa; font-size: 0.75rem; }
    </style>
</head>
<body class="py-4">
<div class="container-fluid px-4">
    <form action="proses_simpan_besar.php" method="POST" id="formPRBesar">
        <div class="card shadow-sm border-0" style="border-radius:12px;">

            <!-- HEADER -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5><i class="fas fa-boxes-stacked me-2"></i>PURCHASE REQUEST — BARANG BESAR / INVESTASI</h5>
                        <small class="opacity-75">Pengajuan ini memerlukan persetujuan Manager sebelum PO diterbitkan</small>
                    </div>
                    <a href="pr.php" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
            </div>

            <div class="card-body p-4">

                <!-- ALUR INFO -->
                <div class="info-alert mb-4">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    <strong>Alur:</strong> Isi PR + Data PO &nbsp;→&nbsp;
                    <span class="badge bg-secondary">PENDING</span> &nbsp;→&nbsp;
                    Manager Review + Lihat PO &nbsp;→&nbsp;
                    <span class="badge bg-success">APPROVE</span> &nbsp;→&nbsp;
                    <span class="badge bg-primary">Petugas Pembelian Proses</span>
                </div>

                <!-- HEADER FORM PR -->
                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <div class="section-label">Nomor Request</div>
                        <input type="text" class="form-control bg-autonumber" value="[ AUTO GENERATE ]" readonly>
                    </div>
                    <div class="col-md-2">
                        <div class="section-label">Tanggal Request</div>
                        <input type="date" name="tgl_request" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <div class="section-label">Dibuat Oleh</div>
                        <input type="text" name="nama_pemesan" class="form-control bg-light" value="<?= $nama_user_login ?>" readonly required>
                    </div>
                    <div class="col-md-5">
                        <div class="section-label">Petugas Pembelian <span class="text-danger">*</span></div>
                        <select name="nama_pembeli" class="form-select select-pembeli" required>
                            <option value="">-- Pilih Petugas Pembelian --</option>
                            <?php
                            $user_beli = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE status_aktif='AKTIF' AND (role='bagian_pembelian' OR bagian='Pembelian') ORDER BY nama_lengkap ASC");
                            while ($u = mysqli_fetch_array($user_beli)) {
                                echo "<option value='".strtoupper($u['nama_lengkap'])."'>".strtoupper($u['nama_lengkap'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="section-label">Keperluan / Tujuan Pembelian <span class="text-danger">*</span></div>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Jelaskan tujuan dan keperluan pengajuan barang besar ini..." required></textarea>
                    </div>
                </div>

                <hr class="my-3">

                <!-- TABEL ITEM -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-danger" style="font-size:0.85rem;"><i class="fas fa-list me-1"></i> Daftar Item Barang</span>
                    <button type="button" id="addRow" class="btn btn-sm btn-success fw-bold px-3"><i class="fas fa-plus me-1"></i> Tambah Baris</button>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-input align-middle" id="tableItem">
                        <thead>
                            <tr class="text-center">
                                <th class="col-no">#</th>
                                <th class="col-brg">Nama Barang</th>
                                <th class="col-kat">Kategori</th>
                                <th class="col-kwal">Kwalifikasi / Merk</th>
                                <th class="col-mbl">Unit / Mobil</th>
                                <th class="col-tip">Tipe</th>
                                <th class="col-qty">Qty</th>
                                <th class="col-sat">Satuan</th>
                                <th class="col-hrg">Harga Est. (Rp)</th>
                                <th class="col-tot">Total (Rp)</th>
                                <th class="col-ket">Catatan Detail</th>
                                <th class="col-aks"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItem">
                            <tr class="item-row">
                                <td class="text-center row-number">1</td>
                                <td>
                                    <select name="id_barang[]" class="form-select form-select-sm select-barang" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php
                                        $brg = mysqli_query($koneksi, "SELECT * FROM master_barang WHERE status_aktif='AKTIF' AND is_active=1 ORDER BY nama_barang ASC");
                                        while ($b = mysqli_fetch_array($brg)) {
                                            echo "<option value='".$b['id_barang']."' data-nama='".addslashes(strtoupper($b['nama_barang']))."' data-satuan='".strtoupper($b['satuan'])."' data-merk='".strtoupper($b['merk'])."' data-kategori='".strtoupper($b['kategori'])."' data-harga='".$b['harga_beli']."'>".strtoupper($b['nama_barang'])."</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="nama_barang_manual[]" class="input-nama-barang">
                                </td>
                                <td>
                                    <select name="kategori_request[]" class="form-select form-select-sm select-kategori" required>
                                        <option value="">- Pilih -</option>
                                        <optgroup label="BENGKEL">
                                            <option value="BENGKEL MOBIL">BENGKEL MOBIL</option>
                                            <option value="BENGKEL LISTRIK">BENGKEL LISTRIK</option>
                                            <option value="BENGKEL DINAMO">BENGKEL DINAMO</option>
                                            <option value="BENGKEL BUBUT">BENGKEL BUBUT</option>
                                            <option value="MESIN">MESIN</option>
                                            <option value="LAS">LAS</option>
                                        </optgroup>
                                        <optgroup label="UMUM">
                                            <option value="KANTOR">KANTOR</option>
                                            <option value="BANGUNAN">BANGUNAN</option>
                                            <option value="UMUM">UMUM</option>
                                        </optgroup>
                                        <optgroup label="INVESTASI">
                                            <option value="INVESTASI MESIN">INVESTASI MESIN</option>
                                            <option value="INVESTASI KENDARAAN">INVESTASI KENDARAAN</option>
                                            <option value="INVESTASI IT">INVESTASI IT</option>
                                            <option value="INVESTASI LAINNYA">INVESTASI LAINNYA</option>
                                        </optgroup>
                                    </select>
                                </td>
                                <td><input type="text" name="kwalifikasi[]" class="form-control form-control-sm input-kwalifikasi" placeholder="Merk / Spesifikasi..."></td>
                                <td>
                                    <select name="id_mobil[]" class="form-select form-select-sm select-mobil">
                                        <option value="0">NON MOBIL</option>
                                        <?php
                                        $mbl = mysqli_query($koneksi, "SELECT id_mobil, plat_nomor FROM master_mobil WHERE status_aktif='AKTIF' ORDER BY plat_nomor ASC");
                                        while ($m = mysqli_fetch_array($mbl)) {
                                            echo "<option value='".$m['id_mobil']."'>".$m['plat_nomor']."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="tipe_request[]" class="form-select form-select-sm select-tipe">
                                        <option value="LANGSUNG" selected>LANGSUNG</option>
                                        <option value="STOK">STOK</option>
                                    </select>
                                </td>
                                <td><input type="number" name="jumlah[]" class="form-control form-control-sm input-qty text-center" step="0.01" min="0.01" value="1" required></td>
                                <td>
                                    <select name="satuan[]" class="form-select form-select-sm select-satuan" required>
                                        <option value="">- Pilih -</option>
                                        <option value="PCS">PCS</option><option value="DUS">DUS</option>
                                        <option value="KG">KG</option><option value="ONS">ONS</option>
                                        <option value="LITER">LITER</option><option value="METER">METER</option>
                                        <option value="SET">SET</option><option value="UNIT">UNIT</option>
                                        <option value="LEMBAR">LEMBAR</option><option value="BATANG">BATANG</option>
                                        <option value="ROLL">ROLL</option><option value="PACK">PACK</option>
                                        <option value="DRUM">DRUM</option><option value="SAK">SAK</option>
                                        <option value="PAIL">PAIL</option><option value="GALON">GALON</option>
                                        <option value="BOTOL">BOTOL</option><option value="TUBE">TUBE</option>
                                        <option value="LONJOR">LONJOR</option><option value="KOTAK">KOTAK</option>
                                        <option value="IKAT">IKAT</option><option value="JURIGEN">JURIGEN</option>
                                    </select>
                                </td>
                                <td><input type="number" name="harga[]" class="form-control form-control-sm input-harga text-end" placeholder="0" min="0" step="1"></td>
                                <td><input type="text" class="form-control form-control-sm input-subtotal text-end bg-light fw-bold" value="0" readonly tabindex="-1"></td>
                                <td><textarea name="keterangan_item[]" class="form-control form-control-sm input-keterangan" rows="1" placeholder="Spesifikasi mendalam..."></textarea></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- TOTAL ITEM -->
                <div class="row justify-content-end mb-4">
                    <div class="col-md-4">
                        <div class="total-box">
                            <div class="grand-label">Total Estimasi Item</div>
                            <div class="grand-value" id="grandTotalDisplay">Rp 0</div>
                            <input type="hidden" id="grandTotalValue" name="grand_total" value="0">
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                <!-- =============================================
                     SECTION: DATA PURCHASE ORDER
                ============================================== -->
                <div class="po-section">
                    <div class="po-section-title">
                        <div class="divider"></div>
                        <i class="fas fa-file-invoice text-primary"></i>
                        Data Purchase Order (PO)
                        <span class="badge bg-primary fw-normal ms-1" style="font-size:0.7rem; text-transform:none;">Data ini akan dilihat Manager saat review</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <div class="section-label">Supplier / Vendor <span class="text-danger">*</span></div>
                            <select name="id_supplier" id="selectSupplier" class="form-select select-supplier" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php
                                $sup = mysqli_query($koneksi, "SELECT * FROM master_supplier WHERE status_aktif='AKTIF' ORDER BY nama_supplier ASC");
                                while ($s = mysqli_fetch_array($sup)) {
                                    echo "<option value='".$s['id_supplier']."'
                                            data-alamat='".addslashes($s['alamat'] ?? '')."'
                                            data-kota='".addslashes($s['kota'] ?? '')."'
                                            data-telp='".($s['telp'] ?? '')."'
                                            data-cp='".addslashes($s['atas_nama'] ?? '')."'>
                                            ".strtoupper($s['nama_supplier'])."
                                          </option>";
                                }
                                ?>
                            </select>
                            <small><a href="master_supplier.php" target="_blank" class="text-decoration-none text-muted"><i class="fas fa-plus-circle me-1"></i>Tambah supplier baru</a></small>
                        </div>
                        <div class="col-md-3">
                            <div class="section-label">Tanggal PO <span class="text-danger">*</span></div>
                            <input type="date" name="tgl_po" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4" id="infoSupplierBox" style="display:none;">
                            <div class="section-label">Info Supplier</div>
                            <div class="p-2 bg-white border rounded" style="font-size:0.78rem; line-height:2;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i><span id="info_alamat_pr">-</span><br>
                                <i class="fas fa-phone text-primary me-1"></i><span id="info_telp_pr">-</span><br>
                                <i class="fas fa-user text-success me-1"></i>U/P: <span id="info_cp_pr">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="section-label">Diskon (Rp)</div>
                            <input type="number" name="diskon" id="inputDiskon" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <div class="section-label">PPN</div>
                            <select name="ppn_persen" id="selectPPN" class="form-select">
                                <option value="0">Tanpa PPN</option>
                                <option value="11" selected>PPN 11%</option>
                                <option value="12">PPN 12%</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="section-label">Grand Total PO (Estimasi)</div>
                            <div class="grand-po-box">
                                <div class="label">Grand Total PO</div>
                                <div class="value" id="displayGrandTotalPO">Rp 0</div>
                                <input type="hidden" name="grand_total_po" id="hiddenGrandTotalPO" value="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="section-label">Catatan / Ketentuan Pembayaran PO</div>
                        <textarea name="catatan_po" class="form-control" rows="3"
                            placeholder="Contoh: Pembayaran AN. PT. XYZ, No Rek: 1234, Bank BCA Cab. Surabaya..."></textarea>
                    </div>
                </div>

            </div><!-- end card-body -->

            <!-- FOOTER -->
            <div class="card-footer px-4 py-3 d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size:0.78rem;">
                    <i class="fas fa-shield-alt text-warning me-1"></i>
                    PR ini akan berstatus <strong>PENDING</strong> dan menunggu persetujuan Manager.
                </div>
                <div>
                    <a href="pr.php" class="btn btn-outline-secondary me-2"><i class="fas fa-times me-1"></i> Batal</a>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="fas fa-paper-plane me-1"></i> Kirim untuk Approval
                    </button>
                </div>
            </div>

        </div><!-- end card -->
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){

    function initSelect2(ctx) {
        var $ctx = ctx ? $(ctx) : $(document);
        $ctx.find('.select-barang,.select-kategori,.select-mobil,.select-tipe,.select-satuan,.select-pembeli,.select-supplier').select2({ theme:'bootstrap-5', width:'100%' });
    }
    initSelect2();

    function rp(n) { return 'Rp ' + parseFloat(n||0).toLocaleString('id-ID'); }

    function hitungSubtotal(row) {
        var qty   = parseFloat(row.find('.input-qty').val()) || 0;
        var harga = parseFloat(row.find('.input-harga').val()) || 0;
        row.find('.input-subtotal').val((qty*harga).toLocaleString('id-ID'));
        hitungSemuaTotal();
    }

    function hitungSemuaTotal() {
        var totalItem = 0;
        $('.input-harga').each(function(){
            var row = $(this).closest('tr');
            totalItem += (parseFloat(row.find('.input-qty').val())||0) * (parseFloat($(this).val())||0);
        });
        $('#grandTotalDisplay').text(rp(totalItem));
        $('#grandTotalValue').val(totalItem);

        var diskon = parseFloat($('#inputDiskon').val()) || 0;
        var ppn    = parseFloat($('#selectPPN').val()) || 0;
        var total  = totalItem - diskon;
        var ppnNom = total * (ppn/100);
        var grand  = total + ppnNom;
        $('#displayGrandTotalPO').text(rp(grand));
        $('#hiddenGrandTotalPO').val(grand);
    }

    function updateRowNumbers() {
        $('#tbodyItem tr.item-row').each(function(i){ $(this).find('.row-number').text(i+1); });
    }

    // Auto Fill
    $(document).on('change', '.select-barang', function(){
        var row = $(this).closest('tr'), sel = $(this).find(':selected');
        row.find('.input-nama-barang').val(sel.data('nama'));
        row.find('.input-kwalifikasi').val(sel.data('merk'));
        row.find('.input-harga').val(sel.data('harga')||'');
        if (sel.data('kategori')) row.find('.select-kategori').val(sel.data('kategori')).trigger('change.select2');
        if (sel.data('satuan'))   row.find('.select-satuan').val(sel.data('satuan')).trigger('change.select2');
        hitungSubtotal(row);
    });

    $(document).on('input', '.input-qty, .input-harga', function(){ hitungSubtotal($(this).closest('tr')); });
    $('#inputDiskon, #selectPPN').on('input change', function(){ hitungSemuaTotal(); });

    // Info supplier
    $('#selectSupplier').on('change', function(){
        var sel = $(this).find(':selected');
        if ($(this).val()) {
            $('#info_alamat_pr').text((sel.data('alamat')||'-') + (sel.data('kota') ? ', '+sel.data('kota') : ''));
            $('#info_telp_pr').text(sel.data('telp')||'-');
            $('#info_cp_pr').text(sel.data('cp')||'-');
            $('#infoSupplierBox').show();
        } else { $('#infoSupplierBox').hide(); }
    });

    // Tambah baris
  // Tambah baris - FIXED
$('#addRow').click(function(){
    // 1. Ambil template baris pertama
    var $template = $('#tbodyItem tr.item-row').first();
    
    // 2. Hancurkan select2 pada template SEBELUM cloning agar tidak membawa event/ID lama
    // Tapi kita lakukan pada clone-nya saja agar baris asal tidak rusak
    var $newRow = $template.clone();

    // 3. Bersihkan sisa-sisa Select2 dan nilai input di baris baru
    $newRow.find('.select2-container').remove();
    $newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');
    $newRow.find('option').removeAttr('data-select2-id');
    
    // 4. Reset nilai input
    $newRow.find('input:not([type=hidden])').val('');
    $newRow.find('textarea').val('');
    $newRow.find('.input-qty').val('1');
    $newRow.find('.input-harga, .input-subtotal').val('0');
    $newRow.find('.input-nama-barang').val('');
    
    // 5. Reset pilihan select
    $newRow.find('select').val('');
    $newRow.find('.select-mobil').val('0');
    $newRow.find('.select-tipe').val('LANGSUNG');

    // 6. Masukkan ke tabel
    $('#tbodyItem').append($newRow);

    // 7. Inisialisasi ulang Select2 HANYA untuk baris baru
    initSelect2($newRow);
    
    // 8. Update nomor urut
    updateRowNumbers();
});

    // Hapus Baris
    $(document).on('click', '.remove-row', function(){
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            hitungSemuaTotal(); updateRowNumbers();
        } else { Swal.fire('Perhatian','Minimal harus ada 1 item barang.','warning'); }
    });

    // Submit
    $('#formPRBesar').on('submit', function(e){
        e.preventDefault();
        var form = this, valid = true;
        $('.select-barang').each(function(){ if (!$(this).val()) { valid=false; return false; } });
        if (!valid) { Swal.fire('Perhatian','Pastikan semua baris sudah memilih nama barang.','warning'); return; }
        if (!$('#selectSupplier').val()) { Swal.fire('Perhatian','Pilih supplier/vendor untuk PO terlebih dahulu.','warning'); return; }

        var totalPO = $('#displayGrandTotalPO').text();
        Swal.fire({
            title:'Kirim PR untuk Approval?',
            html:`PR beserta data PO akan dikirim ke Manager.<br><small class="text-muted">Grand Total PO: <strong class="text-danger">${totalPO}</strong></small>`,
            icon:'question', showCancelButton:true, confirmButtonColor:'#dc3545',
            confirmButtonText:'<i class="fas fa-paper-plane me-1"></i> Ya, Kirim!', cancelButtonText:'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                Swal.fire({ title:'Memproses...', allowOutsideClick:false, showConfirmButton:false, didOpen:()=>Swal.showLoading() });
                form.submit();
            }
        });
    });

    if (new URLSearchParams(window.location.search).get('pesan') === 'berhasil') {
        Swal.fire({ icon:'success', title:'PR Berhasil Dikirim!', text:'Menunggu persetujuan Manager.', confirmButtonColor:'#dc3545' });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const pesan = urlParams.get('pesan');

    if (pesan === 'berhasil') {
        Swal.fire({
            icon: 'success',
            title: 'BERHASIL DISIMPAN!',
            text: 'Data Purchase Request telah berhasil masuk ke sistem.',
            confirmButtonColor: '#0000FF'
        });
    }
    
    // Penting: Hapus parameter dari URL agar notifikasi tidak muncul lagi saat refresh
    window.history.replaceState({}, document.title, window.location.pathname);
});
</script>
</body>
</html>
