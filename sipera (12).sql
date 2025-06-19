-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Jun 2025 pada 15.36
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipera`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `aduan`
--

CREATE TABLE `aduan` (
  `id` int(11) NOT NULL,
  `id_pengirim` int(11) NOT NULL,
  `role_pengirim` enum('penjual','pembeli') NOT NULL,
  `subjek` varchar(255) NOT NULL,
  `pesan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'belum dibaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `aduan`
--

INSERT INTO `aduan` (`id`, `id_pengirim`, `role_pengirim`, `subjek`, `pesan`, `tanggal`, `status`) VALUES
(38, 29, 'penjual', 'Tidak bisa upload bukti pembayaran', 'upload bukti pembayarannya terganggu', '2025-06-19 14:02:36', 'dibalas'),
(39, 29, 'penjual', 'Gagal Upload Ternak', 'ff', '2025-06-19 17:43:42', 'dibalas'),
(40, 28, 'pembeli', 'D', 'DDD', '2025-06-19 18:41:37', 'baru'),
(41, 28, 'pembeli', 'Tidak bisa upload bukti pembayaran', 'xxxx', '2025-06-19 19:17:11', 'dibalas'),
(42, 29, 'penjual', 'Gagal Upload Ternak', 'xxxx', '2025-06-19 19:19:16', 'dibalas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `balasan`
--

CREATE TABLE `balasan` (
  `id` int(11) NOT NULL,
  `id_aduan` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `isi` text NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `balasan_aduan`
--

CREATE TABLE `balasan_aduan` (
  `id` int(11) NOT NULL,
  `id_aduan` int(11) NOT NULL,
  `pengirim` varchar(50) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `balasan_aduan`
--

INSERT INTO `balasan_aduan` (`id`, `id_aduan`, `pengirim`, `pesan`, `tanggal`) VALUES
(53, 38, 'admin', 'pp', '2025-06-19 14:04:13'),
(54, 38, 'penjual', 'pp', '2025-06-19 14:04:30'),
(55, 38, 'penjual', 'ii', '2025-06-19 15:17:39'),
(56, 39, 'admin', 'dd', '2025-06-19 17:43:57'),
(57, 39, 'admin', 'g', '2025-06-19 17:52:45'),
(58, 39, 'admin', 'K', '2025-06-19 17:59:57'),
(59, 41, 'pembeli', 'xxxxx', '2025-06-19 19:17:27'),
(60, 41, 'admin', 'xxxxx', '2025-06-19 19:17:48'),
(61, 42, 'admin', 'okay', '2025-06-19 19:19:34'),
(62, 42, 'penjual', 'oke', '2025-06-19 19:19:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `pengirim_id` int(11) NOT NULL,
  `penerima_id` int(11) NOT NULL,
  `id_ternak` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `waktu_kirim` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `chat`
--

INSERT INTO `chat` (`id`, `pengirim_id`, `penerima_id`, `id_ternak`, `pesan`, `created_at`, `is_read`, `waktu_kirim`) VALUES
(136, 28, 29, 48, 'Halo, saya tertarik dengan ternak ini. Apakah masih tersedia?', '2025-06-19 07:46:42', 0, '2025-06-19 15:54:39'),
(137, 28, 29, 48, 'p', '2025-06-19 07:46:51', 0, '2025-06-19 15:54:39'),
(138, 29, 28, 48, 'pp', '2025-06-19 07:52:29', 0, '2025-06-19 15:54:39'),
(139, 29, 28, 48, 'p', '2025-06-19 08:10:02', 0, '2025-06-19 16:10:02'),
(140, 28, 29, 48, 'Halo, saya tertarik dengan ternak ini. Apakah masih tersedia?', '2025-06-19 10:51:36', 0, '2025-06-19 18:51:36'),
(141, 28, 29, 18, 'Halo, saya tertarik dengan ternak ini. Apakah masih tersedia?', '2025-06-19 10:51:42', 0, '2025-06-19 18:51:42'),
(142, 29, 28, 18, 'halo', '2025-06-19 11:01:41', 0, '2025-06-19 19:01:41'),
(143, 28, 29, 16, 'Halo, saya tertarik dengan ternak ini. Apakah masih tersedia?', '2025-06-19 11:24:46', 0, '2025-06-19 19:24:46'),
(144, 29, 28, 16, 'halo', '2025-06-19 11:25:04', 0, '2025-06-19 19:25:04'),
(145, 28, 29, 16, 'hay', '2025-06-19 11:25:12', 0, '2025-06-19 19:25:12'),
(146, 29, 28, 16, 'okay', '2025-06-19 11:25:18', 0, '2025-06-19 19:25:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_konsultasi`
--

CREATE TABLE `chat_konsultasi` (
  `id` int(11) NOT NULL,
  `id_konsultasi` int(11) NOT NULL,
  `pengirim` enum('penjual','dokter') NOT NULL,
  `pesan` text NOT NULL,
  `waktu_kirim` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `chat_konsultasi`
--

INSERT INTO `chat_konsultasi` (`id`, `id_konsultasi`, `pengirim`, `pesan`, `waktu_kirim`, `created_at`) VALUES
(37, 30, 'penjual', 'halo dok', '2025-06-19 02:40:14', '2025-06-19 02:40:14'),
(38, 31, 'penjual', 'ppp', '2025-06-19 14:39:54', '2025-06-19 14:39:54'),
(39, 32, 'penjual', 'ppp', '2025-06-19 14:40:38', '2025-06-19 14:40:38'),
(40, 33, 'penjual', 'ppp', '2025-06-19 14:41:07', '2025-06-19 14:41:07'),
(41, 34, 'penjual', 'ppp', '2025-06-19 14:41:30', '2025-06-19 14:41:30'),
(42, 35, 'penjual', 'ppp', '2025-06-19 14:41:49', '2025-06-19 14:41:49'),
(43, 36, 'penjual', 'ppp', '2025-06-19 14:42:07', '2025-06-19 14:42:07'),
(44, 36, 'penjual', 'pp', '2025-06-19 14:50:26', '2025-06-19 14:50:26'),
(45, 36, 'dokter', 'k', '2025-06-19 17:03:51', '2025-06-19 17:03:51'),
(46, 37, 'penjual', 'pppp', '2025-06-19 17:05:23', '2025-06-19 17:05:23'),
(47, 38, 'penjual', 'bbb', '2025-06-19 17:06:07', '2025-06-19 17:06:07'),
(48, 38, 'penjual', 'ppp', '2025-06-19 17:06:50', '2025-06-19 17:06:50'),
(49, 38, 'penjual', 'oi', '2025-06-19 17:07:04', '2025-06-19 17:07:04'),
(50, 38, 'penjual', 'dokter', '2025-06-19 17:07:11', '2025-06-19 17:07:11'),
(51, 38, 'dokter', 'opale', '2025-06-19 17:07:20', '2025-06-19 17:07:20'),
(52, 38, 'dokter', 'p', '2025-06-19 17:08:36', '2025-06-19 17:08:36'),
(53, 39, 'penjual', 'xxxxxx', '2025-06-19 19:21:41', '2025-06-19 19:21:41'),
(54, 39, 'penjual', 'xxxx', '2025-06-19 19:22:11', '2025-06-19 19:22:11'),
(55, 39, 'dokter', 'apa', '2025-06-19 19:22:31', '2025-06-19 19:22:31'),
(56, 39, 'penjual', 'iya', '2025-06-19 19:22:36', '2025-06-19 19:22:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `pengirim` enum('penjual','dokter') NOT NULL,
  `pesan` text DEFAULT NULL,
  `balasan` text DEFAULT NULL,
  `status` enum('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `konsultasi`
--

INSERT INTO `konsultasi` (`id`, `id_user`, `id_dokter`, `pengirim`, `pesan`, `balasan`, `status`, `foto`, `created_at`) VALUES
(28, 29, 35, 'penjual', 'halo dok', NULL, 'baru', NULL, '2025-06-18 18:05:46'),
(30, 29, 35, 'penjual', 'halo dok', NULL, 'baru', NULL, '2025-06-18 18:40:14'),
(31, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:39:54'),
(32, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:40:38'),
(33, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:41:07'),
(34, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:41:30'),
(35, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:41:49'),
(36, 29, 35, 'penjual', 'ppp', NULL, 'baru', NULL, '2025-06-19 06:42:07'),
(37, 29, 35, 'penjual', 'pppp', NULL, 'baru', NULL, '2025-06-19 09:05:23'),
(38, 29, 35, 'penjual', 'bbb', NULL, 'baru', NULL, '2025-06-19 09:06:07'),
(39, 29, 35, 'penjual', 'xxxxxx', NULL, 'baru', NULL, '2025-06-19 11:21:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsultasi_balas`
--

CREATE TABLE `konsultasi_balas` (
  `id` int(11) NOT NULL,
  `id_konsultasi` int(11) NOT NULL,
  `id_pengirim` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `konsultasi_balas`
--

INSERT INTO `konsultasi_balas` (`id`, `id_konsultasi`, `id_pengirim`, `pesan`, `foto`, `created_at`) VALUES
(3, 28, 35, 'halo', '', '2025-06-18 18:06:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('baru','dibaca') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL,
  `id_pembeli` int(11) DEFAULT NULL,
  `id_ternak` int(11) DEFAULT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `tanggal_kunjungan` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id`, `id_pembeli`, `id_ternak`, `jenis`, `foto`, `jumlah`, `metode`, `tanggal_kunjungan`, `catatan`, `total_harga`, `status`, `bukti_pembayaran`) VALUES
(9, 28, 4, 'ayam kampung', 'jpg/683340da6265d.jpg', 1, 'Tunai', '2025-06-01', 'pastikan ayamnya sehat', 500000, 'Dikonfirmasi', NULL),
(11, 28, 3, 'babi', 'jpg/6833403cce491.jpg', 2, 'Tunai', '2025-06-10', 'pastikan babinya sesuai', 20000000, 'Dikonfirmasi', NULL),
(30, 28, 47, 'kambing', 'jpg/68524dd926ba7.jpg', 1, 'DANA', '1111-11-11', 'xxxx', 1000000, 'Menunggu Konfirmasi', 'bukti_1750224516_5869.jpg'),
(32, 28, 48, 'ayam kampung', 'jpg/68526442472f4.jpg', 1, 'DANA', '1111-11-11', 'pastingan ayamnya sesuai dengan informasi yang ada', 2000000, 'Dikonfirmasi', 'bukti_1750230227_3978.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ternak`
--

CREATE TABLE `ternak` (
  `id` int(11) NOT NULL,
  `id_penjual` int(11) DEFAULT NULL,
  `jenis` enum('kerbau','babi','kambing','ayam kampung') DEFAULT NULL,
  `usia` int(11) DEFAULT NULL,
  `harga` bigint(20) DEFAULT NULL,
  `berat` varchar(50) DEFAULT NULL,
  `status_kesehatan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `sertifikat` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stok_awal` int(11) DEFAULT 0,
  `stok_sisa` int(11) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `ternak`
--

INSERT INTO `ternak` (`id`, `id_penjual`, `jenis`, `usia`, `harga`, `berat`, `status_kesehatan`, `foto`, `sertifikat`, `deskripsi`, `created_at`, `stok_awal`, `stok_sisa`, `deleted_at`) VALUES
(3, 29, 'babi', 3, 10000000, 'gemuk dan berisi', 'SEHAT', 'public/jpg/ternak_1750263866.jpg', 'jpg/6833403cce496_sertifikat.png', 'Babi berwarna pink pastinya lucu dan sangat gemoy', '2025-05-25 16:07:24', 25, 20, NULL),
(4, 29, 'ayam kampung', 1, 300000, 'Gemuk ', 'SEHAT', 'public/jpg/ternak_1750263821.jpg', 'jpg/683340da62661_sertifikat.png', 'Ayam ini lucu dan gemoy', '2025-05-25 16:10:02', 10, 5, NULL),
(5, 29, 'kerbau', 6, 100000000, 'Gemuk dan berisi', 'SEHAT', 'public/jpg/ternak_1750263788.jpg', 'jpg/683341159618d_sertifikat.png', 'Kerbau ini adalah kerbau saleko yang cantik dan gemoy', '2025-05-25 16:11:01', 15, 7, NULL),
(16, 29, 'kerbau', 3, 40000000, 'Sedikit kurus', 'SEHAT', 'public/jpg/ternak_1750263765.jpg', 'jpg/683840dfd0f39_sertifikat.png', 'ini adalah tedong/kerbau pudu', '2025-05-29 11:11:27', 25, 14, NULL),
(18, 29, 'kambing', 3, 6000000, 'gemuk dan berisi', 'SEHAT', 'public/jpg/ternak_1750263743.jpg', 'jpg/6839e131721ed_sertifikat.png', 'kambing ini sangat comel dan gemoy', '2025-05-30 16:47:45', 25, 18, NULL),
(48, 29, 'ayam kampung', 4, 2000000, NULL, 'SEHAT', 'public/jpg/ternak_1750296527.jpg', NULL, 'ayam ini paling diminati banyak orang', '2025-06-18 07:01:22', 10, 4, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_pembeli` int(11) DEFAULT NULL,
  `id_ternak` int(11) DEFAULT NULL,
  `metode_pembayaran` enum('DANA','COD') DEFAULT NULL,
  `status` enum('pending','dibayar','ditolak') DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `jadwal_kunjungan` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi1`
--

CREATE TABLE `transaksi1` (
  `id` int(11) NOT NULL,
  `id_pembeli` int(11) DEFAULT NULL,
  `id_ternak` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `total_harga` double DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `kode_unik` varchar(10) DEFAULT NULL,
  `tanggal_kunjungan` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','penjual','pembeli','dokter') DEFAULT NULL,
  `verifikasi` tinyint(1) DEFAULT 0,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `str_sip` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `verifikasi`, `foto_ktp`, `str_sip`, `alamat`, `created_at`, `foto`) VALUES
(28, 'Evaleona Palembangan', 'evaa@gmail.com', '085399754794', '$2y$10$GGluCfuf0U2JILpB5bjG/eI90rBodD9MCLC2SPAxUs8xxWSxPG5uW', 'pembeli', 1, 'public/uploads/68322631abb1d.jpg', NULL, 'Jl.Balaikota', '2025-05-24 20:04:01', 'foto_6853cc00db6e4.jpg'),
(29, 'Qhaylha Sahara Putri', 'kela@gmail.com', '085599758870', '$2y$10$NZ3IsvbeDn5jV0HRsAdQ.O1Gmprkpa8bH/lA0ZSZwHmyVyljc4PSS', 'penjual', 1, 'public/uploads/68322c4619f6f.png', NULL, 'Jl.Jendral Sudirman', '2025-05-24 20:29:58', '1750330946kela.png.jpg'),
(34, 'Admin SIPERA', 'sipera@admin.com', NULL, '$2y$10$Jh9nanKZ.ZiA/6rTm3BKr.Gmsb5iVst4uGFI8fmy4xhgdEz4U8oim', 'admin', 1, NULL, NULL, NULL, '2025-05-26 12:11:14', NULL),
(35, 'Dr.Olivia', 'olivia@gmail.com', '0853997494477', '$2y$10$9BTnlUbKoSKwPVcO30r3VuRtZxToRKAPR9DRUcD9qNDggKWPdLlbm', 'dokter', 1, 'public/uploads/684c3ddc8c405.png', NULL, 'Jl.Ahmad Yani', '2025-06-13 15:03:56', '6853f0756f4b7_684c3e76317fc_olivia.jpg.jpg'),
(36, 'PUTRI', 'putri@gmail.com', '099101', '$2y$10$1x7wSMQkQz1O3fy6ZYcpmegLtGX1qUEJW/VyfX6DiOMhFpvOk2fIu', 'dokter', 0, 'public/uploads/6853e003923d0.jpg', NULL, 'kk', '2025-06-19 10:01:39', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `aduan`
--
ALTER TABLE `aduan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `balasan`
--
ALTER TABLE `balasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aduan` (`id_aduan`);

--
-- Indeks untuk tabel `balasan_aduan`
--
ALTER TABLE `balasan_aduan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aduan` (`id_aduan`);

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chat_pengirim` (`pengirim_id`),
  ADD KEY `fk_chat_penerima` (`penerima_id`),
  ADD KEY `fk_chat_ternak` (`id_ternak`);

--
-- Indeks untuk tabel `chat_konsultasi`
--
ALTER TABLE `chat_konsultasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_konsultasi` (`id_konsultasi`);

--
-- Indeks untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_konsultasi_user` (`id_user`),
  ADD KEY `fk_konsultasi_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `konsultasi_balas`
--
ALTER TABLE `konsultasi_balas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_konsultasi` (`id_konsultasi`),
  ADD KEY `id_pengirim` (`id_pengirim`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ternak`
--
ALTER TABLE `ternak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ternak_penjual` (`id_penjual`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaksi_pembeli` (`id_pembeli`),
  ADD KEY `fk_transaksi_ternak` (`id_ternak`);

--
-- Indeks untuk tabel `transaksi1`
--
ALTER TABLE `transaksi1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaksi1_pembeli` (`id_pembeli`),
  ADD KEY `fk_transaksi1_ternak` (`id_ternak`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `aduan`
--
ALTER TABLE `aduan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `balasan`
--
ALTER TABLE `balasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `balasan_aduan`
--
ALTER TABLE `balasan_aduan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT untuk tabel `chat_konsultasi`
--
ALTER TABLE `chat_konsultasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `konsultasi_balas`
--
ALTER TABLE `konsultasi_balas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `ternak`
--
ALTER TABLE `ternak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaksi1`
--
ALTER TABLE `transaksi1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `balasan`
--
ALTER TABLE `balasan`
  ADD CONSTRAINT `balasan_ibfk_1` FOREIGN KEY (`id_aduan`) REFERENCES `aduan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `balasan_aduan`
--
ALTER TABLE `balasan_aduan`
  ADD CONSTRAINT `balasan_aduan_ibfk_1` FOREIGN KEY (`id_aduan`) REFERENCES `aduan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`penerima_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chat_ibfk_3` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`),
  ADD CONSTRAINT `fk_chat_penerima` FOREIGN KEY (`penerima_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chat_pengirim` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chat_ternak` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `chat_konsultasi`
--
ALTER TABLE `chat_konsultasi`
  ADD CONSTRAINT `chat_konsultasi_ibfk_1` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD CONSTRAINT `fk_konsultasi_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_konsultasi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `konsultasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `konsultasi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `konsultasi_balas`
--
ALTER TABLE `konsultasi_balas`
  ADD CONSTRAINT `konsultasi_balas_ibfk_1` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id`),
  ADD CONSTRAINT `konsultasi_balas_ibfk_2` FOREIGN KEY (`id_pengirim`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `ternak`
--
ALTER TABLE `ternak`
  ADD CONSTRAINT `fk_ternak_penjual` FOREIGN KEY (`id_penjual`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ternak_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_pembeli` FOREIGN KEY (`id_pembeli`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transaksi_ternak` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi1`
--
ALTER TABLE `transaksi1`
  ADD CONSTRAINT `fk_transaksi1_pembeli` FOREIGN KEY (`id_pembeli`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transaksi1_ternak` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi1_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi1_ibfk_2` FOREIGN KEY (`id_ternak`) REFERENCES `ternak` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
