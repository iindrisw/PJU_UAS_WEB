-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2026 pada 08.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pju`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'bcrypt hash',
  `email` varchar(150) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `email`, `avatar`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@pju.go.id', NULL, 'superadmin', NULL, '2026-05-29 05:30:13', '2026-05-29 05:30:13'),
(2, 'Indri', 'indri', '$2y$10$f6pXhMeeB31m3p4i8D9y1unZ.0YshlGscG5UfVvJ736CGe7qHnLmy', 'indri@pju.go.id', NULL, 'admin', NULL, '2026-05-29 06:41:00', '2026-05-29 06:42:45'),
(3, 'atmin', 'admin crb', '$2y$10$KaxhQA3ylaCF64tc9flGYOCB457D4mxHwnjqj/FO.Dgk6Qqcwjl9C', '', NULL, 'admin', NULL, '2026-05-29 06:43:46', '2026-05-29 06:44:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_laporan` varchar(20) NOT NULL COMMENT 'Format: PJU-YYYYMMDD-XXXX',
  `nama_pelapor` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `alamat_lokasi` varchar(500) DEFAULT NULL,
  `status` enum('menunggu','diproses','dalam_perjalanan','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `teknisi_id` int(10) UNSIGNED DEFAULT NULL,
  `estimasi_selesai` datetime DEFAULT NULL,
  `catatan_teknisi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan`
--

INSERT INTO `laporan` (`id`, `kode_laporan`, `nama_pelapor`, `no_hp`, `deskripsi`, `foto`, `latitude`, `longitude`, `alamat_lokasi`, `status`, `teknisi_id`, `estimasi_selesai`, `catatan_teknisi`, `created_at`, `updated_at`) VALUES
(1, 'PJU-20240601-0001', 'Ahmad Fauzi', '081111111111', 'Lampu jalan depan pasar mati sejak 3 hari lalu', NULL, -6.91750000, 107.61910000, 'Jl. Pasar Baru, Bandung', 'selesai', 1, NULL, NULL, '2024-06-01 01:00:00', '2026-05-29 05:30:13'),
(2, 'PJU-20240615-0002', 'Siti Rahayu', '082222222222', 'Lampu mati total, jalan sangat gelap berbahaya', NULL, -6.92180000, 107.60740000, 'Jl. Asia Afrika, Bandung', 'diproses', 2, NULL, NULL, '2024-06-15 07:30:00', '2026-05-29 05:30:13'),
(3, 'PJU-20240620-0003', 'Rizky Amalia', '083333333333', 'Kedip-kedip tidak stabil sudah seminggu', NULL, -6.91490000, 107.62730000, 'Jl. Dago, Bandung', 'menunggu', NULL, NULL, NULL, '2024-06-20 02:15:00', '2026-05-29 05:30:13'),
(4, 'PJU-20240701-0004', 'Hendra Wijaya', '084444444444', 'Lampu mati dari 2 minggu yang lalu tidak ada tindakan', NULL, -6.93050000, 107.61470000, 'Jl. Soekarno Hatta, Bandung', 'dalam_perjalanan', 3, NULL, NULL, '2024-07-01 04:00:00', '2026-05-29 05:30:13'),
(5, 'PJU-20240710-0005', 'Dewi Lestari', '085555555555', 'Tiang lampu bengkok dan mati akibat kecelakaan', NULL, -6.92440000, 107.63830000, 'Jl. Ir. H. Juanda, Bandung', 'menunggu', NULL, NULL, NULL, '2024-07-10 09:45:00', '2026-05-29 05:30:13'),
(6, 'PJU-20260529-0001', 'setiawati indri', '08372187498', 'lampu jalan di depan surya bandung meledak', 'PJU_20260529135151_a9264338.jpg', -6.75837000, 108.47250000, 'Jalan Terdeteksi sekitar koordinat: -6.758370000000001, 108.47250000000001', 'menunggu', NULL, NULL, NULL, '2026-05-29 06:51:51', '2026-05-29 06:51:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `teknisi`
--

CREATE TABLE `teknisi` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `teknisi`
--

INSERT INTO `teknisi` (`id`, `nama`, `no_hp`, `email`, `alamat`, `status`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '081234567890', 'budi@pju.go.id', 'Jl. Merdeka No.1, Bandung', 'aktif', NULL, '2026-05-29 05:30:13', '2026-05-29 05:30:13'),
(2, 'Agus Pratama', '082345678901', 'agus@pju.go.id', 'Jl. Sudirman No.5, Bandung', 'aktif', NULL, '2026-05-29 05:30:13', '2026-05-29 05:30:13'),
(3, 'Deni Kurniawan', '083456789012', 'deni@pju.go.id', 'Jl. Gatot Subroto No.10, Bandung', 'aktif', NULL, '2026-05-29 05:30:13', '2026-05-29 05:30:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tracking_status`
--

CREATE TABLE `tracking_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `laporan_id` int(10) UNSIGNED NOT NULL,
  `status` enum('menunggu','diproses','dalam_perjalanan','selesai','dibatalkan') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `diubah_oleh` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tracking_status`
--

INSERT INTO `tracking_status` (`id`, `laporan_id`, `status`, `keterangan`, `diubah_oleh`, `created_at`) VALUES
(1, 1, 'menunggu', 'Laporan diterima sistem', 'System', '2024-06-01 01:00:00'),
(2, 1, 'diproses', 'Laporan ditugaskan ke teknisi Budi Santoso', 'Admin', '2024-06-01 02:00:00'),
(3, 1, 'dalam_perjalanan', 'Teknisi berangkat ke lokasi', 'Budi Santoso', '2024-06-01 03:00:00'),
(4, 1, 'selesai', 'Lampu berhasil diperbaiki, bola lampu diganti baru', 'Budi Santoso', '2024-06-01 07:00:00'),
(5, 2, 'menunggu', 'Laporan diterima sistem', 'System', '2024-06-15 07:30:00'),
(6, 2, 'diproses', 'Ditugaskan ke teknisi Agus Pratama', 'Admin', '2024-06-15 08:00:00'),
(7, 3, 'menunggu', 'Laporan diterima sistem', 'System', '2024-06-20 02:15:00'),
(8, 4, 'menunggu', 'Laporan diterima sistem', 'System', '2024-07-01 04:00:00'),
(9, 4, 'diproses', 'Ditugaskan ke teknisi Deni Kurniawan', 'Admin', '2024-07-01 05:00:00'),
(10, 4, 'dalam_perjalanan', 'Teknisi sedang dalam perjalanan ke lokasi', 'Deni Kurniawan', '2024-07-01 06:00:00'),
(11, 5, 'menunggu', 'Laporan diterima sistem', 'System', '2024-07-10 09:45:00'),
(12, 6, 'menunggu', 'Laporan berhasil terdaftar ke database', 'Masyarakat', '2026-05-29 06:51:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_laporan` (`kode_laporan`),
  ADD KEY `idx_kode_laporan` (`kode_laporan`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_teknisi_id` (`teknisi_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `teknisi`
--
ALTER TABLE `teknisi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tracking_status`
--
ALTER TABLE `tracking_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_laporan_id` (`laporan_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_no_hp` (`no_hp`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `teknisi`
--
ALTER TABLE `teknisi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tracking_status`
--
ALTER TABLE `tracking_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `fk_laporan_teknisi` FOREIGN KEY (`teknisi_id`) REFERENCES `teknisi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tracking_status`
--
ALTER TABLE `tracking_status`
  ADD CONSTRAINT `fk_tracking_laporan` FOREIGN KEY (`laporan_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
