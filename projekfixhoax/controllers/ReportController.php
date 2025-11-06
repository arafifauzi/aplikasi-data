<?php
// controllers/ReportController.php
require_once 'models/Report.php';
require_once 'models/User.php';
require_once 'models/News.php';

class ReportController
{
    private $reportModel;
    private $userModel;
    private $newsModel;

    public function __construct()
    {
        $this->reportModel = new Report();
        $this->userModel = new User();
        $this->newsModel = new News();
    }

    public function report()
    {
        if (!isset($_SESSION['session_email'])) {
            $_SESSION['error'] = "Silakan login untuk melaporkan";
            header("Location: index.php?action=auth/login");
            exit();
        }

        $user = $this->userModel->getUserByEmail($_SESSION['session_email']);
        if (!$user) {
            $_SESSION['error'] = "Pengguna tidak ditemukan";
            header("Location: index.php?action=auth/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
            $email_user = $_SESSION['session_email'];
            $judul = trim($_POST['judul'] ?? '');
            $link_referensi = trim($_POST['link_referensi'] ?? '');
            $tanggapan_alasan = trim($_POST['tanggapan_alasan'] ?? '');
            $status = 'terkirim';
            $bukti_gambar_video = '';

            if (isset($_FILES['bukti_gambar_video']) && $_FILES['bukti_gambar_video']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'public/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_name = time() . '_' . basename($_FILES['bukti_gambar_video']['name']);
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['bukti_gambar_video']['tmp_name'], $file_path)) {
                    $bukti_gambar_video = $file_name;
                } else {
                    $_SESSION['error'] = "Gagal mengunggah file";
                    header("Location: index.php?action=report");
                    exit();
                }
            }

            if (empty($judul) || empty($tanggapan_alasan)) {
                $_SESSION['error'] = "Judul dan tanggapan/alasan wajib diisi";
            } else {
                if ($this->reportModel->addReport($email_user, $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan, $status)) {
                    $_SESSION['success'] = "Laporan berhasil dikirim";
                    header("Location: index.php?action=report");
                    exit();
                } else {
                    $_SESSION['error'] = "Gagal mengirim laporan";
                }
            }
        }

        if ($user['role'] === 'admin') {
            $reports = $this->reportModel->getAllReports();
        } else {
            $reports = $this->reportModel->getReportsByUser($_SESSION['session_email']);
        }

        if (empty($reports)) {
            error_log("ReportController::report: No reports returned for user: {$_SESSION['session_email']}", 3, "public/errors.log");
        }

