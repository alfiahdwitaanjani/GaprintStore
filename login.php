<?php
session_start();
include 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['id_pengguna'] = $user['id_pengguna']; // <-- penting!
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // 🔀 Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        header("Location: hupenjual.php"); // ADMIN
    } else {
        header("Location: hupembeli.php"); // USER BIASA
    }
    exit;
} else {
    echo "<script>alert('Login gagal'); window.location='login.html';</script>";
}
