<?php
// models/User.php
require_once 'config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        if (!$this->db) {
            throw new Exception("Gagal terhubung ke database.");
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT name, email, nickname, gender, country, phone_number, created_at, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            if (!$stmt->execute()) {
                throw new Exception("Gagal menjalankan query getUserByEmail.");
            }
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        } catch (Exception $e) {
            error_log("UserModel: getUserByEmail error for $email: " . $e->getMessage(), 3, 'public/errors.log');
            throw $e;
        }
    }

    public function getUserForLogin($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT email, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            if (!$stmt->execute()) {
                throw new Exception("Gagal menjalankan query getUserForLogin.");
            }
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        } catch (Exception $e) {
            error_log("UserModel: getUserForLogin error for $email: " . $e->getMessage(), 3, 'public/errors.log');
            throw $e;
        }
    }

    public function register($name, $email, $password)
    {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("UserModel: register error for $email: " . $e->getMessage(), 3, 'public/errors.log');
            throw $e;
        }
    }

    public function updateProfile($email, $nickname, $gender, $country, $phone_number)
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET nickname = ?, gender = ?, country = ?, phone_number = ? WHERE email = ?");
            $stmt->bind_param("sssss", $nickname, $gender, $country, $phone_number, $email);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("UserModel: updateProfile error for $email: " . $e->getMessage(), 3, 'public/errors.log');
            throw $e;
        }
    }

    public function getTotalUsers()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users");
            if (!$stmt->execute()) {
                throw new Exception("Gagal menjalankan query getTotalUsers.");
            }
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['total'];
        } catch (Exception $e) {
            error_log("UserModel: getTotalUsers error: " . $e->getMessage(), 3, 'public/errors.log');
            throw $e;
        }
    }
}
