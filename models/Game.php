<?php
class Game {
    private $conn;
    private $table_name = "games";
    
    // Getter pour table_name
    public function getTableName() {
        return $this->table_name;
    }

    public $id;
    public $user_id;
    public $name;
    public $description;
    public $impact_social;
    public $status;
    public $approval_status;
    public $image_url;
    public $demo_url;
    public $category_id;
    public $category_name;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un jeu
    public function create() {
        try {
            // Vérifier si les colonnes existent
            $checkQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'user_id'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasUserId = $checkStmt->rowCount() > 0;
            
            $checkQuery2 = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'approval_status'";
            $checkStmt2 = $this->conn->prepare($checkQuery2);
            $checkStmt2->execute();
            $hasApprovalStatus = $checkStmt2->rowCount() > 0;
            
            $checkQuery3 = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'category_id'";
            $checkStmt3 = $this->conn->prepare($checkQuery3);
            $checkStmt3->execute();
            $hasCategoryId = $checkStmt3->rowCount() > 0;
            
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            
            // Si user_id est null, c'est un admin, donc approuvé automatiquement
            $approvalStatus = ($this->user_id === null) ? 'approved' : 'pending';
            
            if($hasUserId && $hasApprovalStatus && $hasCategoryId) {
                // Version avec toutes les colonnes
                $query = "INSERT INTO " . $this->table_name . " 
                          SET user_id=:user_id, name=:name, description=:description, impact_social=:impact_social,
                              status=:status, approval_status=:approval_status, category_id=:category_id, image_url=:image_url, demo_url=:demo_url";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $this->user_id);
                $stmt->bindParam(":approval_status", $approvalStatus);
                $stmt->bindParam(":category_id", $this->category_id);
            } elseif($hasUserId && $hasCategoryId) {
                // Version avec user_id et category_id mais sans approval_status
                $query = "INSERT INTO " . $this->table_name . " 
                          SET user_id=:user_id, name=:name, description=:description, impact_social=:impact_social,
                              status=:status, category_id=:category_id, image_url=:image_url, demo_url=:demo_url";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $this->user_id);
                $stmt->bindParam(":category_id", $this->category_id);
            } elseif($hasUserId && $hasApprovalStatus) {
                // Version avec user_id et approval_status mais sans category_id
                $query = "INSERT INTO " . $this->table_name . " 
                          SET user_id=:user_id, name=:name, description=:description, impact_social=:impact_social,
                              status=:status, approval_status=:approval_status, image_url=:image_url, demo_url=:demo_url";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $this->user_id);
                $stmt->bindParam(":approval_status", $approvalStatus);
            } elseif($hasUserId) {
                // Version avec user_id mais sans approval_status ni category_id
                $query = "INSERT INTO " . $this->table_name . " 
                          SET user_id=:user_id, name=:name, description=:description, impact_social=:impact_social,
                              status=:status, image_url=:image_url, demo_url=:demo_url";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $this->user_id);
            } else {
                // Version sans les nouvelles colonnes (rétrocompatibilité)
                $query = "INSERT INTO " . $this->table_name . " 
                          SET name=:name, description=:description, impact_social=:impact_social,
                              status=:status, image_url=:image_url, demo_url=:demo_url";
                
                $stmt = $this->conn->prepare($query);
            }
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":impact_social", $this->impact_social);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":image_url", $this->image_url);
            $stmt->bindParam(":demo_url", $this->demo_url);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur Game::create: " . $e->getMessage());
            return false;
        }
    }

    // Lire tous les jeux
    public function readAll() {
        try {
            // Vérifier si la table game_categories existe
            $checkQuery = "SHOW TABLES LIKE 'game_categories'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasCategoriesTable = $checkStmt->rowCount() > 0;
            
            if ($hasCategoriesTable) {
                $query = "SELECT g.*, c.name as category_name 
                          FROM " . $this->table_name . " g
                          LEFT JOIN game_categories c ON g.category_id = c.id
                          ORDER BY g.created_at DESC";
            } else {
                $query = "SELECT g.*, NULL as category_name 
                          FROM " . $this->table_name . " g
                          ORDER BY g.created_at DESC";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur Game::readAll: " . $e->getMessage());
            // Retourner un statement vide en cas d'erreur
            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }

    // Lire un jeu par ID
    public function readOne() {
        $query = "SELECT g.*, c.name as category_name 
                  FROM " . $this->table_name . " g
                  LEFT JOIN game_categories c ON g.category_id = c.id
                  WHERE g.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->user_id = $row['user_id'] ?? null;
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->impact_social = $row['impact_social'];
            $this->status = $row['status'];
            $this->approval_status = $row['approval_status'] ?? 'approved';
            $this->image_url = $row['image_url'];
            $this->demo_url = $row['demo_url'];
            $this->category_id = $row['category_id'] ?? null;
            $this->category_name = $row['category_name'] ?? null;
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Mettre à jour un jeu
    public function update() {
        try {
            // Vérifier si les colonnes existent
            $checkQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            
            // Vérifier si category_id existe
            $checkCategoryQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'category_id'";
            $checkCategoryStmt = $this->conn->prepare($checkCategoryQuery);
            $checkCategoryStmt->execute();
            $hasCategoryId = $checkCategoryStmt->rowCount() > 0;
            
            if($hasApprovalStatus) {
                if($hasCategoryId) {
                    $query = "UPDATE " . $this->table_name . " 
                              SET name=:name, description=:description, impact_social=:impact_social,
                                  status=:status, approval_status=:approval_status, category_id=:category_id,
                                  image_url=:image_url, demo_url=:demo_url
                              WHERE id=:id";
                } else {
                    $query = "UPDATE " . $this->table_name . " 
                              SET name=:name, description=:description, impact_social=:impact_social,
                                  status=:status, approval_status=:approval_status, image_url=:image_url, demo_url=:demo_url
                              WHERE id=:id";
                }
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":name", $this->name);
                $stmt->bindParam(":description", $this->description);
                $stmt->bindParam(":impact_social", $this->impact_social);
                $stmt->bindParam(":status", $this->status);
                $stmt->bindParam(":approval_status", $this->approval_status);
                if($hasCategoryId) {
                    $stmt->bindParam(":category_id", $this->category_id);
                }
                $stmt->bindParam(":image_url", $this->image_url);
                $stmt->bindParam(":demo_url", $this->demo_url);
                $stmt->bindParam(":id", $this->id);
            } else {
                if($hasCategoryId) {
                    $query = "UPDATE " . $this->table_name . " 
                              SET name=:name, description=:description, impact_social=:impact_social,
                                  status=:status, category_id=:category_id, image_url=:image_url, demo_url=:demo_url
                              WHERE id=:id";
                } else {
                    $query = "UPDATE " . $this->table_name . " 
                              SET name=:name, description=:description, impact_social=:impact_social,
                                  status=:status, image_url=:image_url, demo_url=:demo_url
                              WHERE id=:id";
                }
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":name", $this->name);
                $stmt->bindParam(":description", $this->description);
                $stmt->bindParam(":impact_social", $this->impact_social);
                $stmt->bindParam(":status", $this->status);
                if($hasCategoryId) {
                    $stmt->bindParam(":category_id", $this->category_id);
                }
                $stmt->bindParam(":image_url", $this->image_url);
                $stmt->bindParam(":demo_url", $this->demo_url);
                $stmt->bindParam(":id", $this->id);
            }
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur Game::update: " . $e->getMessage());
            return false;
        }
    }
    
    // Approuver ou rejeter un jeu
    public function updateApprovalStatus($gameId, $status) {
        // Get user_id before updating
        $userQuery = "SELECT user_id, status FROM " . $this->table_name . " WHERE id = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->execute([$gameId]);
        $gameData = $userStmt->fetch(PDO::FETCH_ASSOC);
        $userId = $gameData['user_id'] ?? null;
        $currentStatus = $gameData['status'] ?? null;
        
        $query = "UPDATE " . $this->table_name . " SET approval_status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$status, $gameId]);
        
        // Auto-assign medal if game is approved and published
        if ($result && $status === 'approved' && $currentStatus === 'published' && $userId) {
            try {
                require_once "models/Medal.php";
                $medal = new Medal();
                $medal->recalculateMedal($userId);
                error_log("🏆 Medal recalculated for user ID: " . $userId . " after game approval");
            } catch (Exception $e) {
                error_log("🏆 Error recalculating medal: " . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    // Obtenir les jeux en attente d'approbation
    public function getPendingGames() {
        try {
            // Vérifier si la colonne approval_status existe
            $checkQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            if(!$hasApprovalStatus) {
                // Si la colonne n'existe pas, retourner un tableau vide
                return [];
            }
            
            $query = "SELECT g.*, u.username, u.email, c.name as category_name
                      FROM " . $this->table_name . " g 
                      LEFT JOIN users u ON g.user_id = u.id 
                      LEFT JOIN game_categories c ON g.category_id = c.id
                      WHERE g.approval_status = 'pending' 
                      ORDER BY g.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur getPendingGames: " . $e->getMessage());
            return [];
        }
    }
    
    // Get published games by user ID (for medal system)
    public function getPublishedGamesByUserId($user_id) {
        try {
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            
            // Check if approval_status column exists
            $checkQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'approval_status'";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            if ($hasApprovalStatus) {
                $query = "SELECT * FROM " . $this->table_name . " 
                          WHERE user_id = ? AND status = 'published' AND approval_status = 'approved'";
            } else {
                $query = "SELECT * FROM " . $this->table_name . " 
                          WHERE user_id = ? AND status = 'published'";
            }
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur getPublishedGamesByUserId: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtenir les jeux d'un utilisateur
    public function getGamesByUser($userId) {
        try {
            // Vérifier si la colonne user_id existe
            $checkQuery = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'user_id'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasUserId = $checkStmt->rowCount() > 0;
            
            if($hasUserId) {
                $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $userId);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Si la colonne n'existe pas, retourner un tableau vide
                return [];
            }
        } catch(PDOException $e) {
            error_log("Erreur getGamesByUser: " . $e->getMessage());
            return [];
        }
    }

    // Supprimer un jeu
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Compter les jeux par statut
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Rechercher des jeux
    public function search($searchTerm = null, $categoryId = null, $rating = null) {
        $query = "SELECT g.*, c.name as category_name,
                         COALESCE(AVG(gr.rating), 0) as average_rating,
                         COUNT(gr.id) as rating_count
                  FROM " . $this->table_name . " g
                  LEFT JOIN game_categories c ON g.category_id = c.id
                  LEFT JOIN game_ratings gr ON g.id = gr.game_id
                  WHERE 1=1";
        
        $params = [];

        // Filtre par mot-clé (nom ou description)
        if (!empty($searchTerm)) {
            $query .= " AND (g.name LIKE :search OR g.description LIKE :search)";
            $params[':search'] = "%{$searchTerm}%";
        }

        // Filtre par catégorie
        if (!empty($categoryId)) {
            $query .= " AND g.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        // Group by pour les agrégations
        $query .= " GROUP BY g.id, c.name";

        // Filtre par note moyenne
        if (!empty($rating) && is_numeric($rating)) {
            $ratingValue = (float)$rating;
            // Rechercher les jeux avec une note moyenne proche de la note sélectionnée (plage de ±0.5)
            // Exclure les jeux sans notes (average_rating = 0) sauf si on cherche spécifiquement 0
            $query .= " HAVING average_rating >= :rating_min AND average_rating < :rating_max AND average_rating > 0";
            $params[':rating_min'] = $ratingValue - 0.5;
            $params[':rating_max'] = $ratingValue + 0.5;
        }

        // Ajout du tri
        $query .= " ORDER BY g.created_at DESC";

        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                // Determine parameter type
                if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } elseif (is_float($value)) {
                    $type = PDO::PARAM_STR;
                    $value = (string)$value;
                } else {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur Recherche Jeu: " . $e->getMessage());
            return false;
        }
    }
}
?>