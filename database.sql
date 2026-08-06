-- =========================================================
-- DATABASE APLIKASI KASIR
-- Import file ini lewat phpMyAdmin atau: mysql -u root -p < database.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_kasir;
USE db_kasir;

-- ---------------------------------------------------------
-- TABEL USERS (untuk login, role: admin / kasir)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User default (admin/kasir) TIDAK di-insert di sini.
-- Setelah database ini di-import, buka file setup_akun.php di browser SATU KALI
-- untuk membuat akun admin & kasir dengan password ter-hash yang valid.
-- Setelah berhasil, HAPUS file setup_akun.php demi keamanan.

-- ---------------------------------------------------------
-- TABEL PRODUK
-- ---------------------------------------------------------
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(30) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) DEFAULT '-',
    harga_beli DECIMAL(12,2) NOT NULL DEFAULT 0,
    harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO produk (kode_barang, nama_produk, kategori, harga_beli, harga_jual, stok, gambar) VALUES
('BRG001', 'Indomie Goreng', 'Makanan', 2500, 3500, 100, NULL),
('BRG002', 'Aqua Botol 600ml', 'Minuman', 3000, 4000, 150, NULL),
('BRG003', 'Teh Pucuk 350ml', 'Minuman', 4000, 5500, 80, NULL);

-- ---------------------------------------------------------
-- TABEL TRANSAKSI (header)
-- ---------------------------------------------------------
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    total_belanja DECIMAL(12,2) NOT NULL DEFAULT 0,
    pajak DECIMAL(12,2) NOT NULL DEFAULT 0,
    grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    bayar DECIMAL(12,2) NOT NULL DEFAULT 0,
    kembalian DECIMAL(12,2) NOT NULL DEFAULT 0,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABEL DETAIL TRANSAKSI (isi keranjang per transaksi)
-- ---------------------------------------------------------
CREATE TABLE transaksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    produk_id INT NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB;
