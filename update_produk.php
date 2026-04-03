<?php
include "config.php";

// Cek apakah id dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ada");
}

$id = intval($_GET['id']); // aman dari SQL Injection sederhana

// Ambil data buku
$query = mysqli_query($conn, "SELECT * FROM stok_buku WHERE id_stok='$id'");
$row   = mysqli_fetch_assoc($query);

if (!$row) {
    die("Data buku tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Buku</title>
    <link rel="stylesheet" href="css/update.css">
</head>

<body>

    <h2>Update Data Produk</h2>

    <?php if (!empty($row['foto'])) : ?>
        <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" class="buku-foto" alt="Foto Buku">
    <?php endif; ?>

    <hr>

    <!-- Form Update -->
    <form action="proses_update.php" method="post" enctype="multipart/form-data" class="form-buku">

        <input type="hidden" name="id_stok" value="<?php echo $row['id_stok']; ?>">

        <label>Jenis Harga Buku:</label>
        <select name="jenis_harga_buku" required>
            <option value="">-- Pilih Jenis Harga --</option>
            <option value="Normal" <?php if ($row['jenis_harga_buku'] == "Normal") echo "selected"; ?>>Normal</option>
            <option value="Flash Sale" <?php if ($row['jenis_harga_buku'] == "Flash Sale") echo "selected"; ?>>Flash
                Sale</option>
        </select>

        <label>Judul Buku:</label>
        <input type="text" name="judul_buku" value="<?php echo htmlspecialchars($row['judul_buku']); ?>" required>

        <label>Harga:</label>
        <input type="number" name="harga" value="<?php echo $row['harga']; ?>" required>

        <label>Stok:</label>
        <input type="number" name="stok" value="<?php echo $row['stok']; ?>" required>

        <label>Ganti Foto (optional):</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" class="btn-submit">Simpan Perubahan</button>
    </form>

    <hr>

    <!-- Form Hapus -->
    <form action="hapus_produk.php" method="post" onsubmit="return confirm('Yakin hapus produk ini?');"
        class="form-buku">
        <input type="hidden" name="id_stok" value="<?php echo $row['id_stok']; ?>">
        <button type="submit" class="btn-delete">Hapus Produk</button>
    </form>

</body>

</html>