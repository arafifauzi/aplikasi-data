<?php
require_once 'views/partials/header.php';

// Ensure userModel is available
if (!isset($userModel)) {
    require_once 'models/User.php';
    $userModel = new User();
}
$user = isset($_SESSION['session_email']) && !empty($_SESSION['session_email']) ? $userModel->getUserByEmail($_SESSION['session_email']) : null;

// Log if news is undefined
if (!isset($news) || !is_array($news) || empty($news)) {
    error_log("news_detail.php: \$news is undefined, not an array, or empty, ID: " . ($_GET['id'] ?? 'unset'), 3, __DIR__ . '/../../public/errors.log');
    $news = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - News Detail</title>
    <link rel="stylesheet" href="public/css/partials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/news_detail.css?v=<?php echo time(); ?>">
    <script>
        function toggleEditForm(commentId) {
            const form = document.getElementById(`edit-form-${commentId}`);
            if (form) {
                form.classList.toggle("active");
            }
        }

        function handleFormSubmit(form) {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = form.querySelector('textarea[name="comment"]') ? 'Mengirim...' : 'Menyimpan...';
            }

            const comment = form.querySelector('textarea[name="comment"]')?.value.trim();
            if (comment && comment.length < 1) {
                alert('Komentar tidak boleh kosong.');
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Kirim Komentar';
                }
                return false;
            }

            const editContent = form.querySelector('textarea[name="edit_content"]')?.value.trim();
            if (editContent && editContent.length < 1) {
                alert('Komentar yang diedit tidak boleh kosong.');
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Simpan Perubahan';
                }
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <?php if ($news && is_array($news) && !empty($news)): ?>
        <div class="subheader">
            Informasi ini adalah
            <span style="color: <?php echo ($news['keterangan'] === 'HOAX') ? '#ff0000' : '#008000'; ?>">
                <?php echo htmlspecialchars($news['keterangan'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>

        <div class="container">
            <div class="main-content">
                <div class="news-detail">
                    <h1><?php echo htmlspecialchars($news['judul'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="date"><?php echo htmlspecialchars($news['penulis'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo date('d M Y', strtotime($news['tanggal'])); ?></p>
                    <?php if ($news['gambar'] && file_exists(__DIR__ . '/../../public/Uploads/' . $news['gambar'])): ?>
                        <img class="image" src="public/Uploads/<?php echo htmlspecialchars($news['gambar'], ENT_QUOTES, 'UTF-8'); ?>" alt="News Image">
                    <?php else: ?>
                        <img class="image" src="https://via.placeholder.com/800x500" alt="No Image">
                    <?php endif; ?>
                    <?php
                    $paragraphs = array_filter(array_map('trim', explode("\n", $news['isi'])));
                    foreach ($paragraphs as $para): ?>
                        <p><?php echo htmlspecialchars($para, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                    <p><strong>Klarifikasi & Penjelasan:</strong> <?php echo nl2br(htmlspecialchars($news['klarifikasi'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <p><strong>Artikel Pendukung:</strong></p>
                    <ul>
                        <?php if (!empty($articles) && is_array($articles)): ?>
                            <?php foreach ($articles as $artikel): ?>
                                <li><a href="<?php echo htmlspecialchars($artikel['link_artikel'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($artikel['judul_artikel'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Tidak ada artikel pendukung.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="comment-section">
                    <h3>Komentar</h3>
                    <div class="comment-form">
                        <?php if (isset($_SESSION['error'])): ?>
                            <p class="error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <p class="success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['session_email']) && !empty($_SESSION['session_email'])): ?>
                            <form method="post" action="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return handleFormSubmit(this)">
                                <textarea name="comment" rows="4" placeholder="Tulis komentar Anda..." required></textarea>
                                <button type="submit" name="submit_comment">Kirim Komentar</button>
                            </form>
                        <?php else: ?>
                            <p><a href="index.php?action=auth/login">Login</a> untuk menambahkan komentar.</p>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($comments) && is_array($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php
                            $comment_user = $userModel->getUserByEmail($comment['user_email']);
                            $display_name = $comment_user && isset($comment_user['name']) ? htmlspecialchars($comment_user['name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($comment['user_email'], ENT_QUOTES, 'UTF-8');
                            $canEdit = $user && is_array($user) && ($user['role'] === 'admin' || $_SESSION['session_email'] === $comment['user_email']);
                            ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <span class="name"><?php echo $display_name; ?></span>
                                    <span class="date"><?php echo date('d M Y, H:i', strtotime($comment['comment_date'])); ?></span>
                                </div>
                                <div class="comment-content"><?php echo nl2br(htmlspecialchars($comment['comment_content'], ENT_QUOTES, 'UTF-8')); ?></div>
                                <?php if ($comment['is_edited'] && !empty($comment['edited_content'])): ?>
                                    <p class="edited-note"><em>(Diedit: <?php echo nl2br(htmlspecialchars($comment['edited_content'], ENT_QUOTES, 'UTF-8')); ?>)</em></p>
                                <?php endif; ?>
                                <div class="comment-actions">
                                    <?php if (isset($_SESSION['session_email']) && !empty($_SESSION['session_email'])): ?>
                                        <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>&comment_id=<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>&reaction=like" class="reaction like-btn <?php echo ($comment['user_reaction'] === 'like' ? 'active' : ''); ?>">
                                            <span class="icon like-icon"></span>
                                            <span class="count"><?php echo (int)$comment['likes']; ?></span>
                                        </a>
                                        <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>&comment_id=<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>&reaction=dislike" class="reaction dislike-btn <?php echo ($comment['user_reaction'] === 'dislike' ? 'active' : ''); ?>">
                                            <span class="icon dislike-icon"></span>
                                            <span class="count"><?php echo (int)$comment['dislikes']; ?></span>
                                        </a>
                                        <?php if ($canEdit): ?>
                                            <a href="#" class="edit-btn" onclick="toggleEditForm(<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>); return false;">Edit</a>
                                            <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>&delete_comment=<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus komentar ini?');">Hapus</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($canEdit): ?>
                                    <div class="edit-form" id="edit-form-<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <form method="post" action="index.php?action=news_detail&id=<?php echo htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return handleFormSubmit(this)">
                                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <textarea name="edit_content" rows="4" required><?php echo htmlspecialchars($comment['comment_content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            <button type="submit" name="edit_comment">Simpan Perubahan</button>
                                            <button type="button" onclick="toggleEditForm(<?php echo htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8'); ?>)">Batal</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sidebar">
                <h3>Lagi Populer</h3>
                <ul>
                    <?php if (!empty($popular_news) && is_array($popular_news)): ?>
                        <?php foreach ($popular_news as $index => $item): ?>
                            <?php if (isset($item['judul']) && isset($item['id'])): ?>
                                <li>
                                    <?php echo $index + 1; ?>. 
                                    <a href="index.php?action=news_detail&id=<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($item['judul'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>Data berita tidak valid.</li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Tidak ada berita populer saat ini.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="alert error">Berita tidak ditemukan atau ID tidak valid. Silakan periksa kembali.</div>
        </div>
    <?php endif; ?>
</body>
</html>