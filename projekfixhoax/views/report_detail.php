<?php
require_once 'partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Detail Laporan</title>
    <link rel="stylesheet" href="public/css/partials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/report.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Detail Laporan</h1>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8');
                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if ($report): ?>
            <div class="report-detail">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Judul:</strong> <?php echo htmlspecialchars($report['judul'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Link Referensi:</strong> <?php echo htmlspecialchars($report['link_referensi'] ?? 'Tidak ada', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Bukti Gambar:</strong>
                    <?php if ($report['bukti_gambar_video']): ?>
                        <?php $ext = pathinfo($report['bukti_gambar_video'], PATHINFO_EXTENSION); ?>
                        <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="public/uploads/<?php echo htmlspecialchars($report['bukti_gambar_video'], ENT_QUOTES, 'UTF-8'); ?>" alt="Bukti" style="max-width: 300px;">
                        <?php elseif (in_array($ext, ['mp4', 'webm'])): ?>
                            <video controls style="max-width: 300px;">
                                <source src="public/uploads/<?php echo htmlspecialchars($report['bukti_gambar_video'], ENT_QUOTES, 'UTF-8'); ?>" type="video/<?php echo $ext; ?>">
                            </video>
                        <?php endif; ?>
                    <?php else: ?>
                        Tidak ada
                    <?php endif; ?>
                </p>
                <p><strong>Tanggapan/Alasan:</strong> <?php echo nl2br(htmlspecialchars($report['tanggapan_alasan'], ENT_QUOTES, 'UTF-8')); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Pengirim:</strong> <?php echo htmlspecialchars($report['user_email'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Tanggal:</strong> <?php echo date('d M Y, H:i', strtotime($report['created_at'])); ?></p>
                <?php if ($related_news): ?>
                    <p><strong>Berita Terkait:</strong> <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($related_news['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($related_news['judul'], ENT_QUOTES, 'UTF-8'); ?></a></p>
                <?php endif; ?>
                <?php if ($report['status'] === 'terkirim' && isset($_SESSION['session_email']) && $_SESSION['session_email'] === $report['user_email']): ?>
                    <button class="edit-btn" onclick="toggleEditForm()">Edit Laporan</button>
                <?php endif; ?>
            </div>

            <?php if ($report['status'] === 'terkirim' && isset($_SESSION['session_email']) && $_SESSION['session_email'] === $report['user_email']): ?>
                <div class="edit-form" style="display: none;">
                    <h2>Edit Laporan</h2>
                    <form method="post" action="index.php?action=report_update" enctype="multipart/form-data">
                        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="form-group">
                            <label for="judul">Judul Laporan:</label>
                            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($report['judul'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="link_referensi">Link Referensi Berita:</label>
                            <input type="url" id="link_referensi" name="link_referensi" value="<?php echo htmlspecialchars($report['link_referensi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="bukti_gambar_video">Bukti Gambar:</label>
                            <?php if ($report['bukti_gambar_video']): ?>
                                <p>File saat ini: <?php echo htmlspecialchars(basename($report['bukti_gambar_video']), ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                            <input type="file" id="bukti_gambar_video" name="bukti_gambar_video" accept="image/*,video/*">
                        </div>
                        <div class="form-group">
                            <label for="tanggapan_alasan">Tanggapan/Alasan:</label>
                            <textarea id="tanggapan_alasan" name="tanggapan_alasan" rows="5" required><?php echo htmlspecialchars($report['tanggapan_alasan'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <button type="submit" name="update_report" class="submit-btn">Simpan Perubahan</button>
                        <button type="button" class="cancel-btn" onclick="toggleEditForm()">Batal</button>
                    </form>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p>Laporan tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleEditForm() {
            const editForm = document.querySelector('.edit-form');
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>