<?php
// =========================================================
// SETUP AWAL: jalankan file ini SATU KALI lewat browser
// untuk membuat akun admin & kasir default.
// Setelah sukses, HAPUS file ini dari server (demi keamanan).
// =========================================================
require_once 'config/koneksi.php';

$akun = [
    ['username' => 'admin', 'password' => 'admin123', 'nama' => 'Administrator', 'role' => 'admin'],
    ['username' => 'kasir', 'password' => 'kasir123', 'nama' => 'Kasir Satu',    'role' => 'kasir'],
];

$pesan = [];

foreach ($akun as $a) {
    // Cek apakah username sudah ada
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $a['username']);
    mysqli_stmt_execute($stmt);
    $ada = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($ada) {
        $pesan[] = "User '{$a['username']}' sudah ada, dilewati.";
        continue;
    }

    $hash = password_hash($a['password'], PASSWORD_BCRYPT);
    $stmt2 = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "ssss", $a['username'], $hash, $a['nama'], $a['role']);
    mysqli_stmt_execute($stmt2);

    $pesan[] = "User '{$a['username']}' berhasil dibuat (password: {$a['password']}).";
}
?>
<!DOCTYPE html>
<html>
<head><title>Setup Akun</title></head>
<body style="font-family:sans-serif; padding:30px;">
    <h2>Setup Akun Default</h2>
    <ul>
        <?php foreach ($pesan as $p) echo "<li>$p</li>"; ?>
    </ul>
    <p style="color:red;"><strong>PENTING:</strong> Hapus file setup_akun.php ini sekarang dari folder proyek Anda.</p>
    <p><a href="login.php">Lanjut ke halaman Login &raquo;</a></p>
</body>
</html>
