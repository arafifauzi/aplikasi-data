<?php
// models/Comment.php
require_once 'config/database.php';

class Comment
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addComment($news_id, $user_email, $comment_content, $comment_date)
    {
        $stmt = $this->db->prepare("INSERT INTO comments (news_id, user_email, comment_content, comment_date, likes, dislikes, is_edited) VALUES (?, ?, ?, ?, 0, 0, 0)");
        $stmt->bind_param("isss", $news_id, $user_email, $comment_content, $comment_date);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Comment::addComment: Failed to add comment for news_id: $news_id, user: $user_email, error: " . $this->db->error, 3, __DIR__ . '/../public/errors.log');
        }
        $stmt->close();
        return $result;
    }

    public function getCommentsByNewsId($news_id)
    {
        $stmt = $this->db->prepare("SELECT id, news_id, user_email, comment_content, comment_date, is_edited, edited_content FROM comments WHERE news_id = ? ORDER BY comment_date DESC");
        $stmt->bind_param("i", $news_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $comments;
    }

    public function getCommentById($comment_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comment = $result->fetch_assoc();
        $stmt->close();
        return $comment;
    }

    public function updateComment($comment_id, $user_email, $new_content)
    {
        $stmt = $this->db->prepare("UPDATE comments SET comment_content = ?, edited_content = ?, is_edited = 1 WHERE id = ? AND (user_email = ? OR EXISTS (SELECT 1 FROM users WHERE email = ? AND role = 'admin'))");
        $stmt->bind_param("ssiss", $new_content, $new_content, $comment_id, $user_email, $user_email);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Comment::updateComment: Failed to update comment_id: $comment_id, user: $user_email, error: " . $this->db->error, 3, __DIR__ . '/../public/errors.log');
        }
        $stmt->close();
        return $result;
    }

    public function deleteComment($comment_id)
    {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Comment::deleteComment: Failed to delete comment_id: $comment_id, error: " . $this->db->error, 3, __DIR__ . '/../public/errors.log');
        }
        $stmt->close();
        return $result;
    }

    public function addReaction($comment_id, $user_email, $reaction_type, $reaction_date)
    {
        $stmt = $this->db->prepare("INSERT INTO comment_reactions (comment_id, user_email, reaction_type, reaction_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $comment_id, $user_email, $reaction_type, $reaction_date);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Comment::addReaction: Failed to add reaction for comment_id: $comment_id, user: $user_email, type: $reaction_type, error: " . $this->db->error, 3, __DIR__ . '/../public/errors.log');
        }
        $stmt->close();
        return $result;
    }

    public function updateReaction($comment_id, $user_email, $reaction_type, $reaction_date)
    {
        $stmt = $this->db->prepare("UPDATE comment_reactions SET reaction_type = ?, reaction_date = ? WHERE comment_id = ? AND user_email = ?");
        $stmt->bind_param("ssis", $reaction_type, $reaction_date, $comment_id, $user_email);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Comment::updateReaction: Failed to update reaction for comment_id: $comment_id, user: $user_email, type: $reaction_type, error: " . $this->db->error, 3, __DIR__ . '/../public/errors.log');
        }
        $stmt->close();
        return $result;
    }

    public function getReaction($comment_id, $user_email)
    {
        $stmt = $this->db->prepare("SELECT reaction_type FROM comment_reactions WHERE comment_id = ? AND user_email = ?");
        $stmt->bind_param("is", $comment_id, $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $reaction = $result->fetch_assoc();
        $stmt->close();
        return $reaction ? $reaction['reaction_type'] : null;
    }

    public function countReactions($comment_id, $reaction_type)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM comment_reactions WHERE comment_id = ? AND reaction_type = ?");
        $stmt->bind_param("is", $comment_id, $reaction_type);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();
        return $count;
    }
}
