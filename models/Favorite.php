<?php
require_once "config/database.php";

class Favorite {
    private $conn;
    private $table_name = "favorites";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }


    /**
     * Add formation to favorites
     */
    public function addFormation($user_id, $formation_id) {
        // Check if already favorited
        if ($this->isFormationFavorite($user_id, $formation_id)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " (user_id, formation_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $formation_id]);
    }

    /**
     * Add education to favorites
     */
    public function addEducation($user_id, $education_id) {
        // Check if already favorited
        if ($this->isEducationFavorite($user_id, $education_id)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " (user_id, education_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $education_id]);
    }

    /**
     * Remove formation from favorites
     */
    public function removeFormation($user_id, $formation_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ? AND formation_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $formation_id]);
    }

    /**
     * Remove education from favorites
     */
    public function removeEducation($user_id, $education_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ? AND education_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $education_id]);
    }

    /**
     * Check if formation is favorited
     */
    public function isFormationFavorite($user_id, $formation_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? AND formation_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $formation_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Check if education is favorited
     */
    public function isEducationFavorite($user_id, $education_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? AND education_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $education_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Get all favorite formations for a user
     */
    public function getUserFavoriteFormations($user_id) {
        $query = "
            SELECT f.*, c.nom as category_name 
            FROM " . $this->table_name . " fav
            INNER JOIN formations f ON fav.formation_id = f.id
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE fav.user_id = ? AND fav.formation_id IS NOT NULL
            ORDER BY fav.created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all favorite educations for a user
     */
    public function getUserFavoriteEducations($user_id) {
        $query = "
            SELECT e.*, c.nom as category_name, f.title as formation_title, f.id as formation_id
            FROM " . $this->table_name . " fav
            INNER JOIN educations e ON fav.education_id = e.id
            LEFT JOIN categories c ON e.category_id = c.id
            LEFT JOIN formations f ON e.formation_id = f.id
            WHERE fav.user_id = ? AND fav.education_id IS NOT NULL
            ORDER BY fav.created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

