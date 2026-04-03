<?php
session_start();
include "config.php";

// Ambil id_pengguna dari session login
$id_pengguna = $_SESSION['id_pengguna'] ?? null;
if (!$id_pengguna) {
    die("Silakan login terlebih dahulu.");
}

// Hitung jumlah item untuk badge
$q = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM keranjang WHERE id_pengguna='$id_pengguna'");
$notif = mysqli_fetch_assoc($q)['jml'] ?? 0;

// AMBIL KEYWORD SEARCH
$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

// BUILD WHERE CLAUSE DYNAMIC
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

// QUERY FLASH SALE
$flashQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE jenis_harga_buku='flash sale' $whereClause ORDER BY
id_stok DESC");

// QUERY NORMAL / NULL
$normalQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE (jenis_harga_buku IS NULL OR
jenis_harga_buku='Normal') $whereClause ORDER BY id_stok DESC");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAPRINT STORE</title>
    <link rel="stylesheet" href="css/hupembeli.css">
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

            <!-- diganti: login bisa diklik -->
            <div>
                <a href="bantuan.html" style="color: white; text-decoration:none;"> Bantuan</a>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header ">
        <?php if (isset($_SESSION['notif'])): ?>
        <div class="notif-toast">
            <?php echo $_SESSION['notif']; ?>
        </div>
        <?php unset($_SESSION['notif']);
        endif; ?>


        <div class="container ">
            <div class="logo-wrapper">
                <img src="images/tampilan1.png" class="logo-img" alt="Logo GAPRINT STORE">
                <div class="logo-text"></div>
            </div>

            <!-- SEARCH BOX dengan icon -->
            <div class="search-box ">

                <!-- icon search -->
                <div class="search-wrapper">
                    <form method="GET" action="">
                        <input type="text" name="q" placeholder="Cari produk..."
                            value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-icon">🔍</button>
                    </form>
                </div>

                <div class="icon-wrapper">
                    <!-- CART ICON -->
                    <span class="cart-icon notif-icon" onclick="toggleNotifPopup(event)">🔔</span>
                    <!-- POPUP NOTIF -->
                    <div id="notifPopup"
                        style="display:none; position:fixed; top:50px; right:20px; width:300px; max-height:400px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.2); padding:10px; z-index:9999;">
                        <ul id="notifList" style="list-style:none; margin:0; padding:0;"></ul>
                    </div>


                    <!-- CART ICON -->
                    <span class="cart-icon cart-btn" onclick="openCartPopup(event)">🛒</span>
                    <!-- BADGE ANGKA BIRU -->
                    <span class="cart-badge">
                        <?php echo $notif; ?>
                    </span>

                </div>
                <!-- POPUP KERANJANG -->
                <div id="cartPopup" class="popup" style="display:none;">
                    <div class="popup-content">
                        <?php
                        $isi = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_pengguna='$id_pengguna'");
                        if (mysqli_num_rows($isi) > 0):
                            while ($k = mysqli_fetch_assoc($isi)):
                                $total_harga = $k['jumlah_barang'] * $k['harga_barang'];
                        ?>
                        <form action="payment.php" method="post" class="cart-item">
                            <input type="hidden" name="id_stok" value="<?= $k['id_stok'] ?>">
                            <input type="hidden" name="jumlah_barang" value="<?= $k['jumlah_barang'] ?>">
                            <input type="hidden" name="harga_barang" value="<?= $k['harga_barang'] ?>">
                            <input type="hidden" name="nama_buku" value="<?= htmlspecialchars($k['nama_buku']) ?>">

                            <div class="cart-card">
                                <img src="uploads/<?= htmlspecialchars($k['foto']) ?>"
                                    alt="<?= htmlspecialchars($k['nama_buku']) ?>">
                                <div class="cart-info">
                                    <button type="submit"
                                        class="cart-title"><?= htmlspecialchars($k['nama_buku']) ?></button>
                                    <p>Jumlah: <?= $k['jumlah_barang'] ?> pcs</p>
                                    <p>Total: Rp <?= number_format($total_harga, 0, ',', '.') ?></p>
                                </div>
                            </div>
                        </form>
                        <?php
                            endwhile;
                        else:
                            echo "<p>Keranjang kosong</p>";
                        endif;
                        ?>
                    </div>
                </div>

                <!-- AKUN ICON -->
                <div class="account-wrapper">
                    <span class="account-icon" onclick="toggleAccount()">👤</span>

                    <!-- DROPDOWN -->
                    <div class="account-menu" id="accountMenu" style="display:none;">
                        <?php if (isset($_SESSION['name'])): ?>
                        <!-- User sudah login -->
                        <p style="margin:5px 10px; font-weight:bold;">Halo, <?= htmlspecialchars($_SESSION['name']) ?>!
                        </p>
                        <a href="profil.php">Profil Saya</a>
                        <a href="logout.php">Logout</a>
                        <?php else: ?>
                        <!-- Belum login -->
                        <a href="login.html">Login</a>
                        <a href="registrasi.html">Registrasi</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDER -->
        <div class="slider ">
            <div class="slides" id="slides ">
                <img src="images/iklan2.png">
                <img src="images/iklan3.jpeg">
            </div>

            <div class="dots ">
                <span onclick="goSlide(0) " id="d0 " class="active "></span>
                <span onclick="goSlide(1) " id="d1 "></span>
            </div>
        </div>

        <!-- FLASH SALE SECTION -->
        <div class="product-section flash-sale-box">
            <h3>🔥 FLASH SALE</h3>
            <div class="product-grid">
                <div class="product-card3">
                    <div class="product-scroll-container">

                        <?php if (mysqli_num_rows($flashQuery) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($flashQuery)): ?>
                        <a href="detail_buku.php?id=<?php echo $row['id_stok']; ?>" class="product-link">
                            <div class="product-item">
                                <img src="uploads/<?php echo $row['foto']; ?>">
                                <h4><?php echo $row['judul_buku']; ?></h4>
                                <b>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></b>
                                <span>Terjual: <?php echo $row['terjual']; ?></span>
                            </div>
                        </a>
                        <?php endwhile; ?>
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
                <?php while ($p = mysqli_fetch_assoc($normalQuery)): ?>
                <a href="detail_buku.php?id=<?php echo $p['id_stok']; ?>" style="text-decoration:none;color:inherit;">
                    <div class="product-item">
                        <img src="uploads/<?php echo $p['foto']; ?>">
                        <h4><?php echo $p['judul_buku']; ?></h4>
                        <b>Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></b>
                        <span>Terjual: <?php echo $p['terjual']; ?></span>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php else: ?>
                <div class="not-found">Buku tidak ditemukan 🔍</div>
                <?php endif; ?>
            </div>
        </div>
        <script>
        let notifQueue = [];

        async function loadNotif() {
            try {
                const res = await fetch("get_notif.php");
                const data = await res.json();

                // tambahkan ke queue notif baru
                notifQueue.push(...data);

                // tampilkan notif satu-satu jika popup terbuka
                if (document.getElementById("notifPopup").style.display === 'block') {
                    showNotif();
                }
            } catch (err) {
                console.error("Gagal load notif:", err);
            }
        }

        // fungsi tampilkan notif dari queue
        function showNotif() {
            const list = document.getElementById("notifList");

            while (notifQueue.length > 0) {
                const n = notifQueue.shift();
                const li = document.createElement("li");
                li.textContent = n;
                li.style.padding = "5px 0";
                li.style.borderBottom = "1px solid #eee";
                list.insertBefore(li, list.firstChild); // notif baru di atas
            }
        }

        // toggle popup
        function toggleNotifPopup(e) {
            e.stopPropagation();

            const popup = document.getElementById("notifPopup");
            popup.style.display = (popup.style.display === "block") ? "none" : "block";

            if (popup.style.display === "block") {
                showNotif();
            }
        }

        // polling setiap 3 detik
        setInterval(loadNotif, 3000);
        loadNotif(); // langsung load pertama kali

        // SLIDER SCRIPT
        let index = 0;

        function goSlide(i) {
            index = i;
            document.getElementById("slides ").style.transform = "translateX( " + (-1200 * i) + "px) ";
            document.querySelectorAll(".dots span ").forEach(d => d.classList.remove("active "));
            document.getElementById("d " + i).classList.add("active ");
        }

        setInterval(() => {
            index = (index + 1) % 2;
            goSlide(index);
        }, 3000);

        function toggleAccount() {
            let menu = document.getElementById("accountMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }

        // tutup kalau klik di luar menu
        document.addEventListener("click", function(e) {
            let menu = document.getElementById("accountMenu");
            let icon = document.querySelector(".account-icon");

            if (!menu.contains(e.target) && !icon.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        function openCartPopup(e) {
            e.stopPropagation();
            document.getElementById("cartPopup").style.display = "block";
        }

        function closeCartPopup() {
            document.getElementById("cartPopup").style.display = "none";
        }

        // Tutup popup saat klik di luar
        document.addEventListener("click", function() {
            const notifPopup = document.getElementById("notifPopup");
            const cartPopup = document.getElementById("cartPopup");

            if (notifPopup) notifPopup.style.display = "none";
            if (cartPopup) cartPopup.style.display = "none";
        });
        </script>
</body>

</html>