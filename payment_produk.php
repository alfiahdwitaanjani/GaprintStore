<?php
include "config.php";

$q = mysqli_query($conn, "SELECT * FROM payment ORDER BY id_payment DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $field = $_POST['field'];   // tracking1 / tracking2 / dst
    $value = $_POST['value'];   // nilai input tracking

    // validasi nama kolom agar aman
    $allowed = [
        'tracking1',
        'tracking2',
        'tracking3',
        'tracking4',
        'tracking5',
        'tracking6',
        'tracking7',
        'tracking8',
        'tracking9',
        'tracking10'
    ];

    if (in_array($field, $allowed)) {

        $id_payment = $_POST['id_payment'];


        mysqli_query($conn, "
            UPDATE payment SET $field='$value'
            WHERE id_payment='$id_payment'
        ");

        // reload agar langsung readonly
        echo "<meta http-equiv='refresh' content='0'>";
    }
}


?>


<!DOCTYPE html>
<html>

<head>
    <title>Payment Produk</title>
    <link rel="stylesheet" href="css/payment.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

</head>

<body>

    <h2>Payment Produk</h2>
    <div class="payment-container">

        <!-- KOLOM TETAP -->
        <div class="fixed-column">
            <table>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah Barang</th>
                    <th>Total Harga</th>
                    <th>Metode Pembayaran</th>
                    <th>Asal Bank</th>
                    <th>Ekspedisi</th>
                    <th>Download</th>
                    <th>Resi Barang</th>
                    <th>Tracking1</th>
                    <th>Tracking2</th>
                    <th>Tracking3</th>
                    <th>Tracking4</th>
                    <th>Tracking5</th>
                    <th>Tracking6</th>
                    <th>Tracking7</th>
                    <th>Tracking8</th>
                    <th>Tracking9</th>
                    <th>Tracking10</th>
                    <th>Status</th>
                </tr>

                <?php while ($p = mysqli_fetch_assoc($q)) { ?>
                <tr>
                    <td><?= $p["nama_buku"] ?></td>
                    <td><?= $p["jumlah_barang"] ?></td>
                    <td>Rp<?= number_format($p["total_harga"]) ?></td>
                    <td><?= $p["metode_pembayaran"] ?></td>
                    <td><?= $p["bank"] ?></td>
                    <td><?= $p["ekspedisi"] ?></td>

                    <!-- DOWNLOAD BUTTON -->
                    <td>
                        <button <?= $p["sudah_download"] == 1 ? "disabled" : "" ?>
                            onclick='buatDownload(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, this)'>
                            Download JPG
                        </button>

                    </td>

                    <!-- RESI (sekali input) -->
                    <td>
                        <?php if ($p["resi"] == NULL) { ?>
                        <form method="post" action="simpan_input.php">
                            <input type="hidden" name="id_payment" value="<?= $p["id_payment"] ?>">
                            <input type="text" name="resi" required>
                            <button>Simpan</button>
                        </form>
                        <?php } else echo $p["resi"]; ?>
                    </td>

                    <td>
                        <!-- TRACKING 1 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking1">

                            1.
                            <input type="text" name="value" value="<?= $p['tracking1']; ?>"
                                <?= !empty($p['tracking1']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking1']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 2 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking2">

                            2.
                            <input type="text" name="value" value="<?= $p['tracking2']; ?>"
                                <?= !empty($p['tracking2']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking2']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 3 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking3">

                            3.
                            <input type="text" name="value" value="<?= $p['tracking3']; ?>"
                                <?= !empty($p['tracking3']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking3']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 4 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking4">

                            4.
                            <input type="text" name="value" value="<?= $p['tracking4']; ?>"
                                <?= !empty($p['tracking4']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking4']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 5 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking5">

                            5.
                            <input type="text" name="value" value="<?= $p['tracking5']; ?>"
                                <?= !empty($p['tracking5']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking5']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 6 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking6">

                            6.
                            <input type="text" name="value" value="<?= $p['tracking6']; ?>"
                                <?= !empty($p['tracking6']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking6']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 7 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking7">

                            7.
                            <input type="text" name="value" value="<?= $p['tracking7']; ?>"
                                <?= !empty($p['tracking7']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking7']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 8 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking8">

                            8.
                            <input type="text" name="value" value="<?= $p['tracking8']; ?>"
                                <?= !empty($p['tracking8']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking8']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 9 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking9">

                            9.
                            <input type="text" name="value" value="<?= $p['tracking9']; ?>"
                                <?= !empty($p['tracking9']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking9']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- TRACKING 10 -->
                        <form method="post" style="margin-bottom:4px;">
                            <input type="hidden" name="id_payment" value="<?= $p['id_payment']; ?>">
                            <input type="hidden" name="field" value="tracking10">

                            10.
                            <input type="text" name="value" value="<?= $p['tracking10']; ?>"
                                <?= !empty($p['tracking10']) ? 'readonly' : '' ?>>

                            <button type="submit" <?= !empty($p['tracking10']) ? 'disabled' : '' ?>>Simpan</button>
                        </form>

                    </td>
                    <!-- STATUS -->
                    <td>
                        <form method="post" action="simpan_input.php">
                            <input type="hidden" name="id_payment" value="<?= $p["id_payment"] ?>">
                            <select name="status_pengiriman"
                                <?php if ($p["status_pengiriman"] != 'proses') echo "disabled"; ?>>
                                <option value="proses" <?= $p["status_pengiriman"] == 'proses' ? 'selected' : '' ?>>
                                    proses
                                </option>
                                <option value="sukses">sukses</option>
                            </select>
                            <?php if ($p["status_pengiriman"] == 'proses') { ?>
                            <button>Simpan</button>
                            <?php } ?>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <!-- AREA JPG TERSEMBUNYI -->
            <div id="areaDownload">
                <svg id="barcode"></svg>
                <div id="textDetail"></div>
            </div>
        </div>
        <script>
        async function buatDownload(d, btn) {

            // disable tombol agar tidak dobel klik
            btn.disabled = true;

            // pastikan nama field benar
            // (gunakan nama_buku jika itu yang ada di database)
            const namaBarang = d.nama_barang || d.nama_buku || "-";

            // BARCODE
            JsBarcode("#barcode", d.id_payment.toString(), {
                format: "code128",
                width: 2,
                height: 60,
                displayValue: true
            });

            // DETAIL
            // bikin text total harga hanya jika COD
            let totalHargaText = "";

            if (d.metode_pembayaran === "COD") {
                totalHargaText = `<b>Total Harga:</b> Rp${Number(d.total_harga).toLocaleString()}<br>`;
            }

            document.getElementById("textDetail").innerHTML = `
            <b>Nama Penerima:</b> ${d.nama_penerima}<br>
            <b>No Telp:</b> ${d.no_telp}<br>
            <b>Alamat:</b> ${d.alamat}<br>
            <b>Kota:</b> ${d.kota}<br>
            <b>Kode Pos:</b> ${d.kode_pos}<br><br>

            <b>Nama Barang:</b> ${namaBarang}<br>
            <b>Jumlah:</b> ${d.jumlah_barang}<br>
            <b>Metode Pembayaran:</b> ${d.metode_pembayaran}<br>
            ${totalHargaText}
            <b>Ekspedisi:</b> ${d.ekspedisi}<br>`;


            // beri waktu render 100ms
            await new Promise(r => setTimeout(r, 120));

            html2canvas(document.getElementById("areaDownload"), {
                    scale: 2
                })
                .then(canvas => {

                    let a = document.createElement("a");
                    a.href = canvas.toDataURL("image/jpeg");
                    a.download = "label_pengiriman_" + d.id_payment + ".jpg";
                    a.click();

                    // tandai sudah download
                    fetch("set_download.php?id=" + d.id_payment);
                });
        }
        </script>

</body>

</html>