<?php
// controllers/NewsController.php
require_once 'models/News.php';
require_once 'models/Article.php';
require_once 'models/User.php';
require_once 'models/Comment.php';

class NewsController
{
    private $newsModel;
    private $articleModel;
    private $userModel;
    private $commentModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->articleModel = new Article();
        $this->userModel = new User();
        $this->commentModel = new Comment();
    }

    public function news()
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 9;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $result = $this->newsModel->getNewsPaginated($page, $per_page, $search);
        $news_items = $result['news'];
        $total_items = $result['total'];
        $total_pages = ceil($total_items / $per_page);
        $current_page = $page;

        if (empty($news_items)) {
            error_log("NewsController::news: No news found for search: '$search', page: $page", 3, __DIR__ . '/../public/errors.log');
        }

        require_once 'views/news.php';
    }

    public function detail()
    {
        if (!isset($_SESSION['session_email']) || empty($_SESSION['session_email'])) {
            $_SESSION['error'] = "Silakan login untuk melihat detail berita";
            error_log("NewsController::detail: No session email set", 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=auth/login");
            exit();
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID berita tidak valid";
            error_log("NewsController::detail: Invalid or missing news ID: " . ($_GET['id'] ?? 'unset'), 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=news");
            exit();
        }

        $news_id = (int)$_GET['id'];
        $news = $this->newsModel->getNewsById($news_id);

        if (!$news || !is_array($news)) {
            $_SESSION['error'] = "Berita tidak ditemukan";
            error_log("NewsController::detail: News not found for ID: $news_id", 3, __DIR__ . '/../public/errors.log');
            header("Location: index.php?action=news");
            exit();
        }

        $this->newsModel->incrementViews($news_id);

        $artikel_ids = json_decode($news['artikel'] ?? '[]', true) ?: [];
        $articles = [];
        foreach ($artikel_ids as $id) {
            $article = $this->articleModel->getArticleById($id);
            if ($article) {
                $articles[] = $article;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
            $comment_content = trim($_POST['comment'] ?? '');
            $email = $_SESSION['session_email'];
            $comment_date = date('Y-m-d H:i:s');

            if (empty($comment_content)) {
                $_SESSION['error'] = "Komentar tidak boleh kosong";
                error_log("NewsController::detail: Empty comment submitted for news_id: $news_id", 3, __DIR__ . '/../public/errors.log');
            } else {
                if ($this->commentModel->addComment($news_id, $email, $comment_content, $comment_date)) {
                    $_SESSION['success'] = "Komentar berhasil dikirim";
                } else {
                    $_SESSION['error'] = "Gagal menyimpan komentar";
                    error_log("NewsController::detail: Failed to save comment for news_id: $news_id, user: $email", 3, __DIR__ . '/../public/errors.log');
                }
            }
            header("Location: index.php?action=news_detail&id=$news_id");
            exit();
        }

        if (isset($_GET['reaction']) && isset($_GET['comment_id']) && is_numeric($_GET['comment_id'])) {
            $comment_id = (int)$_GET['comment_id'];
            $reaction_type = $_GET['reaction'] === 'like' ? 'like' : 'dislike';
            $email = $_SESSION['session_email'];
            $reaction_date = date('Y-m-d H:i:s');

            $existing_reaction = $this->commentModel->getReaction($comment_id, $email);
            if ($existing_reaction) {
                if ($existing_reaction !== $reaction_type) {
                    $this->commentModel->updateReaction($comment_id, $email, $reaction_type, $reaction_date);
                }
            } else {
                $this->commentModel->addReaction($comment_id, $email, $reaction_type, $reaction_date);
            }
            header("Location: index.php?action=news_detail&id=$news_id");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment']) && isset($_POST['comment_id']) && is_numeric($_POST['comment_id'])) {
            $comment_id = (int)$_POST['comment_id'];
            $new_content = trim($_POST['edit_content'] ?? '');

            if (empty($new_content)) {
                $_SESSION['error'] = "Komentar baru tidak boleh kosong";
                error_log("NewsController::detail: Empty edit content for comment_id: $comment_id", 3, __DIR__ . '/../public/errors.log');
            } else {
                if ($this->commentModel->updateComment($comment_id, $_SESSION['session_email'], $new_content)) {
                    $_SESSION['success'] = "Komentar berhasil diperbarui";
                } else {
                    $_SESSION['error'] = "Gagal mengedit komentar";
                    error_log("NewsController::detail: Failed to edit comment_id: $comment_id, user: {$_SESSION['session_email']}", 3, __DIR__ . '/../public/errors.log');
                }
            }
            header("Location: index.php?action=news_detail&id=$news_id");
            exit();
        }

        if (isset($_GET['delete_comment']) && is_numeric($_GET['delete_comment'])) {
            $comment_id = (int)$_GET['delete_comment'];
            $comment = $this->commentModel->getCommentById($comment_id);
            $user = $this->userModel->getUserByEmail($_SESSION['session_email']);

            if ($comment && ($user['role'] === 'admin' || $comment['user_email'] === $_SESSION['session_email'])) {
                if ($this->commentModel->deleteComment($comment_id)) {
                    $_SESSION['success'] = "Komentar berhasil dihapus";
                } else {
                    $_SESSION['error'] = "Gagal menghapus komentar";
                    error_log("NewsController::detail: Failed to delete comment_id: $comment_id", 3, __DIR__ . '/../public/errors.log');
                }
            } else {
                $_SESSION['error'] = "Anda tidak memiliki izin untuk menghapus komentar ini";
                error_log("NewsController::detail: Unauthorized delete attempt for comment_id: $comment_id by user: {$_SESSION['session_email']}", 3, __DIR__ . '/../public/errors.log');
            }
            header("Location: index.php?action=news_detail&id=$news_id");
            exit();
        }

        $comments = $this->commentModel->getCommentsByNewsId($news_id);
        foreach ($comments as &$comment) {
            $comment['likes'] = $this->commentModel->countReactions($comment['id'], 'like');
            $comment['dislikes'] = $this->commentModel->countReactions($comment['id'], 'dislike');
            $comment['user_reaction'] = $this->commentModel->getReaction($comment['id'], $_SESSION['session_email'] ?? '');
        }
        unset($comment);

        $popular_news = $this->newsModel->getPopularNews(5);
        if (empty($popular_news)) {
            error_log("NewsController::detail: No popular news returned for news_id: $news_id", 3, __DIR__ . '/../public/errors.log');
        }

        $userModel = $this->userModel;
        require_once 'views/news_detail.php';
    }
}
?>