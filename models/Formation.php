<?php
require_once "config/database.php";

class Formation {
    private $conn;
    private $table_name = "formations";

    public $id;
    public $title;
    public $description;
    public $competences;
    public $difficulte;
    public $duree;
    public $categorie;
    public $lien_ressources;
    public $impact_social;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all formations
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get formation by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create
    public function create($title, $description, $competences, $difficulte, $duree, $categorie, $lien_ressources, $impact_social) {
    // Auto-create category and get its ID
    $categoryController = new CategoryController();
    $category_id = $categoryController->createCategoryIfNotExists($categorie);
    
    $query = "INSERT INTO " . $this->table_name . " 
              (title, description, competences, difficulte, duree, categorie, category_id, lien_ressources, impact_social) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $categorie, $category_id, $lien_ressources, $impact_social]);
}

    // Update
    public function update($id, $title, $description, $competences, $difficulte, $duree, $categorie, $lien_ressources, $impact_social) {
        $query = "UPDATE " . $this->table_name . " SET title = ?, description = ?, competences = ?, difficulte = ?, duree = ?, categorie = ?, lien_ressources = ?, impact_social = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $categorie, $lien_ressources, $impact_social, $id]);
    }

    // Delete
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Search formations
    public function search($searchQuery) {
        $searchTerm = '%' . $searchQuery . '%';
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE title LIKE ? OR description LIKE ? OR competences LIKE ? OR categorie LIKE ?
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt;
    }
}
?>