        require_once 'views/report.php';
    }

    public function reportDetail()
    {
        if (!isset($_SESSION['session_email'])) {
            $_SESSION['error'] = "Silakan login untuk melihat detail laporan";
            header("Location: index.php?action=auth/login");
            exit();
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID laporan tidak valid";
            header("Location: index.php?action=report");
            exit();
        }

        $report_id = (int)$_GET['id'];
        $report = $this->reportModel->getReportById($report_id);

        if (!$report) {
            $_SESSION['error'] = "Laporan tidak ditemukan";
            header("Location: index.php?action=report");
            exit();
        }

        $user = $this->userModel->getUserByEmail($_SESSION['session_email']);
        if ($report['email_user'] !== $_SESSION['session_email'] && $user['role'] !== 'admin') {
            $_SESSION['error'] = "Anda tidak memiliki akses ke laporan ini";
            header("Location: index.php?action=report");
            exit();
        }

        $related_news = $report['news_id'] ? $this->newsModel->getNewsById($report['news_id']) : null;
        require_once 'views/report_detail.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan.';
            header('Location: index.php?action=report');
            exit;
        }

        try {
            // Validate session
            if (!isset($_SESSION['session_email'])) {
                throw new Exception('Anda harus login untuk mengedit laporan.');
            }

            // Validate POST data
            if (!isset($_POST['report_id'], $_POST['judul'], $_POST['tanggapan_alasan'], $_POST['update_report'])) {
                throw new Exception('Data yang diperlukan tidak lengkap.');
            }

            $report_id = filter_var($_POST['report_id'], FILTER_VALIDATE_INT);
            $judul = trim($_POST['judul']);
            $link_referensi = isset($_POST['link_referensi']) ? trim($_POST['link_referensi']) : null;
            $tanggapan_alasan = trim($_POST['tanggapan_alasan']);

            if (!$report_id || !$judul || !$tanggapan_alasan) {
                throw new Exception('Judul dan tanggapan/alasan wajib diisi.');
            }

            // Check report ownership and status
            $report = $this->reportModel->getReportById($report_id);
            if (!$report) {
                throw new Exception('Laporan tidak ditemukan.');
            }
            if ($report['email_user'] !== $_SESSION['session_email']) {
                throw new Exception('Anda tidak memiliki izin untuk mengedit laporan ini.');
            }
            if ($report['status'] !== 'terkirim') {
                throw new Exception('Laporan tidak dapat diedit karena sudah dalam proses verifikasi.');
            }

            // Handle file upload
            $bukti_gambar_video = $report['bukti_gambar_video'];
            if (isset($_FILES['bukti_gambar_video']) && $_FILES['bukti_gambar_video']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'public/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_name = time() . '_' . basename($_FILES['bukti_gambar_video']['name']);
                $file_path = $upload_dir . $file_name;

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
                $max_size = 5 * 1024 * 1024; // 5MB
                if (!in_array($_FILES['bukti_gambar_video']['type'], $allowed_types) || $_FILES['bukti_gambar_video']['size'] > $max_size) {
                    throw new Exception('File tidak valid atau terlalu besar.');
                }

                if (!move_uploaded_file($_FILES['bukti_gambar_video']['tmp_name'], $file_path)) {
                    throw new Exception('Gagal mengunggah file.');
                }

                $bukti_gambar_video = $file_name;

                // Delete old file if exists
                if ($report['bukti_gambar_video'] && file_exists($upload_dir . $report['bukti_gambar_video'])) {
                    unlink($upload_dir . $report['bukti_gambar_video']);
                }
            }

            // Update report
            if (!$this->reportModel->updateReport($report_id, $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan)) {
                throw new Exception('Gagal memperbarui laporan di database.');
            }

            $_SESSION['success'] = 'Laporan berhasil diperbarui.';
        } catch (Exception $e) {
            error_log("ReportController::update: " . $e->getMessage(), 3, __DIR__ . '/../../public/errors.log');
            $_SESSION['error'] = 'Gagal memperbarui laporan: ' . $e->getMessage();
        }

        header('Location: index.php?action=report_detail&id=' . $report_id);
        exit;
    }

    public function reportApprove()
    {
        if (!isset($_SESSION['session_email']) || $this->userModel->getUserByEmail($_SESSION['session_email'])['role'] !== 'admin') {
            $_SESSION['error'] = "Akses ditolak";
            header("Location: index.php?action=report");
            exit();
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID laporan tidak valid";
            header("Location: index.php?action=report");
            exit();
        }

        $report_id = (int)$_GET['id'];
        $report = $this->reportModel->getReportById($report_id);
        if (!$report) {
            $_SESSION['error'] = "Laporan tidak ditemukan";
            header("Location: index.php?action=report");
            exit();
        }

        $news_id = null; // Implement news creation logic here
        if ($this->reportModel->updateReportStatus($report_id, 'disetujui', $news_id)) {
            $_SESSION['success'] = "Laporan disetujui";
        } else {
            $_SESSION['error'] = "Gagal menyetujui laporan";
        }
        header("Location: index.php?action=report");
        exit();
    }

    public function reportReject()
    {
        if (!isset($_SESSION['session_email']) || $this->userModel->getUserByEmail($_SESSION['session_email'])['role'] !== 'admin') {
            $_SESSION['error'] = "Akses ditolak";
            header("Location: index.php?action=report");
            exit();
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID laporan tidak valid";
            header("Location: index.php?action=report");
            exit();
        }

        $report_id = (int)$_GET['id'];
        if ($this->reportModel->updateReportStatus($report_id, 'ditolak')) {
            $_SESSION['success'] = "Laporan ditolak";
        } else {
            $_SESSION['error'] = "Gagal menolak laporan";
        }
        header("Location: index.php?action=report");
        exit();
    }
}
?>