<?php
session_start();
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = null;
}
if (!isset($_SESSION['session_email'])) {
    $_SESSION['session_email'] = null;
}

// Session timeout
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['error'] = 'Sesi Anda telah kedaluwarsa. Silakan login kembali.';
    header("Location: index.php?action=auth/login");
    exit();
}
$_SESSION['last_activity'] = time();

try {
    require_once 'controllers/AuthController.php';
    require_once 'controllers/HomeController.php';
    require_once 'controllers/ProfileController.php';
    require_once 'controllers/NewsController.php';
    require_once 'controllers/ReportController.php';
    require_once 'controllers/AdminNewsController.php';
    require_once 'controllers/AdminArticleController.php';
    require_once 'controllers/AdminReportController.php';
    require_once 'models/User.php';
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal memuat aplikasi. Silakan coba lagi nanti.';
    error_log("index.php: Initialization error: " . $e->getMessage(), 3, __DIR__ . '/public/errors.log');
    header("Location: index.php?action=auth/login");
    exit();
}

// Public routes
$public_routes = ['auth/login', 'auth/register', 'logout'];
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?: 'home';

// Check login requirement
if (!in_array($action, $public_routes) && !isset($_SESSION['session_email']) && $_SESSION['session_email'] === null) {
    $_SESSION['error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: index.php?action=auth/login");
    exit();
}

// Check admin routes
$admin_routes = ['admin/news', 'admin/artikel', 'admin/lapor'];
if (in_array($action, $admin_routes)) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.';
        header("Location: index.php?action=home");
        exit();
    }
}

// Router
try {
    switch ($action) {
        case 'auth/login':
            $controller = new AuthController();
            $controller->login();
            break;
        case 'auth/register':
            $controller = new AuthController();
            $controller->register();
            break;
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
        case 'home':
            include 'views/partials/header.php';
            $controller = new HomeController();
            $controller->index();
            include 'views/partials/footer.php';
            break;
        case 'profile':
            include 'views/partials/header.php';
            $controller = new ProfileController();
            $controller->index();
            include 'views/partials/footer.php';
            break;
        case 'news':
            include 'views/partials/header.php';
            $controller = new NewsController();
            $controller->news();
            include 'views/partials/footer.php';
            break;
        case 'news_detail':
            include 'views/partials/header.php';
            $controller = new NewsController();
            $controller->detail();
            include 'views/partials/footer.php';
            break;
        case 'report':
            include 'views/partials/header.php';
            $controller = new ReportController();
            $controller->report();
            include 'views/partials/footer.php';
            break;
        case 'report_detail':
            include 'views/partials/header.php';
            $controller = new ReportController();
            $controller->reportDetail();
            include 'views/partials/footer.php';
            break;
        case 'report_update':
            $controller = new ReportController();
            $controller->update();
            break;
        case 'admin/news':
            include 'views/partials/admin/sidebar.php';
            $controller = new AdminNewsController();
            $controller->index();
            break;
        case 'admin/artikel':
            include 'views/partials/admin/sidebar.php';
            $controller = new AdminArticleController();
            $controller->index();
            break;
        case 'admin/lapor':
            include 'views/partials/admin/sidebar.php';
            $controller = new AdminReportController();
            $controller->index();
            break;
        default:
            $_SESSION['error'] = 'Halaman tidak ditemukan.';
            error_log("index.php: Invalid action: $action", 3, __DIR__ . '/public/errors.log');
            header("Location: index.php?action=home");
            exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
    error_log("index.php: Runtime error: " . $e->getMessage(), 3, __DIR__ . '/public/errors.log');
    header("Location: index.php?action=auth/login");
    exit();
}
