<?php
// public/admin/admin_news.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    error_log("admin_news.php: No active session", 3, __DIR__ . '/../../public/errors.log');
    header("Location: ../../index.php?action=auth/login");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.';
    error_log("admin_news.php: Unauthorized access attempt by " . ($_SESSION['session_email'] ?? 'unknown'), 3, __DIR__ . '/../../public/errors.log');
    header("Location: ../../index.php?action=home");
    exit();
}

// Tambahkan array artikel_titles untuk digunakan di JavaScript
$artikel_titles = [];
foreach ($article_data as $artikel) {
    $artikel_titles[$artikel['id']] = htmlspecialchars($artikel['judul_artikel']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Admin News</title>
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_base.css">
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_news.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/projekfixhoax/public/js/admin.js" defer></script>
    <script>
        window.artikelTitles = <?php echo json_encode($artikel_titles); ?>;
    </script>
</head>

<body>
    <div class="main-content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']);
                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-button" data-tab="news">Daftar Berita</button>
            <button class="tab-button" data-tab="add">Tambah Berita</button>
        </div>

        <div class="news-section" style="display: <?php echo isset($_GET['tab']) && $_GET['tab'] === 'add' ? 'none' : 'block'; ?>;">
            <table class="news-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Keterangan</th>
                        <th>Tema</th>
                        <th>Penulis</th>
                        <th>Tanggal</th>
                        <th>Gambar</th>
                        <th>Artikel</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news_data as $news): ?>
                        <tr class="clickable" data-id="<?php echo $news['id']; ?>">
                            <td><?php echo htmlspecialchars($news['id']); ?></td>
                            <td><?php echo htmlspecialchars($news['judul']); ?></td>
                            <td><?php echo htmlspecialchars($news['keterangan']); ?></td>
                            <td><?php echo htmlspecialchars($news['tema']); ?></td>
                            <td><?php echo htmlspecialchars($news['penulis']); ?></td>
                            <td><?php echo htmlspecialchars($news['tanggal']); ?></td>
                            <td>
                                <?php
                                $upload_dir = __DIR__ . '/../../public/Uploads/';
                                $gambar_file = htmlspecialchars($news['gambar'] ?? '');
                                $gambar_path = '/projekfixhoax/public/Uploads/' . $gambar_file;
                                $gambar_full_path = $upload_dir . $gambar_file;
                                if (!empty($gambar_file) && file_exists($gambar_full_path)): ?>
                                    <img src="<?php echo $gambar_path; ?>" class="thumbnail" alt="News Image">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/40" class="thumbnail" alt="No Image">
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $artikel_ids = [];
                                if (!empty($news['artikel'])) {
                                    if (is_string($news['artikel'])) {
                                        $decoded = json_decode($news['artikel'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $artikel_ids = $decoded;
                                        } else {
                                            error_log("admin_news.php: Invalid JSON in artikel for news ID {$news['id']}: " . $news['artikel'], 3, __DIR__ . '/../../public/errors.log');
                                        }
                                    } elseif (is_array($news['artikel'])) {
                                        $artikel_ids = $news['artikel'];
                                    }
                                }

                                $artikel_titles = [];
                                foreach ($article_data as $artikel) {
                                    if (in_array($artikel['id'], $artikel_ids)) {
                                        $artikel_titles[] = htmlspecialchars($artikel['judul_artikel']);
                                    }
                                }
                                echo !empty($artikel_titles) ? implode(', ', $artikel_titles) : '-';
                                ?>
                            </td>
                            <td class="action-column">
                                <button class="edit-news-btn" data-id="<?php echo $news['id']; ?>" title="Edit Berita">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?action=admin/news&delete_news=<?php echo $news['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="add-form-section" style="display: <?php echo isset($_GET['tab']) && $_GET['tab'] === 'add' ? 'block' : 'none'; ?>;">
            <div class="add-form">
                <h3>Tambah Berita Baru</h3>
                <form method="post" enctype="multipart/form-data">
                    <label for="judul">Judul:</label>
                    <input type="text" name="judul" required>
                    <label for="isi">Isi:</label>
                    <textarea name="isi" required></textarea>
                    <label for="keterangan">Keterangan:</label>
                    <select name="keterangan" required>
                        <option value="HOAX">HOAX</option>
                        <option value="FAKTA">FAKTA</option>
                    </select>
                    <label for="klarifikasi">Klarifikasi:</label>
                    <textarea name="klarifikasi" required></textarea>
                    <label for="penulis">Penulis:</label>
                    <input type="text" name="penulis" required>
                    <label for="tanggal">Tanggal:</label>
                    <input type="date" name="tanggal" required>
                    <label for="tema">Tema:</label>
                    <select name="tema" required>
                        <?php foreach ($tema_options as $option): ?>
                            <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="artikel">Artikel (Ctrl+klik untuk pilih banyak):</label>
                    <select name="artikel[]" multiple>
                        <?php foreach ($article_data as $artikel): ?>
                            <option value="<?php echo $artikel['id']; ?>"><?php echo htmlspecialchars($artikel['judul_artikel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="gambar">Gambar:</label>
                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png">
                    <button type="submit" name="add_news">Simpan</button>
                </form>
            </div>
        </div>

        <!-- Modal untuk Edit Berita -->
        <div class="modal" id="edit-modal">
            <div class="modal-content">
                <button class="close-btn" title="Tutup">×</button>
                <h3>Edit Berita</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    <input type="hidden" name="existing_gambar">
                    <label for="judul">Judul:</label>
                    <input type="text" name="judul" required>
                    <label for="isi">Isi:</label>
                    <textarea name="isi" required></textarea>
                    <label for="keterangan">Keterangan:</label>
                    <select name="keterangan" required>
                        <option value="HOAX">HOAX</option>
                        <option value="FAKTA">FAKTA</option>
                    </select>
                    <label for="klarifikasi">Klarifikasi:</label>
                    <textarea name="klarifikasi" required></textarea>
                    <label for="penulis">Penulis:</label>
                    <input type="text" name="penulis" required>
                    <label for="tanggal">Tanggal:</label>
                    <input type="date" name="tanggal" required>
                    <label for="tema">Tema:</label>
                    <select name="tema" required>
                        <?php foreach ($tema_options as $option): ?>
                            <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="artikel">Artikel (Ctrl+klik untuk pilih banyak):</label>
                    <select name="artikel[]" multiple>
                        <?php foreach ($article_data as $artikel): ?>
                            <option value="<?php echo $artikel['id']; ?>"><?php echo htmlspecialchars($artikel['judul_artikel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="gambar">Gambar:</label>
                    <img src="" class="image-preview" style="display: none;" alt="Current Image">
                    <p class="no-image" style="display: none;">Tidak ada gambar</p>
                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png">
                    <button type="submit" name="update_news">Simpan</button>
                    <button type="button" class="cancel-btn">Tutup</button>
                </form>
            </div>
        </div>

        <!-- Modal untuk Detail Berita -->
        <div class="modal" id="detail-modal">
            <div class="modal-content">
                <button class="close-btn" title="Tutup">×</button>
                <h3>Detail Berita</h3>
                <div class="detail-content">
                    <p><strong>ID:</strong> <span class="detail-id"></span></p>
                    <p><strong>Judul:</strong> <span class="detail-judul"></span></p>
                    <p><strong>Isi:</strong> <span class="detail-isi"></span></p>
                    <p><strong>Keterangan:</strong> <span class="detail-keterangan"></span></p>
                    <p><strong>Klarifikasi:</strong> <span class="detail-klarifikasi"></span></p>
                    <p><strong>Penulis:</strong> <span class="detail-penulis"></span></p>
                    <p><strong>Tanggal:</strong> <span class="detail-tanggal"></span></p>
                    <p><strong>Tema:</strong> <span class="detail-tema"></span></p>
                    <p><strong>Artikel:</strong></p>
                    <ul class="artikel-list"></ul>
                    <p><strong>Gambar:</strong></p>
                    <img class="detail-image" src="" alt="News Image" style="max-width: 300px; display: none;">
                    <p class="no-image" style="display: none;">Tidak ada gambar</p>
                </div>
                <button class="edit-btn" data-id="">Edit</button>
                <button class="close-detail-btn">Tutup</button>
            </div>
        </div>
    </div>
</body>

</html>

</html>