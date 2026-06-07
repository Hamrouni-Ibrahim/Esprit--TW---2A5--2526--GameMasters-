<?php
class Database {
    private $host = "localhost";
    private $db_name = "game_masters";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            $error_message = "Erreur de connexion à la base de données: " . $exception->getMessage();
            error_log($error_message);
            throw new Exception($error_message);
        }
        
        return $this->conn;
    }
}
?>
