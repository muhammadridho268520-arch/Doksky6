<?php
require_once 'includes/cek_login.php';
require_once 'config/koneksi.php';

$judul_halaman = "Dashboard";

// --- Total transaksi hari ini ---
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) as jml, COALESCE(SUM(grand_total),0) as total
                               FROM transaksi WHERE DATE(tanggal) = CURDATE()");
$hariIni = mysqli_fetch_assoc($q1);

// --- Total transaksi bulan ini ---
$q2 = mysqli_query($koneksi, "SELECT COALESCE(SUM(grand_total),0) as total
                               FROM transaksi WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$bulanIni = mysqli_fetch_assoc($q2);

// --- Jumlah produk & stok menipis (<10) ---
$q3 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM produk");
$jmlProduk = mysqli_fetch_assoc($q3)['jml'];

$q4 = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok < 10 ORDER BY stok ASC LIMIT 5");

// --- 5 transaksi terbaru ---
$q5 = mysqli_query($koneksi, "SELECT t.*, u.nama_lengkap FROM transaksi t
                               JOIN users u ON t.user_id = u.id
                               ORDER BY t.tanggal DESC LIMIT 5");

// --- Produk terlaris (top 5) ---
$q6 = mysqli_query($koneksi, "SELECT nama_produk, SUM(qty) as total_terjual
                               FROM transaksi_detail GROUP BY produk_id
                               ORDER BY total_terjual DESC LIMIT 5");

require_once 'includes/header.php';
?>

<div class="cards">
    <div class="card">
        <div class="card-title">Transaksi Hari Ini</div>
        <div class="card-value"><?php echo $hariIni['jml']; ?></div>
    </div>
    <div class="card">
        <div class="card-title">Pendapatan Hari Ini</div>
        <div class="card-value">Rp <?php echo number_format($hariIni['total'], 0, ',', '.'); ?></div>
    </div>
    <div class="card">
        <div class="card-title">Pendapatan Bulan Ini</div>
        <div class="card-value">Rp <?php echo number_format($bulanIni['total'], 0, ',', '.'); ?></div>
    </div>
    <div class="card">
        <div class="card-title">Total Produk</div>
        <div class="card-value"><?php echo $jmlProduk; ?></div>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <h3>Transaksi Terbaru</h3>
        <table class="table">
            <tr><th>No. Transaksi</th><th>Kasir</th><th>Total</th><th>Tanggal</th></tr>
            <?php while ($row = mysqli_fetch_assoc($q5)): ?>
            <tr>
                <td><?php echo $row['no_transaksi']; ?></td>
                <td><?php echo $row['nama_lengkap']; ?></td>
                <td>Rp <?php echo number_format($row['grand_total'],0,',','.'); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="panel">
        <h3>Stok Menipis (&lt; 10)</h3>
        <table class="table">
            <tr><th>Kode</th><th>Nama Produk</th><th>Stok</th></tr>
            <?php while ($row = mysqli_fetch_assoc($q4)): ?>
            <tr>
                <td><?php echo $row['kode_barang']; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td class="text-danger"><?php echo $row['stok']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<div class="panel">
    <h3>Produk Terlaris</h3>
    <table class="table">
        <tr><th>Nama Produk</th><th>Total Terjual</th></tr>
        <?php while ($row = mysqli_fetch_assoc($q6)): ?>
        <tr>
            <td><?php echo $row['nama_produk']; ?></td>
            <td><?php echo $row['total_terjual']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
