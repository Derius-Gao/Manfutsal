-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Feb 2026 pada 16.28
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
-- Database: `manfut`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'settings_updated', 'Mengupdate pengaturan web', '127.0.0.1', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(2, 1, 'user_created', 'Membuat user baru: Derpyus', '127.0.0.1', '2026-02-03 09:36:57', '2026-02-03 09:36:57'),
(3, 1, 'user_deleted', 'Menghapus user: ada', '127.0.0.1', '2026-02-03 09:37:06', '2026-02-03 09:37:06'),
(4, 1, 'user_status_toggled', 'Mengubah status user: Mr. Donald Sipes', '127.0.0.1', '2026-02-03 09:37:10', '2026-02-03 09:37:10'),
(5, 1, 'user_updated', 'Mengupdate user: Pantai', '127.0.0.1', '2026-02-03 09:37:19', '2026-02-03 09:37:19'),
(6, 1, 'lapangan_created', 'Membuat lapangan baru: ATC FUTSAL', '127.0.0.1', '2026-02-03 09:38:00', '2026-02-03 09:38:00'),
(7, 1, 'lapangan_updated', 'Mengupdate lapangan: ATC FUTSAL', '127.0.0.1', '2026-02-04 07:14:08', '2026-02-04 07:14:08'),
(8, 4, 'booking_created', 'Membuat booking baru untuk ATC FUTSAL', '127.0.0.1', '2026-02-04 07:19:44', '2026-02-04 07:19:44'),
(9, 1, 'booking_completed', 'Menyelesaikan booking 1', '127.0.0.1', '2026-02-04 07:26:07', '2026-02-04 07:26:07'),
(10, 4, 'booking_created', 'Membuat booking baru untuk ATC FUTSAL', '127.0.0.1', '2026-02-04 07:39:47', '2026-02-04 07:39:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lapangan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `lapangan_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `total_harga`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2026-02-04', '07:00:00', '11:00:00', 400000.00, 'completed', 'ada', '2026-02-04 07:19:44', '2026-02-04 07:26:07'),
(2, 4, 1, '2026-02-09', '07:00:00', '11:00:00', 400000.00, 'pending', NULL, '2026-02-04 07:39:47', '2026-02-04 07:39:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lapangans`
--

CREATE TABLE `lapangans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `daerah` varchar(255) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `fasilitas` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `lapangans`
--

INSERT INTO `lapangans` (`id`, `nama`, `lokasi`, `daerah`, `kapasitas`, `harga_per_jam`, `fasilitas`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ATC FUTSAL', 'Jakarta', 'Jakarta', 10, 100000.00, 'Lampu', 'lapangan/8BYBmQLPPUFYWm5TqeeNaEP1QoW25q3RKuhZPeef.webp', 'aktif', '2026-02-03 09:38:00', '2026-02-04 07:14:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000003_create_lapangan_table', 1),
(5, '2024_01_01_000004_create_bookings_table', 1),
(6, '2024_01_01_000005_create_payments_table', 1),
(7, '2024_01_01_000006_create_activities_table', 1),
(8, '2026_02_03_154826_create_web_settings_table', 2),
(9, '2024_01_01_000001_create_user_permissions_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `metode_pembayaran` varchar(255) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `jumlah`, `metode_pembayaran`, `bukti_pembayaran`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 400000.00, 'cash', NULL, 'verified', NULL, '2026-02-04 07:19:44', '2026-02-04 07:19:44'),
(2, 2, 400000.00, 'transfer_bank', NULL, 'pending', NULL, '2026-02-04 07:39:47', '2026-02-04 07:39:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('enV48wPGeNO1vCUk45u2YpgKpw4o6csOC7NQ1gQE', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYjJDWGVpUnVhbUNBaHFYbkZRU3NlQXFGbHBnUm5FZFFsanZZVHhCaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hY2Nlc3MiO3M6NToicm91dGUiO3M6MTI6ImFjY2Vzcy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1770218673);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','manager','admin','superadmin') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `address`, `profile_photo`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@manfutsal.com', NULL, '$2y$12$DP5zcWthD6UPrJ0Yd4iPZes8thGAMMdfeT7ThVU8rLOmYviudOrAO', 'superadmin', '08123456789', 'Jakarta, Indonesia', NULL, 1, NULL, '2026-02-02 23:57:51', '2026-02-02 23:57:51'),
(2, 'Admin User', 'admin@manfutsal.com', NULL, '$2y$12$r/gIl79/x3J4bUODl4TYT.DMagDD9rwZ7MIlc7n//YgzmengYfm9C', 'admin', '08123456788', 'Jakarta, Indonesia', NULL, 1, NULL, '2026-02-02 23:57:52', '2026-02-02 23:57:52'),
(3, 'Manager User', 'manager@manfutsal.com', NULL, '$2y$12$zQs7EnUuFuFY463rP3dMfeFUWo/L1fimVo4FKIpIoJRFTi3R5UydO', 'manager', '08123456787', 'Jakarta, Indonesia', NULL, 1, NULL, '2026-02-02 23:57:52', '2026-02-02 23:57:52'),
(4, 'Rick Schultz IV', 'bgutkowski@example.com', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '302-919-2706', '14793 Marie Trail Suite 031\nFrederiquebury, FL 70669', NULL, 1, 'dl5jLqArWUFzHFbY9BNlH1acuufdJ0bXGIV8JUQRTUdLlpZxGJJEgPk8gPMF', '2026-02-02 23:57:53', '2026-02-02 23:57:53'),
(5, 'Pantai', 'janet54@example.net', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '+14178003940', '925 Larson Street\r\nEast Frankiestad, DC 97665-4992', NULL, 0, 'Z9iOepoWTJ', '2026-02-02 23:57:54', '2026-02-03 09:37:19'),
(6, 'Dr. Theo Leuschke DVM', 'gmann@example.com', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '949-932-3655', '66881 Orn Crossing\nSouth Destineymouth, IN 29204-7558', NULL, 1, 'rhwHL1WlkJ', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(7, 'Archibald Veum', 'greta16@example.net', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '+1 (762) 851-1875', '26135 Gutkowski Mall Suite 492\nPort Margie, MS 22093-1361', NULL, 1, 'SCQhND4DZh', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(8, 'Lamar McGlynn', 'roxanne75@example.net', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '1-352-844-6544', '1239 Fritsch Parkways\nBaumbachbury, WV 63558', NULL, 1, '0GzLycN94D', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(9, 'Dr. Buck Smitham', 'vivien.treutel@example.org', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '(973) 430-7353', '549 Ewell Valley\nEast Averyfurt, OK 72807-7282', NULL, 1, 'U2o3bbvA3i', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(10, 'Stefanie Carroll', 'breanne.casper@example.com', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '(843) 606-0149', '507 Sister Heights Suite 792\nWest Otis, UT 66749-8307', NULL, 1, 'wdFMjgqrCT', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(11, 'Retta Christiansen', 'jorge.robel@example.com', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '+16784310677', '265 Alexa Shoal Suite 222\nEast Juddview, NE 07533-9214', NULL, 1, 'RZRuA9T6i9', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(12, 'Fae Wintheiser', 'meda.maggio@example.com', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '+1.219.374.7894', '4549 Pouros Point Apt. 051\nSouth Annamarieton, WY 27223', NULL, 1, 'ZBYkoDm0gt', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(13, 'Prof. Daren Johnston', 'brady.brakus@example.org', '2026-02-02 23:57:53', '$2y$12$TJWYBB1wKf33RuWTC.Ucqe0ZutDc2yFe0Ht2uXKx.lUFnUVp2ggHu', 'customer', '737.358.5258', '18496 Kuhn Locks Suite 343\nSchuppefurt, TX 33952-7400', NULL, 1, 'GHAMJtaOVX', '2026-02-02 23:57:54', '2026-02-02 23:57:54'),
(16, 'Derpyus', 'derpyusz@gmail.com', NULL, '$2y$12$pL4CLBbgQgLUKuX8MISFPepB4eQeYTqA9SvsLDOEq5bz5fsSc2NIK', 'admin', '082170639694', 'ada', NULL, 1, NULL, '2026-02-03 09:36:57', '2026-02-03 09:36:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `can_access` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `web_settings`
--

CREATE TABLE `web_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `web_settings`
--

INSERT INTO `web_settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Ada', 'string', 'Nama aplikasi', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(2, 'app_description', 'Sistem Manajemen Futsal', 'string', 'Deskripsi aplikasi', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(3, 'app_email', 'hello@example.com', 'string', 'Email aplikasi', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(4, 'app_phone', '+62 812-3456-7890', 'string', 'Nomor telepon', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(5, 'app_address', 'Jakarta, Indonesia', 'string', 'Alamat', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(6, 'social_facebook', '', 'string', 'Facebook URL', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(7, 'social_instagram', '', 'string', 'Instagram URL', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(8, 'social_twitter', '', 'string', 'Twitter URL', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(9, 'maintenance_mode', '0', 'boolean', 'Mode maintenance', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(10, 'allow_registration', '1', 'boolean', 'Izinkan registrasi', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(11, 'email_notifications', '1', 'boolean', 'Notifikasi email', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(12, 'sms_notifications', '0', 'boolean', 'Notifikasi SMS', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(13, 'max_booking_per_day', '3', 'integer', 'Maksimal booking per hari', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(14, 'max_booking_hours', '4', 'integer', 'Maksimal jam booking', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(15, 'auto_confirm_booking', '0', 'boolean', 'Auto konfirmasi booking', '2026-02-03 09:20:53', '2026-02-03 09:20:53'),
(16, 'payment_timeout', '60', 'integer', 'Timeout pembayaran (menit)', '2026-02-03 09:20:53', '2026-02-03 09:20:53');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_index` (`user_id`),
  ADD KEY `activities_action_index` (`action`),
  ADD KEY `activities_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_lapangan_id_foreign` (`lapangan_id`),
  ADD KEY `bookings_tanggal_lapangan_id_index` (`tanggal`,`lapangan_id`),
  ADD KEY `bookings_user_id_status_index` (`user_id`,`status`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lapangans`
--
ALTER TABLE `lapangans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_booking_id_index` (`booking_id`),
  ADD KEY `payments_status_index` (`status`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permissions_user_id_page_name_unique` (`user_id`,`page_name`);

--
-- Indeks untuk tabel `web_settings`
--
ALTER TABLE `web_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `web_settings_key_unique` (`key`),
  ADD KEY `web_settings_key_index` (`key`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lapangans`
--
ALTER TABLE `lapangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `web_settings`
--
ALTER TABLE `web_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_lapangan_id_foreign` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
