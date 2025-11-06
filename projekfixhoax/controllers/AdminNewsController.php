<?php
// controllers/AdminNewsController.php
require_once 'models/News.php';
require_once 'models/Article.php';
require_once 'models/User.php';

class AdminNewsController
{
    private $newsModel;
    private $articleModel;
    private $userModel;
    private $upload_dir = 'public/Uploads/';
    private $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    private $max_file_size = 2 * 1024 * 1024; // 2MB
    private $tema_options = ['Politik', 'Kesehatan', 'Teknologi', 'Ekonomi', 'Pendidikan', 'Lingkungan', 'Sosial'];
    private $per_page = 10;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        try {
            $this->newsModel = new News();
            $this->articleModel = new Article();
            $this->userModel = new User();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal mengakses database. Silakan coba lagi nanti.';
            error_log("AdminNewsController: Initialization error: " . $e->getMessage(), 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=auth/login"); // Redirect ke login jika error
            exit();
        }
    }

    public function index()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.';
            error_log("AdminNewsController: Unauthorized access attempt by " . ($_SESSION['session_email'] ?? 'unknown'), 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=auth/login"); // Redirect ke login
            exit();
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $page = max(1, $page);

        try {
            $news_result = $this->newsModel->getNewsPaginated($page, $this->per_page, '');
            $news_data = $news_result['news'];
            $total_items = $news_result['total'];
            $total_pages = ceil($total_items / $this->per_page);
            $article_data = $this->articleModel->getAllArticles();
            $tema_options = $this->tema_options;

            // Hapus berita
            if (isset($_GET['delete_news']) && is_numeric($_GET['delete_news'])) {
                $id = (int)$_GET['delete_news'];
                try {
                    $news = $this->newsModel->getNewsById($id);
                    if (!$news) {
                        $_SESSION['error'] = 'Berita tidak ditemukan.';
                        header("Location: index.php?action=admin/news&tab=news");
                        exit();
                    }
                    $success = $this->newsModel->deleteNews($id);
                    if ($success) {
                        if (!empty($news['gambar']) && file_exists($this->upload_dir . $news['gambar'])) {
                            if (!unlink($this->upload_dir . $news['gambar'])) {
                                error_log("AdminNewsController: Gagal menghapus gambar {$this->upload_dir}{$news['gambar']}", 3, __DIR__ . '/../public/errors.log');
                            }
                        }
                        $_SESSION['success'] = 'Berita berhasil dihapus.';
                    } else {
                        $_SESSION['error'] = 'Gagal menghapus berita.';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Terjadi kesalahan saat menghapus berita.';
                    error_log("AdminNewsController: Delete error for ID $id: " . $e->getMessage(), 3, __DIR__ . '/../public/errors.log');
                }
                header("Location: index.php?action=admin/news&tab=news");
                exit();
            }

            // Tambah berita
            if (isset($_POST['add_news'])) {
                $judul = trim(filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_STRING));
                $isi = trim(filter_input(INPUT_POST, 'isi', FILTER_SANITIZE_STRING));
                $keterangan = trim(filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING));
                $klarifikasi = trim(filter_input(INPUT_POST, 'klarifikasi', FILTER_SANITIZE_STRING));
                $penulis = trim(filter_input(INPUT_POST, 'penulis', FILTER_SANITIZE_STRING));
                $tanggal = trim(filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING));
                $tema = trim(filter_input(INPUT_POST, 'tema', FILTER_SANITIZE_STRING));
                $artikel_ids = isset($_POST['artikel']) ? array_map('intval', $_POST['artikel']) : [];
                $gambar = null;

                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['gambar'];
                    if (in_array($file['type'], $this->allowed_types) && $file['size'] <= $this->max_file_size) {
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $gambar = 'news_' . time() . '.' . strtolower($ext);
                        if (!is_dir($this->upload_dir)) {
                            mkdir($this->upload_dir, 0755, true);
                        }
                        if (!move_uploaded_file($file['tmp_name'], $this->upload_dir . $gambar)) {
                            $_SESSION['error'] = 'Gagal mengunggah gambar.';
                            header("Location: index.php?action=admin/news&tab=add");
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = 'Gambar harus JPG/PNG dan maksimal 2MB.';
                        header("Location: index.php?action=admin/news&tab=add");
                        exit();
                    }
                }

                if (empty($judul) || empty($isi) || !in_array($keterangan, ['HOAX', 'FAKTA']) || empty($klarifikasi) || empty($penulis) || empty($tanggal) || !in_array($tema, $this->tema_options)) {
                    $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
                    header("Location: index.php?action=admin/news&tab=add");
                    exit();
                }

                $valid_artikel_ids = array_filter($artikel_ids, fn($id) => in_array($id, array_column($article_data, 'id')));
                $artikel_json = json_encode($valid_artikel_ids);

                if ($this->newsModel->addNews($judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $valid_artikel_ids, $gambar)) {
                    $_SESSION['success'] = 'Berita berhasil ditambahkan.';
                } else {
                    if ($gambar && file_exists($this->upload_dir . $gambar)) {
                        unlink($this->upload_dir . $gambar);
                    }
                    $_SESSION['error'] = 'Gagal menambahkan berita.';
                }
                header("Location: index.php?action=admin/news&tab=news");
                exit();
            }

