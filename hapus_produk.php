<?php
include "config.php";

$id = $_POST['id_stok'];

mysqli_query($conn, "DELETE FROM stok_buku WHERE id_stok='$id'");

header("Location: hupenjual.php");
?>