<?php
session_start();
include "config.php";

$id_pengguna = $_SESSION['id_pengguna'] ?? null;
if (!$id_pengguna) die("Silakan login terlebih dahulu.");

// --- Inisialisasi variabel ---
$id_stok = $_POST['id_stok'] ?? null;
$nama_buku = $_POST['nama_buku'] ?? null;
$harga_barang = $_POST['harga_barang'] ?? null;
$jumlah_barang = $_POST['jumlah_barang'] ?? 1;

// --- Jika datang dari keranjang ---
if (isset($_POST['dari_keranjang'])) {
    $id_stok = intval($_POST['id_stok']);
    $cekKeranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_pengguna='$id_pengguna' AND id_stok='$id_stok'");
    if ($row = mysqli_fetch_assoc($cekKeranjang)) {
        $nama_buku = $row['nama_buku'];
        $jumlah_barang = $row['jumlah_barang'];
        $harga_barang = $row['harga_barang'];
    } else {
        die("Buku tidak ditemukan di keranjang.");
    }
}

// --- Jika datang dari tombol Beli Sekarang ---
if (isset($_POST['beli_sekarang'])) {
    $id_stok = intval($_POST['id_stok']);
    $nama_buku = $_POST['nama_buku'];
    $jumlah_barang = $_POST['jumlah_barang'];
    $harga_barang = $_POST['harga_barang'];
}

// --- Total harga sementara (tanpa ekspedisi dulu) ---
$jumlah_harga = $jumlah_barang * $harga_barang;

