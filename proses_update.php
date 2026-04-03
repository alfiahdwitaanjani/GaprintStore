<?php
include "config.php";

$id     = $_POST['id_stok'];
$jenis  = $_POST['jenis_harga_buku'];
$judul  = $_POST['judul_buku'];
$harga  = $_POST['harga'];
$stok   = $_POST['stok'];

// cek apakah upload foto baru
if($_FILES['foto']['name']!=""){

    $namaFile = time() . "_" . $_FILES['foto']['name'];
    $tmp      = $_FILES['foto']['tmp_name'];

    move_uploaded_file($tmp, "uploads/".$namaFile);

    // update dengan foto
    mysqli_query($conn, "UPDATE stok_buku SET 
        jenis_harga_buku='$jenis',
        judul_buku='$judul',
        harga='$harga',
        stok='$stok',
        foto='$namaFile'
        WHERE id_stok='$id'");
} else {

    // update tanpa ganti foto
    mysqli_query($conn, "UPDATE stok_buku SET 
        jenis_harga_buku='$jenis',
        judul_buku='$judul',
        harga='$harga',
        stok='$stok'
        WHERE id_stok='$id'");
}

header("Location: hupenjual.php");
exit;
?>