-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Bulan Mei 2025 pada 05.13
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
-- Database: `fixhoax`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

CREATE TABLE `artikel` (
  `id` int(11) NOT NULL,
  `judul_artikel` varchar(255) NOT NULL,
  `link_artikel` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `artikel`
--

INSERT INTO `artikel` (`id`, `judul_artikel`, `link_artikel`) VALUES
(2, 'Tips Menjaga Kesehatan Saat Kerja Remote', 'https://example.com/artikel/kerja-remote'),
(3, 'Panduan Belajar Pemrograman untuk Pemula', 'https://example.com/artikel/belajar-pemrograman'),
(4, 'Manfaat Olahraga di Pagi Hari', 'https://example.com/artikel/olahraga-pagi'),
(6, 'asdadsaddad', 'https://example.com/artikel/kerja-remote');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `comment_content` text NOT NULL,
  `comment_date` datetime NOT NULL,
  `is_edited` tinyint(4) DEFAULT 0,
  `edited_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `comment_reactions`
--

CREATE TABLE `comment_reactions` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `reaction_type` enum('like','dislike') NOT NULL,
  `reaction_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text DEFAULT NULL,
  `klarifikasi` text NOT NULL,
  `keterangan` enum('HOAX','FAKTA') NOT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `tema` enum('Politik','Kesehatan','Teknologi','Ekonomi','Pendidikan','Lingkungan','Sosial','Pemerintahan') NOT NULL,
  `artikel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`artikel`)),
  `gambar` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `news`
--

INSERT INTO `news` (`id`, `judul`, `isi`, `klarifikasi`, `keterangan`, `penulis`, `tanggal`, `tema`, `artikel`, `gambar`, `views`) VALUES
(11, 'Dibawah Umur, Pernikahan Dini Terjadi Di Lombok', 'Sebuah video pernikahan adat Sasak antara seorang siswi SMP berusia 15 tahun dan siswa SMK berusia 17 tahun di Lombok Tengah, Nusa Tenggara Barat, menjadi viral di media sosial. Video tersebut menunjukkan perilaku mempelai perempuan yang dianggap janggal, menimbulkan keprihatinan publik. Akibatnya, orang tua kedua mempelai dan penghulu yang menikahkan mereka dipolisikan.\n\nPasangan yang menikah adalah SMY (15) dari Desa Sukaraja, Kecamatan Praya Timur, dan SR (17) dari Desa Braim, Kecamatan Praya Tengah. Dalam video yang beredar, terlihat SMY berjoget sambil berjalan menuju pelaminan, ditandu oleh dua perempuan dewasa. Perilaku ini menimbulkan kekhawatiran mengenai kondisi psikologisnya. Ketua Lembaga Perlindungan Anak (LPA) Kota Mataram, Joko Jumadi, menyatakan bahwa pihaknya belum dapat memastikan kondisi psikologis SMY tanpa pemeriksaan medis lebih lanjut.\n\nLPA Kota Mataram melaporkan kasus ini ke Polres Lombok Tengah. Joko Jumadi menegaskan bahwa pernikahan anak di bawah umur melanggar Undang-Undang Nomor 16 Tahun 2019 tentang Perkawinan, yang menetapkan usia minimal menikah adalah 19 tahun. Pihak kepolisian kini tengah menyelidiki kasus ini dan mempertimbangkan langkah hukum terhadap orang tua kedua mempelai dan penghulu yang menikahkan mereka.\n\nKasus ini menyoroti pentingnya upaya pencegahan pernikahan anak di Indonesia. Menurut data LPA Kota Mataram, tren pengajuan dispensasi pernikahan anak menurun, namun masih ada kasus yang tidak tercatat karena kurangnya kesadaran masyarakat. Pemerintah daerah dan lembaga terkait terus berupaya meningkatkan kesadaran masyarakat tentang dampak negatif pernikahan anak dan pentingnya pendidikan bagi anak-anak.\n\nPernikahan anak di bawah umur memiliki dampak negatif yang signifikan terhadap kesehatan, pendidikan, dan masa depan anak-anak. Kasus di Lombok Tengah ini menjadi pengingat akan pentingnya peran orang tua, masyarakat, dan pemerintah dalam melindungi hak-hak anak dan mencegah praktik pernikahan dini.', 'Pernikahan dini antara siswi SMP berusia 15 tahun dan siswa SMK berusia 17 tahun di Lombok Tengah adalah fakta dan telah dilaporkan ke Polres Lombok Tengah karena melanggar UU Nomor 16 Tahun 2019, yang menetapkan usia minimal menikah 19 tahun. Kasus ini menunjukkan perlunya edukasi masyarakat untuk mencegah pernikahan anak yang berdampak buruk pada kesehatan, pendidikan, dan masa depan anak.', 'FAKTA', 'Anonim', '2025-05-27', 'Sosial', '[]', NULL, 6),
(12, 'PLN Korupsi Dana, Korupsi Indonesia Bertambah', 'PT Perusahaan Listrik Negara (PLN) tengah disorot akibat dugaan korupsi dalam proyek Pembangkit Listrik Tenaga Uap (PLTU) 1 di Kalimantan Barat. Proyek yang dimulai pada 2008 ini mangkrak sejak 2016, menyebabkan kerugian negara hingga Rp1,2 triliun.\n\nPada 2008, PLN mengadakan lelang pembangunan PLTU 1 Kalbar berkapasitas 2x50 MW. Konsorsium KSO BRN memenangkan lelang meski tidak memenuhi syarat prakualifikasi serta evaluasi administrasi dan teknis. Kontrak senilai USD 80 juta dan Rp507 miliar ditandatangani pada 11 Juni 2009 antara Dirut PT BRN dan Dirut PLN saat itu. Namun, PT BRN mengalihkan seluruh pekerjaan kepada perusahaan asal Tiongkok, PT PI dan QJPSE, yang berujung pada mangkraknya proyek sejak 2016.\n\nKorps Pemberantasan Tindak Pidana Korupsi (Kortastipidkor) Polri telah menaikkan status kasus ini ke tahap penyelidikan. Pada awal Februari 2025, sejumlah pejabat PLN Pusat dipanggil untuk dimintai keterangan terkait proyek ini dan dua kasus dugaan korupsi lainnya yang masih berkaitan dengan PLN.\n\nSebuah video yang menampilkan tumpukan uang tunai diklaim sebagai hasil korupsi PLN sebesar Rp1,2 triliun beredar di media sosial. Namun, klaim tersebut telah diklarifikasi sebagai misinformasi. Video tersebut sebenarnya merupakan dokumentasi penyitaan uang Rp173 miliar dari kasus korupsi mantan Dirut PLN, Nur Pamudji, pada 2019.\n\nKasus ini menambah daftar panjang dugaan korupsi di sektor energi Indonesia. Penyelidikan terhadap proyek PLTU Kalbar 1 diharapkan dapat mengungkap pihak-pihak yang bertanggung jawab dan mencegah kerugian serupa di masa depan.', 'Klaim bahwa video tumpukan uang Rp1,2 triliun merupakan hasil korupsi PLN adalah hoax. Video tersebut sebenarnya menunjukkan penyitaan Rp173 miliar dari kasus korupsi mantan Dirut PLN, Nur Pamudji, pada 2019. Namun, dugaan korupsi pada proyek PLTU Kalbar 1 yang merugikan negara Rp1,2 triliun sedang diselidiki oleh Kortastipidkor Polri.', 'HOAX', 'Anonim', '2025-05-27', 'Sosial', '[]', NULL, 41),
(13, 'asdasd', 'asdada', 'asdad', 'HOAX', 'asda', '2025-05-27', 'Politik', '[4,6]', 'news_1748311954.jpeg', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `news_artikel`
--

CREATE TABLE `news_artikel` (
  `news_id` int(11) NOT NULL,
  `artikel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `link_referensi` varchar(255) DEFAULT NULL,
  `bukti_gambar_video` varchar(255) DEFAULT NULL,
  `tanggapan_alasan` text DEFAULT NULL,
  `status` enum('terkirim','memverifikasi','publikasi','tidak publikasi') DEFAULT 'terkirim',
  `email_user` varchar(100) DEFAULT NULL,
  `news_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isi` text DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `sumber` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `judul`, `link_referensi`, `bukti_gambar_video`, `tanggapan_alasan`, `status`, `email_user`, `news_id`, `created_at`, `isi`, `kategori`, `sumber`) VALUES
