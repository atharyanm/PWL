-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Jan 2025 pada 14.57
-- Versi server: 10.4.17-MariaDB
-- Versi PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uts_pwl06869`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin utama', 'admin@contoh.com', '2024-11-10 07:30:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `npp` char(16) NOT NULL,
  `namadosen` varchar(50) NOT NULL,
  `homebase` char(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`npp`, `namadosen`, `homebase`, `username`, `password`) VALUES
('0686.11.2025.002', 'Prof. Dr. Budi Santoso', 'A16', 'budi user', 'password2'),
('0686.11.2025.003', 'Dr. Citra Dewi', 'A12', 'citra', 'password3'),
('0686.11.2025.004', 'Prof. Eko Prasetyo', 'A13', 'eko', 'password4'),
('0686.11.2025.006', 'Dr. Gunawan Wiranto', 'A15', 'gunawan', 'password6'),
('0686.11.2025.007', 'Prof. Hana Permata', 'A16', 'hana', 'password7'),
('0686.11.2025.009', 'Prof. Joko Widodo', 'A11', 'joko', 'password9'),
('0686.11.2025.010', 'Dr. Kartika Sari', 'A12', 'kartika', 'password10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(14) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `foto_profil`, `nama`, `username`, `password`, `email`) VALUES
(18, 'A12.2022.06869', '6057803538966364008.jpg', 'Atha', 'Atha Coba', 'Aku123', 'Atha@Contoh.com'),
(21, 'A12.2022.06867', 'burger.jpg', 'Atha', 'Atha', 'Atha123', 'Atha@contoh.com'),
(23, 'A12.2022.06802', '', 'Nama2', 'User2', 'Pass2', 'user2@contoh.com'),
(24, 'A12.2022.06803', '', 'Nama3', 'User3', 'Pass3', 'user3@contoh.com'),
(28, 'A12.2022.06807', '', 'Nama7', 'User7', 'Pass7', 'user7@contoh.com'),
(29, 'A12.2022.06808', '', 'Nama8', 'User8', 'Pass8', 'user8@contoh.com'),
(30, 'A12.2022.06809', '', 'Nama9', 'User9', 'Pass9', 'user9@contoh.com'),
(31, 'A12.2022.06810', '', 'Nama10', 'User10edit', 'Pass10', 'user10@contoh.com'),
(32, 'A12.2022.06811', '', 'Nama11 Edit', 'User11', 'Pass11', 'user11@contoh.com'),
(33, 'A12.2022.06812', NULL, 'Nama12', 'User12', 'Pass12', 'user12@contoh.com'),
(34, 'A12.2022.06813', '', 'Nama13', 'User13', 'Pass13', 'user13@contoh.com'),
(35, 'A12.2022.06814', '', 'Nama14', 'User14', 'Pass14', 'user14@contoh.com'),
(37, 'A12.2022.06816', NULL, 'Nama16', 'User16', 'Pass16', 'user16@contoh.com'),
(38, 'A12.2022.06817', '', 'Nama17', 'User17', 'Pass17', 'user17@contoh.com'),
(39, 'A12.2022.06818', NULL, 'Nama18', 'User18', 'Pass18', 'user18@contoh.com'),
(40, 'A12.2022.06819', '', 'Nama19', 'User19', 'Pass19', 'user19@contoh.com'),
(41, 'A12.2022.06820', 'udinus logo.jpg', 'Nama20', 'User20', 'Pass20', 'user20@contoh.com'),
(42, 'aaaaaaaaaaaaaa', NULL, 'vkuyv,hjv', 'uygkjhv,', 'lyuiyukyt', 'ykvjujvyuv@kytv.com'),
(43, '12376543256756', NULL, 'Coba', 'Coba', 'Coba', 'Coba@mm.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `matakuliah_tawar`
--

CREATE TABLE `matakuliah_tawar` (
  `id_tawar` int(11) NOT NULL,
  `idmatkul` char(10) NOT NULL,
  `npp` char(16) NOT NULL,
  `kelompok` varchar(10) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat') NOT NULL,
  `jam` varchar(20) NOT NULL,
  `ruang` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `matakuliah_tawar`
--

INSERT INTO `matakuliah_tawar` (`id_tawar`, `idmatkul`, `npp`, `kelompok`, `hari`, `jam`, `ruang`) VALUES
(3, 'A12.54102', '0686.11.2025.003', 'A12.4102', 'Rabu', '7:00-10:20', 'D.3.1'),
(5, 'A12.54104', '0686.11.2025.006', 'A12.4104', 'Senin', '9:30-12:00', 'A.1.1'),
(6, 'A12.54107', '0686.11.2025.002', 'A12.4101', 'Jumat', '10:20-12:50', 'G.6.12'),
(7, 'A12.54108', '0686.11.2025.009', 'A12.4101', 'Rabu', '14:30-17:00', 'G.5.3'),
(11, 'A12.54109', '0686.11.2025.006', 'A12.41.01', 'Selasa', '7:00-9:30', 'H.4.11'),
(13, 'A12.54109', '0686.11.2025.004', 'A12.4101', 'Kamis', '7:00-9:30', 'C.3.7');

-- --------------------------------------------------------

--
-- Struktur dari tabel `matkul`
--

CREATE TABLE `matkul` (
  `idmatkul` char(10) NOT NULL,
  `namamatkul` varchar(50) NOT NULL,
  `sks` int(2) NOT NULL,
  `jns` enum('T','P','TP') NOT NULL,
  `smt` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `matkul`
--

INSERT INTO `matkul` (`idmatkul`, `namamatkul`, `sks`, `jns`, `smt`) VALUES
('A12.54101', 'Pemrograman Web Lanjut', 3, 'TP', '3'),
('A12.54102', 'Basis Data Lanjut', 4, 'TP', '4'),
('A12.54103', 'Jaringan Komputer', 3, 'TP', '5'),
('A12.54104', 'Kecerdasan Buatan', 3, 'T', '6'),
('A12.54105', 'Rekayasa Perangkat Lunak', 3, 'TP', '5'),
('A12.54106', 'Sistem Informasi Manajemen', 3, 'T', '4'),
('A12.54107', 'Keamanan Informasi', 3, 'T', '6'),
('A12.54108', 'Pemrograman Mobile', 3, 'TP', '5'),
('A12.54109', 'Data Mining', 3, 'T', '6'),
('A12.54110', 'Analisis Algoritma', 3, 'T', '4');

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
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`npp`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `matakuliah_tawar`
--
ALTER TABLE `matakuliah_tawar`
  ADD PRIMARY KEY (`id_tawar`),
  ADD UNIQUE KEY `unique_matkul_tawar` (`idmatkul`,`npp`,`kelompok`,`hari`,`jam`),
  ADD KEY `idx_idmatkul` (`idmatkul`),
  ADD KEY `idx_npp` (`npp`);

--
-- Indeks untuk tabel `matkul`
--
ALTER TABLE `matkul`
  ADD PRIMARY KEY (`idmatkul`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `matakuliah_tawar`
--
ALTER TABLE `matakuliah_tawar`
  MODIFY `id_tawar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `matakuliah_tawar`
--
ALTER TABLE `matakuliah_tawar`
  ADD CONSTRAINT `fk_dosen` FOREIGN KEY (`npp`) REFERENCES `dosen` (`npp`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_matkul` FOREIGN KEY (`idmatkul`) REFERENCES `matkul` (`idmatkul`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
