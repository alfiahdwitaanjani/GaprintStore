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

// notif
$notifCount = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM np WHERE status='baru'");
$notifData = mysqli_fetch_assoc($notifCount);
$jumlahNotif = $notifData['jml'];

// Flash Sale
$flashQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE jenis_harga_buku='flash sale' $whereClause ORDER BY id_stok DESC");

// Normal / null
$normalQuery = mysqli_query($conn, "SELECT * FROM stok_buku WHERE (jenis_harga_buku IS NULL OR jenis_harga_buku='Normal') $whereClause ORDER BY id_stok DESC");

// ambil distinct pilihan filter
$listJenisHarga = mysqli_query($conn, "SELECT DISTINCT jenis_harga_buku FROM stok_buku WHERE jenis_harga_buku IS NOT NULL");
$listJenisBuku  = mysqli_query($conn, "SELECT DISTINCT jenis_buku FROM stok_buku WHERE jenis_buku IS NOT NULL");
$listBahasa     = mysqli_query($conn, "SELECT DISTINCT bahasa FROM stok_buku WHERE bahasa IS NOT NULL");

// rating belum ada di DB
// jadi kita buat manual sementara
$ratingList = [5, 4, 3, 2, 1];
?>

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

            <!-- SEARCH BOX -->
            <div class="search-box">
                <div class="search-wrapper">
                    <form method="GET" action="" id="searchForm">
                        <input type="text" name="q" placeholder="Cari produk..."
                            value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit" class="search-icon">🔍</button>
                    </form>
                </div>

                <span class="notif-icon" onclick="openCartPopup()">🔔</span>
                <span class="cart-icon" onclick="openCartPopup()">🛒</span>

                <div class="account-wrapper">
                    <span class="account-icon" onclick="toggleAccount()">👤</span>
                    <div class="account-menu" id="accountMenu">
                        <a href="login.html">Login</a>
                        <a href="registrasi.html">Registrasi</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDER -->
        <div class="slider">
            <div class="slides" id="slides">
                <img src="images/iklan2.png">
                <img src="images/iklan3.jpeg">
            </div>
            <div class="dots">
                <span onclick="goSlide(0)" id="d0" class="active"></span>
                <span onclick="goSlide(1)" id="d1"></span>
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

        <!-- POPUP LOGIN -->
        <div id="cartPopup" class="popup-overlay">
            <div class="popup-box">
                <h3>Silahkan lakukan login terlebih dahulu</h3>
                <div class="popup-buttons">
                    <a href="login.html" class="btn-login">Login</a>
                    <a href="registrasi.html" class="btn-register">Registrasi</a>
                </div>
            </div>
        </div>

        <script>
        // ===== SLIDER =====
        let index = 0;

        function goSlide(i) {
            index = i;
            document.getElementById("slides").style.transform = "translateX(" + (-1200 * i) + "px)";
            document.querySelectorAll(".dots span").forEach(d => d.classList.remove("active"));
            document.getElementById("d" + i).classList.add("active");
        }
        setInterval(() => {
            goSlide((index + 1) % 2);
        }, 3000);

        // ===== ACCOUNT MENU =====
        function toggleAccount() {
            const menu = document.getElementById("accountMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
        document.addEventListener("click", function(e) {
            const menu = document.getElementById("accountMenu");
            const icon = document.querySelector(".account-icon");
            if (!menu.contains(e.target) && !icon.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        // ===== CART POPUP =====
        function openCartPopup() {
            document.getElementById("cartPopup").style.display = "flex";
        }
        document.getElementById("cartPopup").addEventListener("click", function() {
            this.style.display = "none";
        });

        // ===== PRICE VALIDATION =====
        const maxInput = document.getElementById("maxPrice");
        if (maxInput) {
            maxInput.addEventListener("change", function() {
                const minVal = parseInt(document.getElementById("minPrice").value || 0);
                const maxVal = parseInt(this.value || 0);
                if (maxVal < minVal) {
                    alert("Harga maksimum tidak boleh lebih kecil dari minimum");
                    this.value = minVal;
                }
            });
        }

        // ===== LEVEL SELECTION =====
        function selectLevel(btn, level) {
            document.querySelectorAll(".level-btn").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            document.getElementById("levelValue").value = level;
        }

        // tutup popup saat klik di luar kotak popup
        document.getElementById("cartPopup").addEventListener("click", function(e) {
            if (e.target === this) { // hanya overlay, bukan kotak
                this.style.display = "none";
            }
        });

        // bind semua icon keranjang
        document.querySelectorAll(".cart-icon").forEach(icon => {
            icon.addEventListener("click", openCartPopup);
        });

        function toggleFilter() {
            const panel = document.getElementById("filter-panel");
            if (!panel) return;
            if (panel.style.right === "-320px" || panel.style.right === "") {
                panel.style.right = "0px";
                panel.style.display = "block";
            } else {
                panel.style.right = "-320px";
                panel.style.display = "none";
            }
        }
        </script>
</body>

</html>