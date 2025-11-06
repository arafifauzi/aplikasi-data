<?php
// views/partials/footer.php
// Pastikan sesi sudah dimulai (biasanya di index.php, tapi tambahkan sebagai cadangan)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan tujuan tautan logo berdasarkan peran pengguna
$logo_link = 'index.php'; // Default ke beranda
if (isset($_SESSION['session_email']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        $logo_link = 'index.php?action=admin/news'; // Rute untuk admin
    } else {
        $logo_link = 'index.php'; // Rute untuk user biasa
    }
}
?>

<div class="footer">
    <div class="footer-content">
        <div class="footer-text">
            <div class="logo">
                <a href="<?php echo htmlspecialchars($logo_link, ENT_QUOTES, 'UTF-8'); ?>">
                    <img src="../../../projekfixhoax/public/images/logo.png" alt="Fix Hoax Logo">
                </a>
            </div>
            <p>Made by LUNARHIA Group</p>
            <p>© 2025 FILKOM, Universitas Brawijaya. All rights reserved.</p>
        </div>
        <div class="social-icons">
            <a href="#"><img src="../../../projekfixhoax/public/images/twitter.png" alt="X"></a>
            <a href="#"><img src="../../../projekfixhoax/public/images/instagram.png" alt="Instagram"></a>
            <a href="#"><img src="../../../projekfixhoax/public/images/tiktok.png" alt="TikTok"></a>
            <a href="#"><img src="../../../projekfixhoax/public/images/facebook.png" alt="Facebook"></a>
        </div>
    </div>
</div>