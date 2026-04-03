<?php
session_start();
include 'config.php';

// Ambil notif terbaru
$result = mysqli_query($conn, "SELECT message FROM notif WHERE id_pengguna='{$_SESSION['id_pengguna']}' ORDER BY id DESC LIMIT 10");

$notifs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifs[] = $row['message'];
}

echo json_encode($notifs);