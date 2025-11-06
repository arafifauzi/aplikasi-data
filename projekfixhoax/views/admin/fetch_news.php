<?php
// fetch_news.php
require_once 'models/News.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Akses ditolak. Hanya admin yang dapat mengakses data ini.']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID berita tidak valid.']);
    exit();
}

$id = (int)$_GET['id'];

try {
    $newsModel = new News();
    $news = $newsModel->getNewsById($id);
    if (!$news) {
        echo json_encode(['error' => 'Berita tidak ditemukan.']);
        exit();
    }

    $upload_dir = __DIR__ . '/public/Uploads/';
    $gambar_exists = !empty($news['gambar']) && file_exists($upload_dir . $news['gambar']);
    $artikel_ids = !empty($news['artikel']) ? json_decode($news['artikel'], true) ?? [] : [];

    echo json_encode([
        'id' => $news['id'],
        'judul' => $news['judul'],
        'isi' => $news['isi'],
        'keterangan' => $news['keterangan'],
        'klarifikasi' => $news['klarifikasi'],
        'penulis' => $news['penulis'],
        'tanggal' => $news['tanggal'],
        'tema' => $news['tema'],
        'artikel' => $artikel_ids,
        'gambar' => $news['gambar'],
        'gambar_exists' => $gambar_exists
    ]);
} catch (Exception $e) {
    error_log("fetch_news.php: Error fetching news ID $id: " . $e->getMessage(), 3, __DIR__ . '/public/errors.log');
    echo json_encode(['error' => 'Gagal memuat data berita.']);
}
?>