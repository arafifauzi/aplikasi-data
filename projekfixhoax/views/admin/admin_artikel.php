<?php
// public/admin/admin_artikel.php
// Session is managed in index.php, so only check if session is active
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log("admin_artikel.php: No active session", 3, __DIR__ . '/../errors.log');
    header("Location: ../index.php?action=auth/login");
    exit();
}

// Initialize variables to prevent undefined errors
$article_data = isset($article_data) ? $article_data : [];
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Admin Artikel</title>
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_base.css">
    <link rel="stylesheet" href="../../../projekfixhoax/public/css/admin_artikel.css">
</head>

<body>
    <div class="main-content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8');
                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="header">
            <h2>Manajemen Artikel</h2>
        </div>

        <div class="tabs">
            <button class="tab-button" data-tab="articles">Daftar Artikel</button>
            <button class="tab-button" data-tab="add">Tambah Artikel</button>
        </div>

        <div class="articles-section" style="display: <?php echo isset($_GET['tab']) && $_GET['tab'] === 'add' ? 'none' : 'block'; ?>;">
            <table class="article-table dashboard-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul Artikel</th>
                        <th>Link Artikel</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($article_data) && is_array($article_data)): ?>
                        <?php foreach ($article_data as $article): ?>
                            <tr class="clickable" data-id="<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <td><?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if ($edit_id == $article['id']): ?>
                                    <td colspan="2">
                                        <form method="post" class="edit-form">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="text" name="judul_artikel" value="<?php echo htmlspecialchars($article['judul_artikel'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                            <input type="text" name="link_artikel" value="<?php echo htmlspecialchars($article['link_artikel'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                            <div class="edit-actions">
                                                <button type="submit" name="update" class="action-btn">Simpan</button>
                                                <a href="?action=admin/artikel" class="action-btn">Batal</a>
                                            </div>
                                        </form>
                                    </td>
                                <?php else: ?>
                                    <td><?php echo htmlspecialchars($article['judul_artikel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><a href="<?php echo htmlspecialchars($article['link_artikel'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($article['link_artikel'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                <?php endif; ?>
                                <td class="action-column">
                                    <?php if ($edit_id == $article['id']): ?>
                                    <?php else: ?>
                                        <a href="?action=admin/artikel&edit=<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>" class="action-btn">Edit</a>
                                        <?php if (isset($_GET['confirm_delete']) && (int)$_GET['confirm_delete'] == $article['id']): ?>
                                            <div class="confirmation">
                                                <p>Yakin ingin menghapus artikel ini?</p>
                                                <div style="display: flex; gap: 12px;">
                                                    <a href="?action=admin/artikel&delete=<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>" class="action-btn">Ya</a>
                                                    <a href="?action=admin/artikel" class="action-btn">Tidak</a>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <a href="?action=admin/artikel&confirm_delete=<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>" class="action-btn">Hapus</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">Tidak ada artikel tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="add-form-section" style="display: <?php echo isset($_GET['tab']) && $_GET['tab'] === 'add' ? 'block' : 'none'; ?>;">
            <div class="add-form">
                <h3>Tambah Artikel Baru</h3>
                <form method="post">
                    <label for="judul_artikel">Judul Artikel:</label>
                    <input type="text" name="judul_artikel" placeholder="Judul Artikel" required>
                    <label for="link_artikel">Link Artikel:</label>
                    <input type="text" name="link_artikel" placeholder="Link Artikel" required>
                    <button type="submit" name="add_article" class="action-btn">Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const articlesSection = document.querySelector('.articles-section');
            const addFormSection = document.querySelector('.add-form-section');

            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'articles';
            tabs.forEach(tab => {
                if (tab.dataset.tab === activeTab) tab.classList.add('active');
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    articlesSection.style.display = this.dataset.tab === 'articles' ? 'block' : 'none';
                    addFormSection.style.display = this.dataset.tab === 'add' ? 'block' : 'none';
                    window.history.pushState({}, '', `?action=admin/artikel&tab=${this.dataset.tab}`);
                });
            });
            document.querySelector(`[data-tab="${activeTab}"]`).click();
        });
    </script>