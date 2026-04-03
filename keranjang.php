<?php
session_start();
include "config.php";

// --- Ambil ID pengguna dari session login ---
$id_pengguna = $_SESSION['id_pengguna'] ?? null;
if (!$id_pengguna) {
    die("Silakan login terlebih dahulu.");
}

// --- Pastikan form dikirim ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak valid.");
}

// --- Ambil data POST dari form ---
$id_stok = isset($_POST['id_stok']) ? intval($_POST['id_stok']) : null;
$nama_buku = $_POST['nama_buku'] ?? null;
$harga_barang = isset($_POST['harga_barang']) ? intval($_POST['harga_barang']) : null;
$jumlah_barang = isset($_POST['jumlah_barang']) ? intval($_POST['jumlah_barang']) : null;

// --- Validasi input ---
if (!$id_stok || !$nama_buku || !$harga_barang || !$jumlah_barang || $jumlah_barang <= 0) {
    die("Data buku tidak valid.");
}

// --- Cek buku ada di stok_buku ---
$cek_buku = mysqli_query($conn, "SELECT * FROM stok_buku WHERE id_stok='$id_stok'");
if (!$cek_buku || mysqli_num_rows($cek_buku) == 0) {
    die("Buku tidak ditemukan di stok.");
}

$row_buku = mysqli_fetch_assoc($cek_buku);
$foto = $row_buku['foto'];

// --- Cek apakah buku sudah ada di keranjang ---
$cek_keranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_pengguna='$id_pengguna' AND id_stok='$id_stok'");
$cek = mysqli_fetch_assoc($cek_keranjang);

if ($cek) {
    // Buku sudah ada → update jumlah dan total harga
    $jumlah_baru = $cek['jumlah_barang'] + $jumlah_barang;
    $total_baru = $jumlah_baru * $harga_barang;

    $update = mysqli_query($conn, "UPDATE keranjang SET 
        jumlah_barang='$jumlah_baru',
        jumlah_harga='$total_baru',
        updated_at=NOW()
        WHERE id_keranjang='{$cek['id_keranjang']}'");

    if (!$update) {
        die("Gagal update keranjang: " . mysqli_error($conn));
    }
} else {
    // Buku belum ada → insert baru ke keranjang
    $total = $jumlah_barang * $harga_barang;

    $insert = mysqli_query($conn, "INSERT INTO keranjang (
        id_pengguna,
        id_stok,
        foto,
        nama_buku,
        jumlah_barang,
        harga_barang,
        jumlah_harga,
        created_at,
        updated_at
    ) VALUES (
        '$id_pengguna',
        '$id_stok',
        '$foto',
        '$nama_buku',
        '$jumlah_barang',
        '$harga_barang',
        '$total',
        NOW(),
        NOW()
    )");

    if (!$insert) {
        die("Gagal menambahkan ke keranjang: " . mysqli_error($conn));
    }
}

// --- Redirect ke halaman keranjang ---
header("Location: hupembeli.php");
exit;