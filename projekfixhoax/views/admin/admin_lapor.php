<?php
// public/admin/admin_lapor.php
// Session is managed in index.php, so only check if session is active
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log("admin_lapor.php: No active session", 3, __DIR__ . '/../errors.log');
    header("Location: ../index.php?action=auth/login");
    exit();
}

// Initialize variables to prevent undefined errors
$reports = isset($reports) ? $reports : [];
$news_data = isset($news_data) ? $news_data : [];
$report_details = isset($report_details) ? $report_details : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Admin Laporan</title>
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_base.css">
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_lapor.css">
</head>

<body>
    <div class="main-content">
        <div class="header">
            <h2>Review Laporan</h2>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <table class="report-table dashboard-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports) && is_array($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr class="clickable" data-id="<?php echo htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <td><?php echo htmlspecialchars($report['user_email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($report['judul'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($report['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="action-column">
                                <form method="post" class="status-form">
                                    <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <select name="status" class="status-select">
                                        <option value="terkirim" <?php echo $report['status'] === 'terkirim' ? 'selected' : ''; ?>>Terkirim</option>
                                        <option value="memverifikasi" <?php echo $report['status'] === 'memverifikasi' ? 'selected' : ''; ?>>Memverifikasi</option>
                                        <option value="publikasi" <?php echo $report['status'] === 'publikasi' ? 'selected' : ''; ?>>Publikasi</option>
                                        <option value="tidak publikasi" <?php echo $report['status'] === 'tidak publikasi' ? 'selected' : ''; ?>>Tidak Publikasi</option>
                                    </select>
                                    <div class="news-select <?php echo $report['status'] === 'publikasi' ? 'active' : ''; ?>">
                                        <select name="news_id" <?php echo $report['status'] === 'publikasi' ? 'required' : ''; ?>>
                                            <option value="">Pilih Berita</option>
                                            <?php foreach ($news_data as $news): ?>
                                                <option value="<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($report['news_id']) && $report['news_id'] == $news['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($news['judul'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_status" class="action-btn">Update</button>
                                </form>
                                <a href="?action=admin/lapor&view_report=<?php echo htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8'); ?>" class="action-btn">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">Tidak ada laporan tersedia.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="report-details-overlay" style="display: none;">
            <div class="report-details-card">
                <button class="close-button" title="Tutup">×</button>
                <h3>Detail Laporan</h3>
                <?php if ($report_details): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($report_details['id'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Pengguna:</strong> <?php echo htmlspecialchars($report_details['user_email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Judul:</strong> <?php echo htmlspecialchars($report_details['judul'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Isi:</strong> <?php echo htmlspecialchars($report_details['isi'] ?? 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Kategori:</strong> <?php echo htmlspecialchars($report_details['kategori'] ?? 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Sumber:</strong> <?php echo htmlspecialchars($report_details['sumber'] ?? 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Tanggal Dibuat:</strong> <?php echo htmlspecialchars($report_details['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($report_details['status'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if (isset($report_details['news_id']) && $report_details['news_id']): ?>
                        <p><strong>Berita Terkait:</strong>
                            <?php
                            $news_title = 'Tidak Ditemukan';
                            foreach ($news_data as $news) {
                                if ($news['id'] == $report_details['news_id']) {
                                    $news_title = $news['judul'];
                                    break;
                                }
                            }
                            echo htmlspecialchars($news_title, ENT_QUOTES, 'UTF-8');
                            ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportDetailsOverlay = document.querySelector('.report-details-overlay');
            const closeButton = document.querySelector('.close-button');
            const statusSelects = document.querySelectorAll('.status-select');

            // Handle status select change
            statusSelects.forEach(select => {
                const form = select.closest('.status-form');
                const newsSelect = form.querySelector('.news-select');
                const newsSelectInput = newsSelect.querySelector('select');

                if (select.value === 'publikasi') {
                    newsSelect.classList.add('active');
                    newsSelectInput.setAttribute('required', 'required');
                }

                select.addEventListener('change', function() {
                    if (this.value === 'publikasi') {
                        newsSelect.classList.add('active');
                        newsSelectInput.setAttribute('required', 'required');
                    } else {
                        newsSelect.classList.remove('active');
                        newsSelectInput.removeAttribute('required');
                    }
                });
            });

            // Show overlay if view_report is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('view_report')) {
                reportDetailsOverlay.style.display = 'block';
            }

            // Handle close button
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    reportDetailsOverlay.style.display = 'none';
                    window.history.pushState({}, '', '?action=admin/lapor');
                });
            }

            // Handle overlay click to close
            if (reportDetailsOverlay) {
                reportDetailsOverlay.addEventListener('click', function(e) {
                    if (e.target === reportDetailsOverlay) {
                        reportDetailsOverlay.style.display = 'none';
                        window.history.pushState({}, '', '?action=admin/lapor');
                    }
                });
            }

            // Prevent form submission if news_id is required but empty
            document.querySelectorAll('.status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const statusSelect = form.querySelector('.status-select');
                    const newsSelectInput = form.querySelector('.news-select select');
                    if (statusSelect.value === 'publikasi' && !newsSelectInput.value) {
                        e.preventDefault();
                        alert('Harap pilih berita untuk status Publikasi.');
                    }
                });
            });
        });
    </script>
