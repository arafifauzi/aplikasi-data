<?php
// views/home.php
require_once 'views/partials/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Beranda</title>
    <link rel="stylesheet" href="public/css/partials.css">
    <link rel="stylesheet" href="public/css/home.css">
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
        <h1>Selamat Datang di FixHoax</h1>
        <p>Platform untuk melawan hoaks dan menyebarkan fakta.</p>

        <?php if (!empty($latest_news) && isset($latest_news[0])): ?>
            <div class="featured-news">
                <?php
                $featured_image = !empty($latest_news[0]['gambar']) && file_exists('public/Uploads/' . $latest_news[0]['gambar'])
                    ? 'public/Uploads/' . htmlspecialchars($latest_news[0]['gambar'])
                    : 'https://via.placeholder.com/600x300';
                ?>
                <img src="<?php echo $featured_image; ?>" alt="Featured News">
                <div class="title">
                    <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($latest_news[0]['id']); ?>">
                        <?php echo htmlspecialchars($latest_news[0]['judul']); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="news-sections">
            <div class="news-section">
                <h3>Terbaru</h3>
                <?php if (!empty($latest_news)): ?>
                    <?php foreach (array_slice($latest_news, 1) as $news): ?>
                        <div class="news-item">
                            <?php
                            $news_image = !empty($news['gambar']) && file_exists('public/Uploads/' . $news['gambar'])
                                ? 'public/Uploads/' . htmlspecialchars($news['gambar'])
                                : 'https://via.placeholder.com/100x60';
                            ?>
                            <img src="<?php echo $news_image; ?>" alt="News Image">
                            <p><a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id']); ?>">
                                    <?php echo htmlspecialchars($news['judul']); ?>
                                </a></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada berita terbaru saat ini.</p>
                <?php endif; ?>
            </div>

            <div class="news-section popular-container">
                <h3>Terpopuler</h3>
                <?php if (is_array($popular_news) && !empty($popular_news)): ?>
                    <?php foreach ($popular_news as $popular): ?>
                        <?php if (isset($popular['judul']) && isset($popular['id'])): ?>
                            <div class="news-item">
                                <?php
                                $popular_image = !empty($popular['gambar']) && file_exists('public/Uploads/' . $popular['gambar'])
                                    ? 'public/Uploads/' . htmlspecialchars($popular['gambar'])
                                    : 'https://via.placeholder.com/100x60';
                                ?>
                                <img src="<?php echo $popular_image; ?>" alt="Popular News Image">
                                <p><a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($popular['id']); ?>">
                                        <?php echo htmlspecialchars($popular['judul']); ?>
                                    </a></p>
                            </div>
                        <?php else: ?>
                            <p>Berita tidak valid.</p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada berita populer.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="index.php?action=report" class="action-btn">Laporkan Hoax</a>
    </div>
</body>

</html>