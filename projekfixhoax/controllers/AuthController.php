<?php
// controllers/AuthController.php
require_once 'models/User.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        try {
            $this->userModel = new User();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal mengakses database. Silakan coba lagi nanti.';
            error_log("AuthController: Database initialization error: " . $e->getMessage(), 3, 'public/errors.log');
            header("Location: index.php?action=auth/login");
            exit();
        }
    }

    public function login()
    {
        if (isset($_SESSION['session_email'])) {
            $_SESSION['success'] = 'Anda sudah login.';
            header("Location: index.php?action=home");
            exit();
        }

        $email = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = trim($_POST['password'] ?? '');

                if (empty($email) || empty($password)) {
                    $error = 'Email dan kata sandi harus diisi.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Format email tidak valid.';
                } else {
                    $user = $this->userModel->getUserForLogin($email);
                    if ($user && password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['session_email'] = $email;
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['last_activity'] = time();
                        $_SESSION['success'] = 'Login berhasil. Selamat datang!';
                        header("Location: index.php?action=home");
                        exit();
                    } else {
                        $error = 'Email atau kata sandi salah.';
                        error_log("AuthController: Login failed for $email", 3, 'public/errors.log');
                    }
                }
            } catch (Exception $e) {
                $error = 'Gagal memproses login. Silakan coba lagi.';
                error_log("AuthController: Login error: " . $e->getMessage(), 3, 'public/errors.log');
            }
        }

        require_once 'views/auth/login.php';
    }

    public function register()
    {
        if (isset($_SESSION['session_email'])) {
            $_SESSION['success'] = 'Anda sudah login.';
            header("Location: index.php?action=home");
            exit();
        }

        $name = '';
        $email = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = trim($_POST['password'] ?? '');
                $confirm_password = trim($_POST['confirm_password'] ?? '');

                if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                    $error = 'Semua kolom harus diisi.';
                } elseif (strlen($password) < 6) {
                    $error = 'Kata sandi harus minimal 6 karakter.';
                } elseif ($password !== $confirm_password) {
                    $error = 'Kata sandi dan konfirmasi tidak cocok.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Format email tidak valid.';
                } elseif ($this->userModel->getUserByEmail($email)) {
                    $error = 'Email sudah terdaftar. Gunakan email lain.';
                } else {
                    if ($this->userModel->register($name, $email, $password)) {
                        session_regenerate_id(true);
                        $_SESSION['session_email'] = $email;
                        $_SESSION['user_role'] = 'user';
                        $_SESSION['last_activity'] = time();
                        $_SESSION['success'] = 'Registrasi berhasil. Selamat datang, ' . htmlspecialchars($name) . '!';
                        header("Location: index.php?action=home");
                        exit();
                    } else {
                        $error = 'Gagal mendaftar. Silakan coba lagi.';
                        error_log("AuthController: Registration failed for $email", 3, 'public/errors.log');
                    }
                }
            } catch (Exception $e) {
                $error = 'Gagal memproses registrasi. Silakan coba lagi.';
                error_log("AuthController: Registration error: " . $e->getMessage(), 3, 'public/errors.log');
            }
        }

        require_once 'views/auth/register.php';
    }

    public function logout()
    {
        try {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['success'] = 'Anda telah berhasil logout.';
            header("Location: index.php?action=auth/login");
            exit();
        } catch (Exception $e) {
            session_start();
            $_SESSION['error'] = 'Gagal logout. Silakan coba lagi.';
            error_log("AuthController: Logout error: " . $e->getMessage(), 3, 'public/errors.log');
            header("Location: index.php?action=auth/login");
            exit();
        }
    }
}
