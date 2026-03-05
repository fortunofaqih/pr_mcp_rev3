<?php
session_start();
include '../../config/koneksi.php';
<<<<<<< HEAD
include '../../auth/check_session.php';
=======
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612

if ($_SESSION['status'] != "login") {
    header("location:../../login.php?pesan=belum_login");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Proses hapus dari tabel pembelian
    $query = "DELETE FROM pembelian WHERE id_pembelian = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        header("location:data_pembelian.php?pesan=hapus_berhasil");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("location:data_pembelian.php");
}
?>