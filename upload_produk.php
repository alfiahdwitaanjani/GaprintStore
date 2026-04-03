<?php
session_start(); // ❗ wajib agar session bisa dipakai
include 'config.php';

// --- PROSES UPLOAD BUKU ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil input
    $judul      = $_POST['judul_buku'] ?? '';
    $pengarang  = $_POST['nama_pengarang'] ?? '';
    $halaman    = $_POST['halaman'] ?? '';
    $bahasa     = $_POST['bahasa'] ?? '';
    $ringkasan  = $_POST['ringkasan'] ?? '';
    $jenis      = $_POST['jenis_buku'] ?? '';
    $stok       = $_POST['stok'] ?? '';
    $harga      = $_POST['harga'] ?? '';
    $jenisharga = $_POST['jenis_harga_buku'] ?? '';

    // CEK WAJIB ISI
    if (
        $judul == '' || $pengarang == '' || $halaman == '' || $bahasa == '' ||
        $ringkasan == '' || $jenis == '' || $stok == '' || $harga == '' ||
        $jenisharga == '' || empty($_FILES['foto']['name'])
    ) {
        echo "<script>
            alert('Mohon diperiksa kembali, semua kolom wajib diisi');
            window.location='upload_buku.php';
        </script>";
        exit();
    }

    // FOTO
    $fotoName = $_FILES['foto']['name'];
    $tmpName  = $_FILES['foto']['tmp_name'];

    $ext = pathinfo($fotoName, PATHINFO_EXTENSION);
    $newName = time() . "_" . rand(100, 999) . "." . $ext;

    if (!is_dir("uploads")) {
        mkdir("uploads");
    }

    move_uploaded_file($tmpName, "uploads/" . $newName);

    // SIMPAN KE DATABASE
    $sql = "
    INSERT INTO stok_buku
    (foto, judul_buku, jenis_buku, nama_pengarang, halaman, bahasa, ringkasan, stok, harga, jenis_harga_buku, terjual)
    VALUES
    ('$newName', '$judul', '$jenis', '$pengarang', '$halaman', '$bahasa', '$ringkasan', '$stok', '$harga', '$jenisharga', 0)
    ";

    if (mysqli_query($conn, $sql)) {
        // Ambil ID buku terakhir yang baru ditambahkan
        $lastId = mysqli_insert_id($conn);

        // Simpan ke session untuk notifikasi realtime
        $_SESSION['last_buku_id'] = $lastId;

        // Bisa juga menambahkan session notif sementara untuk alert admin
        $_SESSION['notif_admin'] = "Buku '$judul' berhasil ditambahkan!";

        echo "<script>
            window.location='hupenjual.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan data');
            window.location='upload_buku.php';
        </script>";
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Upload Buku</title>
    <link rel="stylesheet" href="css/up.css">
</head>

<body>
    <div class="container">
        <h2>Tambah Buku Baru</h2>
        <form action="upload_buku.php" method="POST" enctype="multipart/form-data">
            <label>Jenis Harga Buku:</label>
            <select name="jenis_harga_buku" required>
                <option value="">-- Pilih Jenis Harga Buku --</option>
                <option value="Flash Sale">Flash Sale</option>
                <option value="Normal">Normal</option>
            </select>

            <label>Foto Buku:</label>
            <input type="file" name="foto" required>

            <label>Judul Buku:</label>
            <input type="text" name="judul_buku" required>

            <label>Jenis Buku:</label>
            <select name="jenis_buku" required>
                <option value="">-- Pilih Jenis Buku --</option>
                <option value="Pelajaran SD">Pelajaran SD</option>
                <option value="Pelajaran SMP">Pelajaran SMP</option>
                <option value="Pelajaran SMA">Pelajaran SMA</option>
                <option value="Novel">Novel</option>
                <option value="Dongeng">Dongeng</option>
                <option value="Anak-anak">Anak-anak</option>
                <option value="Komik">Komik</option>
                <option value="Resep">Resep</option>
            </select>

            <label>Nama Pengarang:</label>
            <input type="text" name="nama_pengarang" required>

            <label>Jumlah Halaman:</label>
            <input type="number" name="halaman" required>

            <label>Bahasa Buku:</label>
            <input type="text" name="bahasa" required>

            <label>Ringkasan Buku:</label>
            <textarea name="ringkasan" rows="4" required></textarea>

            <label>Stok Buku:</label>
            <input type="number" name="stok" required>

            <label>Harga Buku:</label>
            <input type="number" name="harga" required>

            <button type="submit">Kirim</button>
        </form>
    </div>
</body>

</html>