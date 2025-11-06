<?php
// models/News.php
require_once 'config/database.php';

class News
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    public function getTotalNews()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM news");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
    public function getAllNews()
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM news ORDER BY tanggal DESC");
        if (!$stmt) {
            error_log("getAllNews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return [];
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $news = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $news;
    }

    public function getNewsPaginated($page, $per_page, $search = '')
    {
        $offset = ($page - 1) * $per_page;
        $search = "%$search%";

        $count_stmt = $this->db->prepare("SELECT COUNT(*) FROM news WHERE judul LIKE ? OR isi LIKE ?");
        $count_stmt->bind_param("ss", $search, $search);
        $count_stmt->execute();
        $total_items = $count_stmt->get_result()->fetch_row()[0];

        $stmt = $this->db->prepare("SELECT n.* FROM news n WHERE n.judul LIKE ? OR n.isi LIKE ? ORDER BY n.tanggal DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ssii", $search, $search, $per_page, $offset);
        $stmt->execute();
        $news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'news' => $news,
            'total' => $total_items
        ];
    }

    public function addNews($judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $artikel_ids, $gambar)
    {
        $artikel_json = json_encode($artikel_ids);
        $stmt = mysqli_prepare($this->db, "INSERT INTO news (judul, isi, keterangan, klarifikasi, penulis, tanggal, tema, artikel, gambar, views) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        if (!$stmt) {
            error_log("addNews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return false;
        }
        mysqli_stmt_bind_param($stmt, "sssssssss", $judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $artikel_json, $gambar);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    public function updateNews($id, $judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $artikel_ids, $gambar)
    {
        $artikel_json = json_encode($artikel_ids);
        $stmt = mysqli_prepare($this->db, "UPDATE news SET judul = ?, isi = ?, keterangan = ?, klarifikasi = ?, penulis = ?, tanggal = ?, tema = ?, artikel = ?, gambar = ? WHERE id = ?");
        if (!$stmt) {
            error_log("updateNews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return false;
        }
        mysqli_stmt_bind_param($stmt, "sssssssssi", $judul, $isi, $keterangan, $klarifikasi, $penulis, $tanggal, $tema, $artikel_json, $gambar, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    public function getLatestNews($limit = 5)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM news ORDER BY tanggal DESC LIMIT ?");
        if (!$stmt) {
            error_log("getLatestNews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return [];
        }
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $news = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $news;
    }

    public function getPopularNews($limit = 5)
    {
        $stmt = mysqli_prepare($this->db, "SELECT id, judul, gambar FROM news ORDER BY views DESC LIMIT ?");
        if (!$stmt) {
            error_log("getPopularNews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return [];
        }
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $news = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        if (empty($news)) {
            error_log("getPopularNews: No popular news found for limit $limit", 3, "errors.log");
        }
        return $news;
    }
    public function getNewsById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare Error: " . $this->db->error);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $news = $result->fetch_assoc();
            $stmt->close();
            return $news ?: false;
        } catch (Exception $e) {
            error_log("getNewsById error for ID $id: " . $e->getMessage(), 3, __DIR__ . '/../public/errors.log');
            throw $e;
        }
    }

    public function deleteNews($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT gambar FROM news WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare Error: " . $this->db->error);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $news = $result->fetch_assoc();
            $stmt->close();

            $upload_dir = __DIR__ . '/../public/Uploads/';
            if ($news && $news['gambar'] && file_exists($upload_dir . $news['gambar'])) {
                unlink($upload_dir . $news['gambar']);
            }

            $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare Error: " . $this->db->error);
            }
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("deleteNews error for ID $id: " . $e->getMessage(), 3, __DIR__ . '/../public/errors.log');
            throw $e;
        }
    }

    public function incrementViews($id)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE news SET views = views + 1 WHERE id = ?");
        if (!$stmt) {
            error_log("incrementViews Prepare Error: " . mysqli_error($this->db), 3, "errors.log");
            return false;
        }
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if (!$success) {
            error_log("incrementViews Execute Error: " . mysqli_error($this->db), 3, "errors.log");
        }
        return $success;
    }
}
