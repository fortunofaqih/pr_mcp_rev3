<?php
include "../../config/koneksi.php";
<<<<<<< HEAD
include '../../auth/check_session.php';
=======
>>>>>>> 94045b4816561a997cee91cfa3d1618d40e56612
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    if (mysqli_query($koneksi, "DELETE FROM perbandingan_harga WHERE id_perbandingan = '$id'")) {
        echo "<script>alert('Dihapus!'); window.location='data_perbandingan.php';</script>";
    }
}
?>