(1, 'Kehadiran Undang-Undang Miller Yang Datpat Memancing Kerusuhan Mahasiswa', 'www.beritaLU-Kerahaasian-Milter.com', 'uploads/FotoPengesahan_UU_Miller.jpg', 'Kritik terhadap kebijakan otoriter pemerintah', 'terkirim', 'user1@example.com', NULL, '2025-05-18 10:29:07', NULL, NULL, NULL),
(2, 'Kehadiran Undang-Undang Miller Yang Datpat Memancing Kerusuhan Mahasiswa', 'www.beritaLU-Kerahaasian-Milter.com', 'uploads/FotoPengesahan_UU_Miller.jpg', 'Kritik terhadap kebijakan otoriter pemerintah', 'memverifikasi', 'user2@example.com', NULL, '2025-05-18 10:29:07', NULL, NULL, NULL),
(3, 'Kehadiran Undang-Undang Miller Yang Datpat Memancing Kerusuhan Mahasiswa', 'www.beritaLU-Kerahaasian-Milter.com', 'uploads/FotoPengesahan_UU_Miller.jpg', 'Kritik terhadap kebijakan otoriter pemerintah', 'tidak publikasi', 'user3@example.com', NULL, '2025-05-18 10:29:07', NULL, NULL, NULL),
(4, 'Kehadiran Undang-Undang Miller Yang Datpat Memancing Kerusuhan Mahasiswa', 'www.beritaLU-Kerahaasian-Milter.com', 'uploads/FotoPengesahan_UU_Miller.jpg', 'Kritik terhadap kebijakan otoriter pemerintah', 'publikasi', 'user4@example.com', NULL, '2025-05-18 10:29:07', NULL, NULL, NULL),
(5, 'ini judul berita', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '', 'sadsdasdasdasdadasdad', 'publikasi', 'admin@gmail.com', NULL, '2025-05-18 10:30:28', NULL, NULL, NULL),
(6, 'ini judul dimas', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', 'uploads/1747565197_e25d87e1-d982-4334-bfe7-cb815b50f745_jpg', 'asdadadadasda', 'publikasi', 'dimas@gmail.com', NULL, '2025-05-18 10:46:37', 'Ini adalah isi laporan', 'Hoaks', 'sumber.com'),
(7, 'ini judul dimas', NULL, NULL, NULL, 'publikasi', 'dimas@gmail.com', NULL, '2025-05-18 10:46:37', 'Ini adalah isi laporan', 'Hoaks', 'sumber.com'),
(8, 'footer jelek', 'http://localhost/fixhoax/report.php', 'uploads/1747573436_DETAIL_PAGE__1__png', 'liat di halaman home, sumpah kurang teratur', 'terkirim', 'dimas@gmail.com', NULL, '2025-05-18 13:03:56', NULL, NULL, NULL),
(9, 'kasian hakim', 'http://localhost/fixhoax/report.php', 'uploads/1747573578_e25d87e1-d982-4334-bfe7-cb815b50f745_jpg', 'hakim buat komen gak selesai selesai', 'tidak publikasi', 'mbek@gmail.com', NULL, '2025-05-18 13:06:18', NULL, NULL, NULL),
(10, 'kasian hakim', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '', 'quiz pemlan gampanb', 'publikasi', 'admin@gmail.com', 11, '2025-05-19 04:57:28', NULL, NULL, NULL),
(11, 'ini judul beritaaa', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', 'uploads/1747735156_e25d87e1-d982-4334-bfe7-cb815b50f745_jpg', 'asdasad', 'tidak publikasi', 'admin@gmail.com', NULL, '2025-05-20 09:59:16', NULL, NULL, NULL),
(12, 'aku judulbaru', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '1748189402_e25d87e1-d982-4334-bfe7-cb815b50f745.jpg', 'asdasas', '', 'dbd@gmail.com', NULL, '2025-05-25 16:10:02', NULL, NULL, NULL),
(13, 'tesbfscsc', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '1748207351_599e3b95636919.5eb96c0445ea7.jpg', 'asdad', 'terkirim', 'dimas@gmail.com', NULL, '2025-05-25 21:09:11', NULL, NULL, NULL),
(14, '12', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '1748227615_599e3b95636919.5eb96c0445ea7.jpg', 'asdadsd', 'tidak publikasi', 'admin@gmail.com', NULL, '2025-05-26 02:46:55', NULL, NULL, NULL),
(15, 'tesbfscsc', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '1748227647_dec1bda918b1406c887e217c9751b23b.mov', 'asdasd', 'tidak publikasi', 'admin@gmail.com', NULL, '2025-05-26 02:47:27', NULL, NULL, NULL),
(16, 'thai', 'http://localhost/fixhoax/report.php', '1748311628_Group 239183 (1).png', 'asdasdasda', 'publikasi', 'admin@gmail.com', 12, '2025-05-27 01:54:02', NULL, NULL, NULL),
(17, 'ada', 'https://www.detik.com/jatim/berita/d-6133691/viral-benda-aneh-mirip-ufo-di-malang-peneliti-astronomi-video-rekayasa', '1748312434_download.jpg', 'adsad', 'publikasi', 'admin@gmail.com', 12, '2025-05-27 02:20:34', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nickname` varchar(50) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `nickname`, `gender`, `country`, `phone_number`, `role`) VALUES
(1, 'dimas', 'dimas@gmail.com', '$2y$10$NAt54WhjBRvopoUpEp/k3.jw8noU5nVBNRx4nE6Q8h.lp23zSGbTW', '2025-05-18 06:02:07', 'dimas keren', 'Male', 'Indonesia', '085838918072', 'user'),
(2, 'admin', 'admin@gmail.com', '$2y$10$HMoctCLzsQB2yeZj7aZ5newiqHerqX2OPnnaUVUNzD3uNcq1qdGAu', '2025-05-18 06:26:04', 'admin', 'Male', 'Indonesia', '085838918072', 'admin'),
(3, 'Ahmad Rafi', 'rafi@gmail.com', '$2y$10$pdRGg3UU0EiZpAJdrGJksOXgo21MxuB.VlSlZYcM/cBnRg7h3Se9O', '2025-05-18 08:34:05', 'rafi', 'Male', 'Indonesia', '085838918072', 'user'),
(4, 'aku', 'aku@gmail.com', '$2y$10$fCNsfo0DRTQ24GaqmxUk8OR4QbP7XYSfV0TkH20FGbjgZHNw7IXam', '2025-05-18 11:09:38', NULL, NULL, NULL, NULL, 'user'),
(5, 'hakim', 'mbek@gmail.com', '$2y$10$UEpw6af3ORDD8I0GeN06wuXWQG8IFYVRYjFL.W95izVziR.vTQIze', '2025-05-18 13:05:04', NULL, NULL, NULL, NULL, 'user'),
(6, 'dbd', 'akun@gmail.com', '$2y$10$wKOnuFS3e5hrRhtlDZU41.wbTEKLtro4rZ1zMm5qjW45J1DTwfQBu', '2025-05-25 10:31:05', NULL, NULL, NULL, NULL, 'user'),
(7, 'dimasss', 'dimasss@gmail.com', '$2y$10$6.vwpOQVhhOi1WbL5IXIkuVvMeUgKugwMo3l6FanqxtA9T9BndLrq', '2025-05-25 11:16:43', NULL, NULL, NULL, NULL, 'user'),
(8, 'ded', 'ded@gmail.com', '$2y$10$r.0EpcO/S0q0x3seOcsra.JkZpERPgmC3SCrMNveyBKKwVkXqXiOe', '2025-05-25 12:54:06', 'dedi', 'Male', 'Indonesia', '085838918072', 'user'),
(9, 'dbd', 'dbd@gmail.com', '$2y$10$Hm3YM4rFeJT9h2Ck3p651OqjSevAsECY1HC6EApg2HtMC2IeOFHPC', '2025-05-25 15:45:27', NULL, NULL, NULL, NULL, 'user'),
(11, 'aku', 'akunn@gmail.com', '$2y$10$xJGh7Z8gWJ9npmua0wiY9eMjAxSAZtgXCIsxTOpD1VVpO1m2bR/Cy', '2025-05-25 21:15:42', NULL, NULL, NULL, NULL, 'user'),
(12, 'bigs', 'big@gmail.com', '$2y$10$d3O/VdTY7a8ZeCKmhIq2d.ZKGyCnY1Cfkxd5zRi4p9/lch54MM7py', '2025-05-26 12:21:19', NULL, NULL, NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indeks untuk tabel `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indeks untuk tabel `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `news_artikel`
--
ALTER TABLE `news_artikel`
  ADD PRIMARY KEY (`news_id`,`artikel_id`),
  ADD KEY `artikel_id` (`artikel_id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

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
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `comment_reactions`
--
ALTER TABLE `comment_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD CONSTRAINT `comment_reactions_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_reactions_ibfk_2` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `news_artikel`
--
ALTER TABLE `news_artikel`
  ADD CONSTRAINT `news_artikel_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_artikel_ibfk_2` FOREIGN KEY (`artikel_id`) REFERENCES `artikel` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
