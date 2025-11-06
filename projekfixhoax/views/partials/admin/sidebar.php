<div class="sidebar">

    <a href="index.php?action=admin/dashboard" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i>📊</i> Dashboard</a>
    <a href="index.php?action=admin/news" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_news.php' ? 'active' : ''; ?>"><i>📰</i> Berita</a>
    <a href="index.php?action=admin/artikel" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_artikel.php' ? 'active' : ''; ?>"><i>📝</i> Artikel</a>
    <a href="index.php?action=admin/lapor" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_lapor.php' ? 'active' : ''; ?>"><i>📋</i> Lapor</a>

    <a href="index.php?action=logout" class="logout-btn">Logout <i>⤶</i></a>
</div>