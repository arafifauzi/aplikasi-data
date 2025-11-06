<?php
// controllers/ProfileController.php
require_once 'models/User.php';

class ProfileController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $email = $_SESSION['session_email'];
        $success = "";
        $error = "";

        $user = $this->userModel->getUserByEmail($email);
        if (!$user) {
            $error = "Pengguna tidak ditemukan.";
        }

        if (isset($_POST['save'])) {
            $nickname = trim($_POST['nickname']);
            $gender = trim($_POST['gender']);
            $country = trim($_POST['country']);
            $phone_number = trim($_POST['phone_number']);

            if (empty($nickname) || empty($gender) || empty($country)) {
                $error = "Semua kolom harus diisi.";
            } elseif (strlen($phone_number) > 0 && !preg_match("/^[0-9+ ]+$/", $phone_number)) {
                $error = "Nomor telepon hanya boleh berisi angka, tanda +, dan spasi.";
            } else {
                if ($this->userModel->updateProfile($email, $nickname, $gender, $country, $phone_number)) {
                    $success = "Profil berhasil diperbarui.";
                    $user = $this->userModel->getUserByEmail($email);
                } else {
                    $error = "Gagal memperbarui profil.";
                }
            }
        }

        require_once 'views/profile.php';
    }
}
?>