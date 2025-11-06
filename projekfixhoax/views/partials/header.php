<!-- views/partials/header.php -->
<div class="navbar">
    <div class="navbar-content">
        <div class="logo-container">
            <a href="index.php?action=home">
                <div class="logo"><img src="../../../projekfixhoax/public/images/logo.png" alt="Fix Hoax Logo"></div>
            </a>
        </div>
        <ul>
            <li class="<?php echo ($action === 'home') ? 'active' : ''; ?>"><a href="index.php?action=home">Home</a></li>
            <li class="<?php echo ($action === 'news' || $action === 'news_detail') ? 'active' : ''; ?>"><a href="index.php?action=news">News</a></li>
            <li class="<?php echo ($action === 'report' || $action === 'report_detail') ? 'active' : ''; ?>"><a href="index.php?action=report">Report</a></li>
            <li class="<?php echo ($action === 'profile') ? 'active' : ''; ?>"><a href="index.php?action=profile">Profile</a></li>
        </ul>
        <div class="user-actions-container">
            <div class="user-actions">
                <a href="index.php?action=logout" class="logout-btn"><img src="../../../projekfixhoax/public/images/logout.png" alt="Logout"></a>
            </div>
        </div>
    </div>
</div>