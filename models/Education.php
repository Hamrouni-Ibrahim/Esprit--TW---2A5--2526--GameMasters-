
<?php
require_once "config/database.php";

class Education {
    private $conn;
    private $table_name = "educations";

    public $id;
    public $title;
    public $description;
    public $competences;
    public $difficulte;
    public $duree;
    public $prerequis;
    public $categorie;
    public $lien_ressources;
    public $impact_social;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create education with formation_id
    public function create($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social, $formation_id = null) {
        if ($formation_id) {
            $query = "INSERT INTO " . $this->table_name . " (title, description, competences, difficulte, duree, prerequis, categorie, lien_ressources, impact_social, formation_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social, $formation_id]);
        } else {
            $query = "INSERT INTO " . $this->table_name . " (title, description, competences, difficulte, duree, prerequis, categorie, lien_ressources, impact_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social]);
        }
    }
    
    // Get educations by formation_id (with duplicate prevention)
    public function getByFormationId($formation_id) {
        // Use DISTINCT to prevent duplicates, or group by id to get unique educations
        $query = "SELECT DISTINCT e.* FROM " . $this->table_name . " e WHERE e.formation_id = ? ORDER BY e.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$formation_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Additional check: remove duplicates by id (in case DISTINCT doesn't work due to different timestamps)
        $uniqueEducations = [];
        $seenIds = [];
        foreach ($results as $edu) {
            if (!in_array($edu['id'], $seenIds)) {
                $uniqueEducations[] = $edu;
                $seenIds[] = $edu['id'];
            }
        }
        
        return $uniqueEducations;
    }

    // Get educations by formation_id organized as a tree
    public function getTreeByFormationId($formation_id) {
        $educations = $this->getByFormationId($formation_id);
        
        // Build tree
        $tree = [];
        $educationsById = [];
        
        // Index by ID
        foreach ($educations as &$education) {
            $education['children'] = [];
            $educationsById[$education['id']] = &$education;
        }
        
        // Assign to parents
        foreach ($educations as &$education) {
            if (!empty($education['parent_id']) && isset($educationsById[$education['parent_id']])) {
                $educationsById[$education['parent_id']]['children'][] = &$education;
            } else {
                $tree[] = &$education;
            }
        }
        
        return $tree;
    }

    // KEEP ORIGINAL - no category_id logic here  
    public function update($id, $title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social) {
        $query = "UPDATE " . $this->table_name . " SET title = ?, description = ?, competences = ?, difficulte = ?, duree = ?, prerequis = ?, categorie = ?, lien_ressources = ?, impact_social = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Update parent_id for hierarchy
    public function updateParent($id, $parent_id) {
        $query = "UPDATE " . $this->table_name . " SET parent_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        // If parent_id is 0 or empty, set to NULL
        if (empty($parent_id)) {
            $parent_id = null;
        }
        return $stmt->execute([$parent_id, $id]);
    }

    // Search educations
    public function search($searchQuery) {
        $searchTerm = '%' . $searchQuery . '%';
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE title LIKE ? OR description LIKE ? OR competences LIKE ? OR categorie LIKE ? OR prerequis LIKE ?
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt;
    }
}
?>
