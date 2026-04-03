<?php
include "config.php";
$id = $_GET["id"];
mysqli_query($conn, "UPDATE payment SET sudah_download=1 WHERE id_payment='$id'");