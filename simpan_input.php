<?php
include "config.php";

$id = $_POST["id_payment"];

$update = [];

if (isset($_POST["resi"])) $update[] = "resi='" . $_POST["resi"] . "'";

for ($i = 1; $i <= 10; $i++) {
    if (isset($_POST["tracking$i"]))
        $update[] = "tracking$i='" . $_POST["tracking$i"] . "'";
}

if (isset($_POST["status_pengiriman"]))
    $update[] = "status_pengiriman='" . $_POST["status_pengiriman"] . "'";

$sql = "UPDATE payment SET " . implode(",", $update) . " WHERE id_payment='$id'";

mysqli_query($conn, $sql);

header("Location: payment_produk.php");