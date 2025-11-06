<?php
require_once 'config/database.php';
?>


<?php
// models/Article.php
class Article
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllArticles()
    {
        $stmt = $this->db->prepare("SELECT id, judul_artikel, link_artikel FROM artikel");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt = $this->db->prepare("SELECT id, judul_artikel, link_artikel FROM artikel");
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->db->error, 3, 'public/errors.log');
            return [];
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error, 3, 'public/errors.log');
            return [];
        }
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        if (empty($data)) {
            error_log("No articles found in database", 3, 'public/errors.log');
        }
        return $data;
    }

    public function getArticleById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM artikel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $article = $result->fetch_assoc();
        $stmt->close();
        return $article;
    }

    public function updateArticle($id, $judul, $link)
    {
        $stmt = $this->db->prepare("UPDATE artikel SET judul_artikel = ?, link_artikel = ? WHERE id = ?");
        $stmt->bind_param("ssi", $judul, $link, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function deleteArticle($id)
    {
        $stmt = $this->db->prepare("DELETE FROM artikel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function addArticle($judul, $link)
    {
        $stmt = $this->db->prepare("INSERT INTO artikel (judul_artikel, link_artikel) VALUES (?, ?)");
        $stmt->bind_param("ss", $judul, $link);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getTotalArticles()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM artikel");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
}
