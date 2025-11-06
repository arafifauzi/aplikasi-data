<?php

require_once 'partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Berita</title>
    <link rel="stylesheet" href="public/css/partials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/news.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="header">BERITA TERKINI</div>
    <div class="container">
        <!-- Search Bar -->
        <div class="search-section">
            <form method="get" action="index.php" class="search-form">
                <input type="hidden" name="action" value="news">
                <input type="text" name="search" placeholder="Cari berita..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <!-- Session Feedback -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']);
                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- News List -->
        <div class="news-grid">
            <?php
            // Ensure $news_items is defined
            if (!isset($news_items)) {
                error_log("news.php: \$news_items is undefined", 3, "errors.log");
                $news_items = [];
            }
            ?>
            <?php if (is_array($news_items) && !empty($news_items)): ?>
                <?php foreach ($news_items as $news): ?>
                    <div class="news-card">
                        <?php if (!empty($news['gambar'])): ?>
                            <img src="public/uploads/<?php echo htmlspecialchars($news['gambar']); ?>" alt="<?php echo htmlspecialchars($news['judul']); ?>" class="news-image">
                        <?php else: ?>
                            <img src="public/images/placeholder.jpg" alt="Placeholder" class="news-image">
                        <?php endif; ?>
                        <div class="news-content">
                            <h3><?php echo htmlspecialchars($news['judul']); ?></h3>
                            <p class="news-summary"><?php echo htmlspecialchars(substr($news['isi'] ?? '', 0, 150)) . (strlen($news['isi'] ?? '') > 150 ? '...' : ''); ?></p>
                            <p class="news-meta">
                                <span>Penulis: <?php echo htmlspecialchars($news['penulis'] ?? 'Tidak Diketahui'); ?></span> |
                                <span><?php echo $news['tanggal'] ? date('d M Y', strtotime($news['tanggal'])) : 'Tanggal Tidak Diketahui'; ?></span>
                            </p>
                            <a href="index.php?action=news_detail&id=<?php echo $news['id']; ?>" class="read-more">Baca Selengkapnya</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada berita ditemukan.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?action=news&page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                        class="<?php echo (isset($current_page) && $current_page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>