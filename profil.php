<?php
session_start();
include "config.php";

// Pastikan login
$id_pengguna = $_SESSION['id_pengguna'] ?? null;
if (!$id_pengguna) {
    header("Location: login.html");
    exit;
}

// Ambil data pengguna
$userQ = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna='$id_pengguna'");
$user = mysqli_fetch_assoc($userQ);

// Ambil data payment untuk riwayat
$paymentQ = mysqli_query($conn, "SELECT * FROM payment WHERE id_pengguna='$id_pengguna' ORDER BY id_payment DESC");

// Hitung status
$totalOrder = mysqli_num_rows($paymentQ);
$prosesQ = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM payment WHERE id_pengguna='$id_pengguna' AND status_pengiriman='proses'");
$proses = mysqli_fetch_assoc($prosesQ)['jml'] ?? 0;
$suksesQ = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM payment WHERE id_pengguna='$id_pengguna' AND status_pengiriman='sukses'");
$sukses = mysqli_fetch_assoc($suksesQ)['jml'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>

    <div class="profile-container">

        <!-- INFO USER -->
        <div class="profile-header">
            <div class="avatar">
                <img src="images/avatar.png" alt="Avatar">
            </div>
            <div class="user-info">
                <h2><?= htmlspecialchars($user['name'] ?? '-') ?></h2>
                <p>Email: <?= htmlspecialchars($user['email'] ?? '-') ?></p>
                <p>No. Telp: <?= htmlspecialchars($user['no_telp'] ?? '-') ?></p>
            </div>
        </div>

        <!-- RINGKASAN ORDER -->
        <div class="order-summary">
            <div class="summary-card">
                <h3>Total Pesanan</h3>
                <p><?= $totalOrder ?></p>
            </div>
            <div class="summary-card">
                <h3>Sedang Dikirim</h3>
                <p><?= $proses ?></p>
            </div>
            <div class="summary-card">
                <h3>Selesai</h3>
                <p><?= $sukses ?></p>
            </div>
        </div>

        <!-- RIWAYAT PEMBELIAN -->
        <div class="order-history">
            <h3>Riwayat Pembelian</h3>
            <?php if (mysqli_num_rows($paymentQ) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Ekspedisi</th>
                        <th>Resi</th>
                        <th>Tracking</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($paymentQ)): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama_buku']) ?></td>
                        <td><?= $p['jumlah_barang'] ?></td>
                        <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['ekspedisi']) ?></td>
                        <td><?= htmlspecialchars($p['resi']) ?></td>
                        <td>
                            <?php
                                    for ($i = 1; $i <= 10; $i++) {
                                        $tracking = $p["tracking$i"];
                                        if (!empty($tracking)) echo "[$tracking] ";
                                    }
                                    ?>
                        </td>
                        <td><?= htmlspecialchars($p['status_pengiriman']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>Belum ada pesanan.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>