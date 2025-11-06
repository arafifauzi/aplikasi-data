<?php
require_once 'partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Report</title>
    <link rel="stylesheet" href="public/css/partials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/report.css?v=<?php echo time(); ?>">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const formSection = document.querySelector('.form-section');
            const reportList = document.querySelector('.report-list');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    if (this.textContent === 'Tambah Laporan') {
                        formSection.classList.add('active');
                        reportList.style.display = 'none';
                    } else {
                        formSection.classList.remove('active');
                        reportList.style.display = 'block';
                    }
                });
            });

            // Set default tab
            tabs[0].click();
        });
    </script>
</head>

<body>
    <div class="header">DAFTAR LAPORAN</div>
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']);
                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['session_email'])): ?>
            <p><a href="index.php?action=auth/login">Login</a> untuk mengajukan laporan.</p>
        <?php else: ?>
            <div class="tabs">
                <button class="tab-button active">Tambah Laporan</button>
                <button class="tab-button">Daftar Laporan</button>
            </div>

            <div class="form-section active">
                <h2>Tambah Laporan Baru</h2>
                <form method="post" action="index.php?action=report" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="judul">Judul Laporan:</label>
                        <input type="text" id="judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="link_referensi">Link Referensi Berita:</label>
                        <input type="url" id="link_referensi" name="link_referensi">
                    </div>
                    <div class="form-group">
                        <label for="bukti_gambar_video">Bukti Gambar:</label>
                        <input type="file" id="bukti_gambar_video" name="bukti_gambar_video" accept="image/*,video/*">
                    </div>
                    <div class="form-group">
                        <label for="tanggapan_alasan">Tanggapan/Alasan:</label>
                        <textarea id="tanggapan_alasan" name="tanggapan_alasan" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="submit_report" class="submit-btn">Kirim Laporan</button>
                </form>
            </div>

            <div class="report-list" style="display: none;">
                <?php
                // Debug: Ensure $reports is defined
                if (!isset($reports)) {
                    error_log("report.php: \$reports is undefined", 3, "errors.log");
                    $reports = [];
                }
                ?>
                <?php if (is_array($reports) && !empty($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <div class="report-card">
                            <h3>Laporan</h3>
                            <p><strong>Pengirim:</strong> <?php echo htmlspecialchars($report['user_email']); ?></p>
                            <p><strong>Judul Laporan:</strong> <?php echo htmlspecialchars($report['judul']); ?></p>
                            <p><strong>Link Referensi Berita:</strong> <?php echo htmlspecialchars($report['link_referensi'] ?? 'Tidak ada'); ?></p>
                            <p><strong>Tanggapan/Alasan:</strong> <?php echo htmlspecialchars(substr($report['tanggapan_alasan'], 0, 50)) . (strlen($report['tanggapan_alasan']) > 50 ? '...' : ''); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status']); ?></p>
                            <a href="index.php?action=report_detail&id=<?php echo $report['id']; ?>" class="action-btn">Lihat Detail</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada laporan yang tersedia.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>