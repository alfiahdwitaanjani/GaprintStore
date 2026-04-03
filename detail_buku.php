<?php
session_start();
include "config.php";

if (!isset($_GET['id'])) die("ID buku tidak ditemukan");
$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT * FROM stok_buku WHERE id_stok='$id'");
$buku = mysqli_fetch_assoc($q);
if (!$buku) die("Data buku tidak ditemukan");

// --- PROSES Beli Sekarang ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beli_sekarang'])) {
    $id_pengguna = $_SESSION['id_pengguna'] ?? null;
    if (!$id_pengguna) die("Silakan login terlebih dahulu.");

    $jumlah_barang = intval($_POST['jumlah_barang']) ?? 1;
    $harga = $buku['harga'];

    // cek apakah buku sudah ada di keranjang
    $cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_pengguna='$id_pengguna' AND id_stok='$id'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $jumlah_baru = $row['jumlah_barang'] + $jumlah_barang;
        $total_baru = $jumlah_baru * $harga;

        mysqli_query($conn, "UPDATE keranjang SET jumlah_barang='$jumlah_baru', jumlah_harga='$total_baru', updated_at=NOW() WHERE id_keranjang='{$row['id_keranjang']}'");
    } else {
        $total = $jumlah_barang * $harga;
        mysqli_query($conn, "INSERT INTO keranjang (id_pengguna, id_stok, foto, nama_buku, jumlah_barang, harga_barang, jumlah_harga, created_at, updated_at)
        VALUES ('$id_pengguna', '$id', '{$buku['foto']}', '{$buku['judul_buku']}', '$jumlah_barang', '$harga', '$total', NOW(), NOW())");
    }

    // redirect ke payment.php
    header("Location: payment.php?id_stok=$id&jumlah_barang=$jumlah_barang");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Buku</title>
    <link rel="stylesheet" href="css/detail.css">
</head>

<body>

    <div class="container">

        <div class="gambar-buku">
            <img src="uploads/<?php echo $buku['foto']; ?>" alt="<?php echo $buku['judul_buku']; ?>">
        </div>

        <div class="detail-buku">
            <h2><?php echo $buku['judul_buku']; ?></h2>

            <div class="info-buku">
                <p>Harga: <b>Rp <?php echo number_format($buku['harga'], 0, ',', '.'); ?></b></p>
                <p>Stok: <span class="stok"><?php echo $buku['stok']; ?></span></p>
                <p>Terjual: <?php echo $buku['terjual']; ?></p>
                <p>Bahasa: <?php echo $buku['bahasa']; ?></p>
            </div>

            <hr>

            <div class="ringkasan">
                <p><?php echo nl2br($buku['ringkasan']); ?></p>
            </div>

            <!-- Form keranjang -->
            <!-- Form Masukkan Keranjang -->
            <form action="keranjang.php" method="post">
                <input type="hidden" name="id_stok" value="<?= $buku['id_stok'] ?>">
                <input type="hidden" name="nama_buku" value="<?= $buku['judul_buku'] ?>">
                <input type="hidden" name="harga_barang" value="<?= $buku['harga'] ?>">
                <div class="stepper">
                    <button type="button" onclick="kurang()">-</button>
                    <input type="number" id="jumlah_barang_keranjang" name="jumlah_barang" value="1" min="1">
                    <button type="button" onclick="tambah()">+</button>
                    <button type="submit" class="btn-beli" name="aksi" value="keranjang">Masukkan Keranjang</button>
                </div>
            </form>

            <!-- Form Beli Sekarang -->
            <form action="payment.php" method="post">
                <input type="hidden" name="id_stok" value="<?= $buku['id_stok'] ?>">
                <input type="hidden" name="nama_buku" value="<?= $buku['judul_buku'] ?>">
                <input type="hidden" name="harga_barang" value="<?= $buku['harga'] ?>">
                <input type="hidden" name="jumlah_barang" id="jumlah_barang_beli_sekarang" value="1">
                <button type="submit" class="btn-beli" name="aksi" value="beli_sekarang">Beli Sekarang</button>
            </form>
        </div>

    </div>

    <script>
    function tambah() {
        let qKeranjang = document.getElementById("jumlah_barang_keranjang");
        let qBeli = document.getElementById("jumlah_barang_beli_sekarang");
        qKeranjang.value = parseInt(qKeranjang.value) + 1;
        qBeli.value = qKeranjang.value; // sinkron
    }

    function kurang() {
        let qKeranjang = document.getElementById("jumlah_barang_keranjang");
        let qBeli = document.getElementById("jumlah_barang_beli_sekarang");
        if (parseInt(qKeranjang.value) > 1) {
            qKeranjang.value = parseInt(qKeranjang.value) - 1;
            qBeli.value = qKeranjang.value; // sinkron
        }
    }
    </script>

</body>

</html>