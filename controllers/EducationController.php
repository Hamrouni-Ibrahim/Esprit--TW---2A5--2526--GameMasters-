
<?php
require_once "models/Education.php";
require_once "controllers/CategoryController.php";
require_once "config/database.php";

class EducationController {

    // List for front with search
    public function list() {
        $education = new Education();
        
        // Handle search
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
        if (!empty($searchQuery)) {
            $stmt = $education->search($searchQuery);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $education->getAll();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        include "views/front/educations_list.php";
    }

    // Detail page - Updated to show formation info
    public function detail($id) {
        try {
            require_once "models/Favorite.php";
            
            $pdo = (new Database())->getConnection();
            $query = $pdo->prepare("
                SELECT e.*, f.id as formation_id, f.title as formation_title 
                FROM educations e 
                LEFT JOIN formations f ON e.formation_id = f.id 
                WHERE e.id = ?
            ");
            $query->execute([$id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            // Check if education is favorited
            $isFavorite = false;
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Use the same logic as FavoriteController to get user ID
            $user_id = null;
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            } elseif (isset($_SESSION['temp_user_id']) && $_SESSION['temp_user_id'] <= 2147483647) {
                $user_id = $_SESSION['temp_user_id'];
            } else {
                // Generate temp ID if not exists or invalid (same logic as FavoriteController)
                $_SESSION['temp_user_id'] = crc32(session_id()) & 0x7FFFFFFF;
                $user_id = $_SESSION['temp_user_id'];
            }

            if ($user_id) {
                $favorite = new Favorite();
                // We don't need getOrCreateUser anymore as we're using direct ID
                $isFavorite = $favorite->isEducationFavorite($user_id, $id);
            }
            
            include "views/front/education_details.php";
        } catch (PDOException $e) {
            error_log("Error fetching education: " . $e->getMessage());
            $result = null;
            $isFavorite = false;
            include "views/front/education_details.php";
        }
    }

    // Admin list - Updated to show formation info
    public function adminList() {
        try {
            $pdo = (new Database())->getConnection();
            $query = $pdo->prepare("
                SELECT e.*, f.title as formation_title 
                FROM educations e 
                LEFT JOIN formations f ON e.formation_id = f.id 
                ORDER BY e.created_at DESC
            ");
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            include "views/admin/educations_list.php";
        } catch (PDOException $e) {
            error_log("Error fetching educations: " . $e->getMessage());
            $results = [];
            include "views/admin/educations_list.php";
        }
    }

    // Add form & insert - UPDATED to support formation_id (REQUIRED)
    public function add() {
        $formation_id = isset($_GET['formation_id']) ? (int)$_GET['formation_id'] : null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get formation_id from POST or GET
            $formation_id = isset($_POST['formation_id']) ? (int)$_POST['formation_id'] : (isset($_GET['formation_id']) ? (int)$_GET['formation_id'] : null);
            
            // REQUIRE formation_id - cannot create education without a formation
            if (empty($formation_id) || $formation_id <= 0) {
                $_SESSION['error_message'] = 'Erreur: Une éducation doit être associée à une formation. Veuillez sélectionner une formation.';
                // Reload the form with error - pass formations list to view
                require_once "models/Formation.php";
                $formationModel = new Formation();
                $allFormations = $formationModel->getAll();
                $formationsList = [];
                while ($row = $allFormations->fetch(PDO::FETCH_ASSOC)) {
                    $formationsList[] = $row;
                }
                $formation = null; // No specific formation selected
                include "views/admin/educations_add.php";
                exit;
            }
            
            // Verify formation exists
            require_once "models/Formation.php";
            $formationModel = new Formation();
            $formation = $formationModel->getById($formation_id);
            if (!$formation) {
                $_SESSION['error_message'] = 'Erreur: La formation sélectionnée n\'existe pas.';
                header("Location: ?controller=education&action=adminList");
                exit;
            }
            
            // Auto-create category and get the ID
            $categoryController = new CategoryController();
            $category_id = $categoryController->createCategoryIfNotExists($_POST['categorie']);
            
            // Create education with category_id and formation_id
            $result = $this->createEducationWithCategoryAndFormation(
                $_POST['title'],
                $_POST['description'],
                $_POST['competences'] ?? '',
                $_POST['difficulte'] ?? 'Débutant',
                $_POST['duree'] ?? 0,
                $_POST['prerequis'] ?? '',
                $_POST['categorie'] ?? '',
                $category_id,
                $_POST['lien_ressources'] ?? '',
                $_POST['impact_social'] ?? '',
                $formation_id
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Éducation créée avec succès.';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de la création de l\'éducation.';
            }
            
            // Redirect back to formation if created from formation, otherwise to education list
            if ($formation_id) {
                header("Location: ?controller=formation&action=adminList");
            } else {
                header("Location: ?controller=education&action=adminList");
            }
            exit;
        } else {
            // Pass formation_id to the view
            $formation = null;
            $formationsList = [];
            
            if ($formation_id) {
                require_once "models/Formation.php";
                $formationModel = new Formation();
                $formation = $formationModel->getById($formation_id);
                if (!$formation) {
                    $_SESSION['error_message'] = 'Formation introuvable.';
                    header("Location: ?controller=formation&action=adminList");
                    exit;
                }
            } else {
                // Load all formations for dropdown if no formation_id provided
                require_once "models/Formation.php";
                $formationModel = new Formation();
                $allFormations = $formationModel->getAll();
                while ($row = $allFormations->fetch(PDO::FETCH_ASSOC)) {
                    $formationsList[] = $row;
                }
            }
            include "views/admin/educations_add.php";
        }
    }

    // New method to handle education creation with category_id
    private function createEducationWithCategory($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social) {
        // If category_id is available, use direct SQL to include category_id
        if ($category_id) {
            return $this->createEducationWithCategoryId($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social);
        } else {
            // Fallback to original method if no category_id
            $education = new Education();
            return $education->create($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $lien_ressources, $impact_social);
        }
    }

    // Method to create education with category_id using direct SQL
    private function createEducationWithCategoryId($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social) {
        try {
            $pdo = (new Database())->getConnection();
            $query = "INSERT INTO educations (title, description, competences, difficulte, duree, prerequis, categorie, category_id, lien_ressources, impact_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social]);
        } catch (PDOException $e) {
            error_log("Error creating education with category: " . $e->getMessage());
            return false;
        }
    }
    
    // Method to create education with category_id and formation_id
    private function createEducationWithCategoryAndFormation($title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social, $formation_id) {
        try {
            $pdo = (new Database())->getConnection();
            if ($formation_id) {
                $query = "INSERT INTO educations (title, description, competences, difficulte, duree, prerequis, categorie, category_id, lien_ressources, impact_social, formation_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social, $formation_id]);
            } else {
                $query = "INSERT INTO educations (title, description, competences, difficulte, duree, prerequis, categorie, category_id, lien_ressources, impact_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social]);
            }
        } catch (PDOException $e) {
            error_log("Error creating education: " . $e->getMessage());
            return false;
        }
    }

    // Edit form & update - UPDATED
    public function edit($id) {
        $education = new Education();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Auto-create category and get the ID
            $categoryController = new CategoryController();
            $category_id = $categoryController->createCategoryIfNotExists($_POST['categorie']);
            
            // Update the education with category_id
            $this->updateEducationWithCategory(
                $id,
                $_POST['title'],
                $_POST['description'],
                $_POST['competences'] ?? '',
                $_POST['difficulte'] ?? 'Débutant',
                $_POST['duree'] ?? 0,
                $_POST['prerequis'] ?? '',
                $_POST['categorie'] ?? '',
                $category_id,
                $_POST['lien_ressources'] ?? '',
                $_POST['impact_social'] ?? ''
            );
            header("Location: ?controller=education&action=adminList");
        } else {
            $result = $education->getById($id);
            include "views/admin/educations_edit.php";
        }
    }

    // Method to update education with category_id
    private function updateEducationWithCategory($id, $title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social) {
        try {
            $pdo = (new Database())->getConnection();
            $query = "UPDATE educations SET title = ?, description = ?, competences = ?, difficulte = ?, duree = ?, prerequis = ?, categorie = ?, category_id = ?, lien_ressources = ?, impact_social = ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            return $stmt->execute([$title, $description, $competences, $difficulte, $duree, $prerequis, $categorie, $category_id, $lien_ressources, $impact_social, $id]);
        } catch (PDOException $e) {
            error_log("Error updating education with category: " . $e->getMessage());
            return false;
        }
    }

    // Delete
    public function delete($id) {
        $education = new Education();
        $education->delete($id);
        header("Location: ?controller=education&action=adminList");
    }
}
?>