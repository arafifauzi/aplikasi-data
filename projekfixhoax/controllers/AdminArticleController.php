<?php
// controllers/AdminArticleController.php
require_once 'models/Article.php';
require_once 'models/User.php';

class AdminArticleController
{
    private $articleModel;
    private $userModel;

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->userModel = new User();
    }

    public function index()
    {
        // Ambil semua artikel
        $article_data = $this->articleModel->getAllArticles();
        error_log("Article data fetched: " . print_r($article_data, true), 3, 'public/errors.log');
        $edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;

        // Handle penghapusan artikel
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $id = (int)$_GET['delete'];
            if ($this->articleModel->deleteArticle($id)) {
                $_SESSION['success'] = 'Artikel berhasil dihapus.';
            } else {
                $_SESSION['error'] = 'Gagal menghapus artikel.';
            }
            header("Location: index.php?action=admin/artikel");
            exit();
        }

        // Handle pembaruan artikel
        if (isset($_POST['update'])) {
            $id = (int)$_POST['id'];
            $judul = trim($_POST['judul_artikel']);
            $link = trim($_POST['link_artikel']);
            if (!empty($judul) && !empty($link)) {
                if ($this->articleModel->updateArticle($id, $judul, $link)) {
                    $_SESSION['success'] = 'Artikel berhasil diperbarui.';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui artikel.';
                }
            } else {
                $_SESSION['error'] = 'Judul dan link artikel wajib diisi.';
            }
            header("Location: index.php?action=admin/artikel");
            exit();
        }

        // Handle penambahan artikel
        if (isset($_POST['add_article'])) {
            $judul = trim($_POST['judul_artikel']);
            $link = trim($_POST['link_artikel']);
            if (!empty($judul) && !empty($link)) {
                if ($this->articleModel->addArticle($judul, $link)) {
                    $_SESSION['success'] = 'Artikel berhasil ditambahkan.';
                } else {
                    $_SESSION['error'] = 'Gagal menambahkan artikel.';
                }
            } else {
                $_SESSION['error'] = 'Judul dan link artikel wajib diisi.';
            }
            header("Location: index.php?action=admin/artikel");
            exit();
        }

        // Pastikan $article_data selalu terdefinisi
        $article_data = $article_data ?: [];
        require_once 'views/admin/admin_artikel.php';
    }
}