            // Perbarui berita
            if (isset($_POST['update_news'])) {
                $id = (int)$_POST['id'];
                $judul = trim(filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_STRING));
                $isi = trim(filter_input(INPUT_POST, 'isi', FILTER_SANITIZE_STRING));
                $keterangan = trim(filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING));
                $klarifikasi = trim(filter_input(INPUT_POST, 'klarifikasi', FILTER_SANITIZE_STRING));
                $penulis = trim(filter_input(INPUT_POST, 'penulis', FILTER_SANITIZE_STRING));
                $tanggal = trim(filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING));
                $tema = trim(filter_input(INPUT_POST, 'tema', FILTER_SANITIZE_STRING));
                $artikel_ids = isset($_POST['artikel']) ? array_map('intval', $_POST['artikel']) : [];
                $gambar = $_POST['existing_gambar'] ?? null;

                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['gambar'];
                    if (in_array($file['type'], $this->allowed_types) && $file['size'] <= $this->max_file_size) {
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $gambar = 'news_' . time() . '.' . strtolower($ext);
                        if (!is_dir($this->upload_dir)) {
                            mkdir($this->upload_dir, 0755, true);
                        }
                        if (move_uploaded_file($file['tmp_name'], $this->upload_dir . $gambar)) {
                            if (!empty($_POST['existing_gambar']) && file_exists($this->upload_dir . $_POST['existing_gambar'])) {
                                unlink($this->upload_dir . $_POST['existing_gambar']);
                            }
                        } else {
                            $_SESSION['error'] = 'Gagal mengunggah gambar.';
                            header("Location: index.php?action=admin/news&tab=news");
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = 'Gambar harus JPG/PNG dan maksimal 2MB.';
                        header("Location: index.php?action=admin/news&tab=news");
                        exit();
                    }
                }

                if (empty($judul) || empty($isi) || !in_array($keterangan, ['HOAX', 'FAKTA']) || empty($klarifikasi) || empty($penulis) || empty($tanggal) || !in_array($tema, $this->tema_options)) {
                    $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
                    header("Location: index.php?action=admin/news&tab=news");
                    exit();
                }

                $valid_artikel_ids = array_filter($artikel_ids, fn($id) => in_array($id, array_column($article_data, 'id')));
                $artikel_json = json_encode($valid_artikel_ids);

                if ($this->newsModel->updateNews($id, $judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $valid_artikel_ids, $gambar)) {
                    $_SESSION['success'] = 'Berita berhasil diperbarui.';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui berita.';
                }
                header("Location: index.php?action=admin/news&tab=news");
                exit();
            }

            require_once 'views/admin/admin_news.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal memuat data berita. Silakan coba lagi nanti.';
            error_log("AdminNewsController: Index error: " . $e->getMessage(), 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=auth/login"); // Redirect ke login jika error
            exit();
        }
    }
}
?>