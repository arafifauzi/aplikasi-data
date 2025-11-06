<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "fixhoax";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->dbname);
            if (!$this->conn) {
                throw new Exception("Koneksi gagal: " . mysqli_connect_error());
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
        return $this->conn;
    }
}
?>