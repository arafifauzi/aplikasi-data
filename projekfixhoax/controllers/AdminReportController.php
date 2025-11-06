<?php
// controllers/AdminReportController.php
require_once 'models/Report.php';
require_once 'models/News.php';
require_once 'models/User.php';

class AdminReportController
{
    private $reportModel;
    private $newsModel;
    private $userModel;

    public function __construct()
    {
        $this->reportModel = new Report();
        $this->newsModel = new News();
        $this->userModel = new User();
    }

    public function index()
    {
        $reports = $this->reportModel->getAllReports();
        $news_data = $this->newsModel->getAllNews();
        $report_details = null;

        if (isset($_GET['view_report']) && is_numeric($_GET['view_report'])) {
            $report_id = (int)$_GET['view_report'];
            $report_details = $this->reportModel->getReportById($report_id);
            if (!$report_details) {
                $_SESSION['error'] = 'Laporan tidak ditemukan.';
                header("Location: index.php?action=admin/lapor");
                exit();
            }
        }

        if (isset($_POST['update_status'])) {
            $report_id = (int)$_POST['report_id'];
            $new_status = $_POST['status'];
            $news_id = isset($_POST['news_id']) && !empty($_POST['news_id']) ? (int)$_POST['news_id'] : null;

            if ($new_status === 'publikasi' && (!$news_id || !in_array($news_id, array_column($news_data, 'id')))) {
                $_SESSION['error'] = 'Pilih artikel berita yang valid untuk publikasi.';
            } else {
                if ($this->reportModel->updateReportStatus($report_id, $new_status, $news_id)) {
                    $_SESSION['success'] = 'Status laporan berhasil diperbarui.';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui status laporan.';
                }
            }
            header("Location: index.php?action=admin/lapor");
            exit();
        }

        require_once 'views/admin/admin_lapor.php';
    }
}
?>