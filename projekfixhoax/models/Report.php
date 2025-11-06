<?php
// models/Report.php
require_once 'config/database.php';

class Report
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getTotalReports()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reports");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    public function getAllReports()
    {
        $stmt = $this->db->prepare("SELECT r.*, u.email AS user_email FROM reports r JOIN users u ON r.email_user = u.email ORDER BY r.created_at DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getReportsByUser($email_user)
    {
        $stmt = $this->db->prepare("SELECT r.*, u.email AS user_email FROM reports r JOIN users u ON r.email_user = u.email WHERE r.email_user = ? ORDER BY r.created_at DESC");
        $stmt->bind_param("s", $email_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getReportById($id)
    {
        $stmt = $this->db->prepare("SELECT r.*, u.email AS user_email FROM reports r JOIN users u ON r.email_user = u.email WHERE r.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addReport($email_user, $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan, $status)
    {
        $stmt = $this->db->prepare("INSERT INTO reports (email_user, judul, link_referensi, bukti_gambar_video, tanggapan_alasan, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $email_user, $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan, $status);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateReportStatus($id, $status, $news_id = null)
    {
        if ($news_id) {
            $stmt = $this->db->prepare("UPDATE reports SET status = ?, news_id = ? WHERE id = ?");
            $stmt->bind_param("sii", $status, $news_id, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE reports SET status = ?, news_id = NULL WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
        }
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateReport($id, $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan)
    {
        $stmt = $this->db->prepare("UPDATE reports SET judul = ?, link_referensi = ?, bukti_gambar_video = ?, tanggapan_alasan = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $judul, $link_referensi, $bukti_gambar_video, $tanggapan_alasan, $id);
        $result = $stmt->execute();
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        if (!$result) {
            error_log("Report::updateReport: Failed to update report ID $id: " . $this->db->error, 3, __DIR__ . '/../../public/errors.log');
        }
        
        return $result && $affected_rows > 0;
    }
}
?>