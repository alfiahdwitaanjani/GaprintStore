<?php
include "config.php";

$result = mysqli_query($conn, "SELECT * FROM stok_buku ORDER BY id DESC");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>