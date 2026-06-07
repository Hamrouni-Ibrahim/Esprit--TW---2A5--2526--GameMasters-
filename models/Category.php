
<?php
require_once "config/database.php";

class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $nom;
    public $type;
    public $description;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByType($type) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE type = ? ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nom) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        if($stmt->execute([$data['nom']])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nom = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['nom'], $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}