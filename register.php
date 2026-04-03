<?php
include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Cek apakah email sudah terdaftar
$check = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");

if (mysqli_num_rows($check) > 0) {
    // Email sudah ada
    echo "<script>alert('Email sudah terdaftar'); window.location='registrasi.html';</script>";
} else {
    // Tambahkan user baru
    $query = "INSERT INTO pengguna (name, email, password) 
              VALUES ('$name', '$email', '$password')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi berhasil'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan. Coba lagi'); window.location='registrasi.html';</script>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register GAPRINT STORE</title>
    <link rel="stylesheet" href="css/registrasi.css">
</head>

<body>
    <div class="card">
        <h2>Daftar</h2>
        <form action="register.php" method="POST">
            <input type="text" name="name" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Daftar</button>
            <p>Sudah punya akun? <a href="login.html">Login</a></p>
        </form>
    </div>
</body>

</html>