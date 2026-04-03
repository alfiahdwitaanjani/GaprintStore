<?php
include "config.php";

// ambil keyword dari search
$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

// buat query dinamis
$whereClause = "";
if (!empty($keyword)) {
    $whereClause = " AND (
        judul_buku LIKE '%$keyword%' OR 
        harga LIKE '%$keyword%' OR 
        terjual LIKE '%$keyword%' OR 
        stok LIKE '%$keyword%' OR 
        jenis_buku LIKE '%$keyword%' OR 
        nama_pengarang LIKE '%$keyword%' OR 
        halaman LIKE '%$keyword%' OR 
        bahasa LIKE '%$keyword%'
    )";
}

$notifCount = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM np WHERE status='baru'");
$notifData = mysqli_fetch_assoc($notifCount);
$jumlahNotif = $notifData['jml'];

// Flash Sale
$flashQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE jenis_harga_buku='flash sale' $whereClause ORDER BY id_stok DESC");

// Normal / null
$normalQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE (jenis_harga_buku IS NULL OR jenis_harga_buku='Normal') $whereClause ORDER BY id_stok DESC");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAPRINT STORE</title>
    <link rel="stylesheet" href="css/tampilan1.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAPRINT STORE</title>
    <link rel="stylesheet" href="css/tampilan1.css">
    <link rel="icon" type="image/png" href="images/logo.jpeg">
</head>

<body>
    <!-- TOP BAR -->
    <div class="topbar">
        <div class="container">
            <div>
                <span onclick="alert('Aplikasi belum dapat di download')" style="cursor:pointer; color:white;">
                    Download •
                </span>
                <a href="https://www.instagram.com/geloraprinting.id" target="_blank"
                    style="color:white; text-decoration:none;">
                    Ikuti Kami </a>
            </div>
            <div>
                <a href="bantuan.html" style="color: white; text-decoration:none;"> Bantuan</a>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header">
        <div class="container">
            <div class="logo-wrapper">
                <img src="images/tampilan1.png" class="logo-img" alt="Logo GAPRINT STORE">
                <div class="logo-text"></div>
            </div>

            <!-- SEARCH BOX dengan icon -->
            <div class="search-box ">

                <!-- icon search -->
                <div class="search-wrapper">
                    <form method="GET" action="" id="searchForm">
                        <input type="text" name="q" placeholder="Cari produk..."
                            value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-icon">🔍</button>
                    </form>
                </div>

                <!-- NOTIFIKASI ICON -->
                <span class="notif-icon" onclick="openCartPopup()" style="position:relative; cursor:pointer;">
                    🔔

                    <?php if ($jumlahNotif > 0) { ?>
                    <span class="notif-badge"><?php echo $jumlahNotif; ?></span>
                    <?php } ?>
                </span>

                <span class="cart-badge">
                    <?php echo $jumlahNotif; ?>
                </span>


                <!-- AKUN ICON -->
                <div class="account-wrapper">
                    <span class="account-icon" onclick="togglemenu()">☰</span>

                    <!-- DROPDOWN -->
                    <div class="account-menu" id="accountMenu">
                        <a href="hupenjual.php">Produk</a>
                        <a href="payment_produk.php">Payment</a>
                    </div>
                </div>


            </div>
        </div>

        <!-- FLASH SALE SECTION -->
        <div class="product-section flash-sale-box">
            <h3>🔥 FLASH SALE</h3>
            <div class="product-grid">
                <div class="product-card3">
                    <div class="product-scroll-container">
                        <?php if (mysqli_num_rows($flashQuery) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($flashQuery)) { ?>
                        <a href="update_produk.php?id=<?php echo $row['id_stok']; ?>" class="product-link">
                            <div class="product-item">
                                <img src="uploads/<?php echo $row['foto']; ?>">
                                <h4><?php echo $row['judul_buku']; ?></h4>
                                <b>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></b>
                                <span>Terjual: <?php echo $row['terjual']; ?></span>
                            </div>
                        </a>
                        <?php } ?>
                        <?php else: ?>
                        <div class="not-found">Buku tidak ditemukan 🔍</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUCT SECTION -->
        <div class="product-section list-box">
            <div class="grid-produk">
                <?php if (mysqli_num_rows($normalQuery) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($normalQuery)) { ?>
                <a href="update_produk.php?id=<?php echo $p['id_stok']; ?>" style="text-decoration:none;color:inherit;">
                    <div class="product-item">
                        <img src="uploads/<?php echo $p['foto']; ?>">
                        <h4><?php echo $p['judul_buku']; ?></h4>
                        <b>Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></b>
                        <span>Terjual: <?php echo $p['terjual']; ?></span>
                    </div>
                </a>
                <?php } ?>
                <?php else: ?>
                <div class="not-found">Buku tidak ditemukan 🔍</div>
                <?php endif; ?>
            </div>
        </div>

        <div id="cartPopup" class="popup" style="display:none;">
            <div class="popup-box">
                <h3>Notifikasi</h3>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM np WHERE status='baru' ORDER BY id DESC");

                if (mysqli_num_rows($q) > 0) {
                    while ($n = mysqli_fetch_assoc($q)) {
                        echo "<p>🔔 " . $n['pesan'] . "</p>";
                    }
                } else {
                    echo "<p>Tidak ada notifikasi baru</p>";
                }

                // tandai sudah dibaca
                mysqli_query($conn, "UPDATE np SET status='dibaca' WHERE status='baru'");
                ?>
            </div>
        </div>

        <!-- FLOATING BUTTON TAMBAH PRODUK -->
        <a href="upload_buku.php" class="floating-add">+</a>

        <script>
        function togglemenu() {
            let menu = document.getElementById("accountMenu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        /* tutup kalau klik di luar menu */
        document.addEventListener("click", function(e) {
            let menu = document.getElementById("accountMenu");
            let icon = document.querySelector(".account-icon");

            if (!menu.contains(e.target) && !icon.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        function openCartPopup() {
            document.getElementById("cartPopup").style.display = "flex";
        }

        document.addEventListener("click", function(e) {

            let popup = document.getElementById("cartPopup");
            let bell = document.querySelector(".notif-icon");
            let box = document.querySelector(".popup-box");

            // kalau popup tidak aktif → abaikan
            if (popup.style.display !== "flex") return;

            // klik ikon lonceng atau isi kotak → jangan tutup
            if (box.contains(e.target) || bell.contains(e.target)) return;

            // selain itu → tutup popup
            popup.style.display = "none";
        });
        </script>
</body>

</html>