// --- Proses submit pembayaran ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    $alamat = $_POST['alamat'] ?? '';
    $kelurahan = $_POST['kelurahan'] ?? '';
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $kode_pos = $_POST['kode_pos'] ?? '';
    $patokan = $_POST['patokan'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';
    $nama_penerima = $_POST['nama_penerima'] ?? '';
    $ekspedisi = $_POST['ekspedisi'] ?? '';
    $jenis_pembayaran = $_POST['jenis_pembayaran'] ?? '';
    $bank = $_POST['bank'] ?? null;

    // --- Hitung biaya ekspedisi ---
    $biaya_ekspedisi = 0;
    $estimasi = '';
    switch ($ekspedisi) {
        case "JNE":
            $biaya_ekspedisi = 5000;
            $estimasi = "7 hari";
            $total_harga = $jumlah_harga + $biaya_ekspedisi;
            break;
        case "TIKI":
            $biaya_ekspedisi = 10000;
            $estimasi = "5 hari";
            $total_harga = $jumlah_harga + $biaya_ekspedisi;
            break;
        case "Sicepat":
            $biaya_ekspedisi = 15000;
            $estimasi = "3 hari";
            $total_harga = $jumlah_harga + $biaya_ekspedisi;
            break;
    }

    // --- Tentukan status payment ---
    $status_payment = ($jenis_pembayaran == "Bank Transfer") ? "berhasil" : "menunggu";

    // --- Insert ke tabel payment ---
    mysqli_query($conn, "INSERT INTO payment (
    id_pengguna, id_stok, nama_buku, jumlah_barang, harga_barang,
    biaya_ekspedisi, total_harga,
    alamat, kelurahan, kecamatan, kota, kode_pos, patokan, no_telp, nama_penerima,
    ekspedisi, metode_pembayaran, status_payment, bank, created_at
    ) VALUES (
    '$id_pengguna','$id_stok','$nama_buku','$jumlah_barang','$harga_barang',
    '$biaya_ekspedisi','$total_harga',
    '$alamat','$kelurahan','$kecamatan','$kota','$kode_pos','$patokan','$no_telp','$nama_penerima',
    '$ekspedisi','$jenis_pembayaran','$status_payment','$bank',NOW()
    )");

    // --- Hapus dari keranjang jika ada ---
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_pengguna='$id_pengguna' AND id_stok='$id_stok'");

    // --- Update jumlah terjual di stok_buku ---
    mysqli_query($conn, "
        UPDATE stok_buku 
        SET terjual = terjual + $jumlah_barang
        WHERE id_stok = '$id_stok'
    ");

    // --- Update stok di stok_buku ---
    mysqli_query($conn, "
        UPDATE stok_buku 
        SET stok = stok - $jumlah_barang
        WHERE id_stok = '$id_stok'
    ");

    // --- Bank info sesuai jumlah buku ---
    $bank_info = "";
    if ($jenis_pembayaran == "Bank Transfer") {
        $bank_label = "";
        switch ($bank) {
            case "BCA":
                $bank_label = "BCA, Rek 001 a/n PT GAT";
                break;
            case "BRI":
                $bank_label = "BRI, Rek 002 a/n PT GAT";
                break;
            case "BJB":
                $bank_label = "BJB, Rek 003 a/n PT GAT";
                break;
            case "BNI":
                $bank_label = "BNI, Rek 004 a/n PT GAT";
                break;
        }
        $bank_info = $bank_label;
    }
?>
<h2>Pembayaran Berhasil!</h2>
<p>Terima kasih telah melakukan pembayaran.</p>
<p>Jumlah Buku: <?= $jumlah_barang ?> pcs</p>
<p>Total Harga Buku: Rp <?= number_format($jumlah_harga, 0, ',', '.') ?></p>
<p>Biaya Ekspedisi: Rp <?= number_format($biaya_ekspedisi, 0, ',', '.') ?></p>
<p><b>Total yang dibayarkan: Rp <?= number_format($total_harga, 0, ',', '.') ?></b></p>
<p>Estimasi pengiriman: <?= $estimasi ?></p>
<?php if ($jenis_pembayaran == "Bank Transfer"): ?>
<p>Silakan transfer ke rekening berikut untuk setiap buku yang dibeli:</p>
<p><?= $bank_info ?></p>
<?php else: ?>
<p>Pembayaran akan dilakukan saat barang diterima (COD).</p>
<?php endif; ?>

<script>
// tunggu 5 detik
setTimeout(function() {
    alert("Happy Shopping");
    window.location.href = "hupembeli.php";
}, 5000);
</script>

<?php
    mysqli_query($conn, "INSERT INTO np(pesan,status,created_at)
VALUES ('Cek payment now','baru',NOW())");

    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Payment - GAPRINT STORE</title>
    <link rel="stylesheet" href="css/payment.css">
</head>

<body>
    <h2>Checkout Buku</h2>
    <p><b><?= $nama_buku ?></b></p>
    <p>Jumlah: <?= $jumlah_barang ?> pcs</p>
    <p>Harga Satuan: Rp <?= number_format($harga_barang, 0, ',', '.') ?></p>
    <p>Total: Rp <?= number_format($jumlah_harga, 0, ',', '.') ?></p>

    <form method="post" onsubmit="return validateForm()">
        <input type="hidden" name="id_stok" value="<?= $id_stok ?>">
        <input type="hidden" name="nama_buku" value="<?= $nama_buku ?>">
        <input type="hidden" name="jumlah_barang" value="<?= $jumlah_barang ?>">
        <input type="hidden" name="harga_barang" value="<?= $harga_barang ?>">

        <label>Alamat:</label><br>
        <input type="text" name="alamat" placeholder="Alamat lengkap" required><br>
        <input type="text" name="kelurahan" placeholder="Kelurahan" required><br>
        <input type="text" name="kecamatan" placeholder="Kecamatan" required><br>
        <input type="text" name="kota" placeholder="Kota/Kabupaten" required><br>
        <input type="text" name="kode_pos" placeholder="Kode Pos" required><br>
        <input type="text" name="patokan" placeholder="Patokan alamat"><br>
        <input type="text" name="no_telp" placeholder="No. Telepon" required><br>
        <input type="text" name="nama_penerima" placeholder="Nama Penerima" required><br><br>

        <label>Ekspedisi:</label>
        <select name="ekspedisi" required>
            <option value="">--Pilih--</option>
            <option value="JNE">JNE</option>
            <option value="TIKI">TIKI</option>
            <option value="Sicepat">Sicepat</option>
        </select><br><br>

        <label>Metode Pembayaran:</label>
        <select name="jenis_pembayaran" id="jenis_pembayaran" required>
            <option value="">--Pilih--</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="COD">COD</option>
        </select><br><br>

        <label>Bank:</label>
        <select name="bank" id="bank_select" disabled>
            <option value="">--Pilih Bank--</option>
            <option value="BCA">BCA</option>
            <option value="BRI">BRI</option>
            <option value="BJB">BJB</option>
            <option value="BNI">BNI</option>
        </select><br><br>

        <button type="submit" name="bayar">Bayar Sekarang</button>
    </form>

    <script>
    const jenis = document.getElementById('jenis_pembayaran');
    const bank = document.getElementById('bank_select');
    jenis.addEventListener('change', function() {
        bank.disabled = this.value !== "Bank Transfer";
        if (bank.disabled) bank.value = "";
    });

    // --- Fungsi Validasi Form ---
    function validateForm() {
        const form = document.forms[0];

        const fieldsNoNumbers = ['kelurahan', 'kecamatan', 'kota', 'patokan', 'nama_penerima'];
        const fieldsNumbers = ['kode_pos', 'no_telp'];

        // Validasi hanya angka
        for (let f of fieldsNumbers) {
            if (!/^\d+$/.test(form[f].value)) {
                alert(f.replace('_', ' ') + " harus berupa angka!");
                form[f].focus();
                return false;
            }
        }

        // Validasi tidak boleh angka
        for (let f of fieldsNoNumbers) {
            if (/\d/.test(form[f].value)) {
                alert(f.replace('_', ' ') + " tidak boleh mengandung angka!");
                form[f].focus();
                return false;
            }
        }

        return true; // jika lolos validasi
    }
    </script>
</body>

</html>