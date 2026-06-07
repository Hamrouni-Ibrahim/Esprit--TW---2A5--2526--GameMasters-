<?php
require_once "config/database.php";

class GameLibrary {
    private $conn;
    private $table_name = "games_library";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all active games
    public function getAll($category = null, $difficulty = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1";
        $params = [];
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $query .= " AND difficulty = ?";
            $params[] = $difficulty;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get game by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get games by category
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category = ? AND is_active = 1 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get distinct categories
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Search games
    public function search($searchQuery) {
        $searchTerm = '%' . $searchQuery . '%';
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE (title LIKE ? OR description LIKE ?) AND is_active = 1
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>





