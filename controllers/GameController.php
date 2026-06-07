<?php
class GameController {
    private $gameModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->gameModel = new Game($db);
    }

    // Lister tous les jeux
    public function index() {
        try {
            $stmt = $this->gameModel->readAll();
            if ($stmt) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch(PDOException $e) {
            error_log("Erreur GameController::index: " . $e->getMessage());
            return [];
        }
    }

    // Afficher un jeu
    public function show($id) {
        $this->gameModel->id = $id;
        if($this->gameModel->readOne()) {
            return [
                'id' => $this->gameModel->id,
                'name' => $this->gameModel->name,
                'description' => $this->gameModel->description,
                'impact_social' => $this->gameModel->impact_social,
                'status' => $this->gameModel->status,
                'image_url' => $this->gameModel->image_url,
                'demo_url' => $this->gameModel->demo_url,
                'category_id' => $this->gameModel->category_id,
                'category_name' => $this->gameModel->category_name,
                'created_at' => $this->gameModel->created_at
            ];
        }
        return null;
    }

    // Créer un jeu AVEC UPLOAD
    public function create($data, $files = [], $userId = null) {
        // Plus besoin de vérifier la permission - tous les utilisateurs peuvent soumettre
        // L'admin approuvera ou refusera les soumissions
        
        $errors = $this->validateGame($data, $files);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Gestion de l'upload d'image
        $imageUrl = $this->handleImageUpload($files['image'] ?? null, $data['name']);
        if ($imageUrl === false) {
            return ['success' => false, 'errors' => ['Erreur lors de l\'upload de l\'image']];
        }

        // Gestion de l'upload vidéo
        $videoUrl = $this->handleVideoUpload($files['video'] ?? null, $data['name']);
        if ($videoUrl === false) {
            return ['success' => false, 'errors' => ['Erreur lors de l\'upload de la vidéo']];
        }

        $this->gameModel->user_id = $userId; // null pour admin, user_id pour utilisateur
        $this->gameModel->name = $data['name'];
        $this->gameModel->description = $data['description'];
        $this->gameModel->impact_social = $data['impact_social'];
        $this->gameModel->status = $data['status'] ?? 'development'; // Par défaut en développement pour les utilisateurs
        $this->gameModel->category_id = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $this->gameModel->image_url = $imageUrl ?: $data['image_url'];
        $this->gameModel->demo_url = $videoUrl ?: $data['demo_url'];

        // Set approval_status: approved for admins (userId === null), pending for regular users
        if (isset($data['approval_status'])) {
            $this->gameModel->approval_status = $data['approval_status'];
        } else {
            // Auto-set based on user type
            $this->gameModel->approval_status = ($userId === null) ? 'approved' : 'pending';
        }

        if($this->gameModel->create()) {
            // Auto-assign medal if game is created as published and approved (admin case)
            if ($this->gameModel->status === 'published' && $this->gameModel->approval_status === 'approved' && $userId) {
                try {
                    require_once "models/Medal.php";
                    $medal = new Medal();
                    $medal->recalculateMedal($userId);
                    error_log("🏆 Medal recalculated for user ID: " . $userId . " after game creation");
                } catch (Exception $e) {
                    error_log("🏆 Error recalculating medal: " . $e->getMessage());
                }
            }
            
            $message = ($userId === null) 
                ? 'Jeu créé avec succès!' 
                : 'Jeu soumis avec succès! Il sera examiné par un administrateur avant publication.';
            return ['success' => true, 'message' => $message];
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de la création du jeu']];
    }

    // Mettre à jour un jeu AVEC UPLOAD
    public function update($id, $data, $files = []) {
        $errors = $this->validateGame($data, $files, true);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Gestion de l'upload d'image
        $imageUrl = $this->handleImageUpload($files['image'] ?? null, $data['name']);
        if ($imageUrl === false) {
            return ['success' => false, 'errors' => ['Erreur lors de l\'upload de l\'image']];
        }

        // Gestion de l'upload vidéo
        $videoUrl = $this->handleVideoUpload($files['video'] ?? null, $data['name']);
        if ($videoUrl === false) {
            return ['success' => false, 'errors' => ['Erreur lors de l\'upload de la vidéo']];
        }

        // Récupérer le jeu existant pour préserver l'approval_status
        $this->gameModel->id = $id;
        if (!$this->gameModel->readOne()) {
            return ['success' => false, 'errors' => ['Jeu non trouvé']];
        }
        
        // Récupérer les données du jeu existant
        $existingGame = [
            'approval_status' => $this->gameModel->approval_status ?? 'approved',
            'status' => $this->gameModel->status
        ];

        $this->gameModel->id = $id;
        $this->gameModel->name = $data['name'];
        $this->gameModel->description = $data['description'];
        $this->gameModel->impact_social = $data['impact_social'];
        $this->gameModel->status = $data['status'];
        $this->gameModel->category_id = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $this->gameModel->image_url = $imageUrl ?: $data['image_url'];
        $this->gameModel->demo_url = $videoUrl ?: $data['demo_url'];
        
        // Préserver l'approval_status existant
        // Si l'admin change le statut en "published", s'assurer que approval_status est "approved"
        if (isset($existingGame['approval_status'])) {
            if ($data['status'] === 'published' && $existingGame['approval_status'] !== 'approved') {
                // Si on publie, approuver automatiquement
                $this->gameModel->approval_status = 'approved';
            } else {
                // Sinon, préserver le statut existant
                $this->gameModel->approval_status = $existingGame['approval_status'];
            }
        }

        if($this->gameModel->update()) {
            // Auto-assign medal if game is published and approved
            if ($this->gameModel->status === 'published' && $this->gameModel->approval_status === 'approved' && $this->gameModel->user_id) {
                try {
                    require_once "models/Medal.php";
                    $medal = new Medal();
                    $medal->recalculateMedal($this->gameModel->user_id);
                    error_log("🏆 Medal recalculated for user ID: " . $this->gameModel->user_id . " after game update");
                } catch (Exception $e) {
                    error_log("🏆 Error recalculating medal: " . $e->getMessage());
                }
            }
            
            return ['success' => true, 'message' => 'Jeu mis à jour avec succès!'];
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de la mise à jour du jeu']];
    }

    // Supprimer un jeu
    public function delete($id) {
        $this->gameModel->id = $id;
        if($this->gameModel->delete()) {
            return ['success' => true, 'message' => 'Jeu supprimé avec succès!'];
        }
        return ['success' => false, 'errors' => ['Erreur lors de la suppression du jeu']];
    }

    // Gestion de l'upload d'image
    private function handleImageUpload($imageFile, $gameName) {
        if (!$imageFile || $imageFile['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imageFile['type'], $allowedTypes)) {
            return false;
        }

        if ($imageFile['size'] > 5 * 1024 * 1024) {
            return false;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/game-masters/public/assets/img/games/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $gameName);
        $filename = 'game_' . $safeName . '_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($imageFile['tmp_name'], $filePath)) {
            return '/game-masters/public/assets/img/games/' . $filename;
        }

        return false;
    }

    // Gestion de l'upload vidéo
    private function handleVideoUpload($videoFile, $gameName) {
        if (!$videoFile || $videoFile['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
        if (!in_array($videoFile['type'], $allowedTypes)) {
            return false;
        }

        if ($videoFile['size'] > 50 * 1024 * 1024) {
            return false;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/game-masters/public/assets/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($videoFile['name'], PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $gameName);
        $filename = 'game_' . $safeName . '_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($videoFile['tmp_name'], $filePath)) {
            return '/game-masters/public/assets/videos/' . $filename;
        }

        return false;
    }

    // Validation du jeu
    private function validateGame($data, $files = [], $isUpdate = false) {
        $errors = [];
        
        // Normalize data - trim all string values
        $data = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
        
        // Check name
        if(!isset($data['name']) || empty($data['name'])) {
            $errors[] = "Le nom du jeu est requis";
        } else {
            $name = $data['name'];
            if(strlen($name) < 2) {
                $errors[] = "Le nom du jeu doit contenir au moins 2 caractères";
            } elseif(strlen($name) > 100) {
                $errors[] = "Le nom du jeu ne peut pas dépasser 100 caractères";
            } elseif(preg_match('/^[0-9]/', $name)) {
                $errors[] = "Le nom du jeu ne peut pas commencer par un chiffre";
            } elseif(preg_match('/[<>"\']/', $name)) {
                $errors[] = "Le nom du jeu contient des caractères interdits";
            }
        }
        
        // Check description
        if(!isset($data['description']) || empty($data['description'])) {
            $errors[] = "La description est requise";
        } else {
            $description = $data['description'];
            if(strlen($description) < 10) {
                $errors[] = "La description doit contenir au moins 10 caractères";
            } elseif(strlen($description) > 2000) {
                $errors[] = "La description ne peut pas dépasser 2000 caractères";
            }
        }
        
        // Check impact_social
        if(!isset($data['impact_social']) || empty($data['impact_social'])) {
            $errors[] = "L'impact social est requis";
        } else {
            $impact = $data['impact_social'];
            if(strlen($impact) < 5) {
                $errors[] = "L'impact social doit contenir au moins 5 caractères";
            } elseif(strlen($impact) > 500) {
                $errors[] = "L'impact social ne peut pas dépasser 500 caractères";
            }
        }
        
        // Check status - default to 'development' if not set
        if(!isset($data['status']) || empty($data['status'])) {
            $data['status'] = 'development';
        }
        
        if(!in_array($data['status'], ['published', 'development', 'archived'])) {
            $errors[] = "Statut invalide. Choisissez entre: published, development, archived";
        }
        
        $hasImageUpload = isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK;
        $hasImageUrl = !empty($data['image_url']);
        
        if (!$hasImageUpload && !$hasImageUrl && !$isUpdate) {
            $errors[] = "Une image est requise (upload ou URL)";
        }
        
        if ($hasImageUpload) {
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($files['image']['type'], $allowedImageTypes)) {
                $errors[] = "Type d'image non supporté. Utilisez JPG, PNG, GIF ou WebP";
            }
            if ($files['image']['size'] > 5 * 1024 * 1024) {
                $errors[] = "L'image est trop volumineuse (max 5MB)";
            }
        }
        
        if ($hasImageUrl && !$hasImageUpload) {
            if(!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
                $errors[] = "URL d'image invalide";
            } elseif(!preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $data['image_url'])) {
                $errors[] = "L'URL de l'image doit pointer vers un fichier image valide (JPG, PNG, GIF, WebP)";
            }
        }
        
        $hasVideoUpload = isset($files['video']) && $files['video']['error'] === UPLOAD_ERR_OK;
        $hasVideoUrl = !empty($data['demo_url']);
        
        if ($hasVideoUpload) {
            $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
            if (!in_array($files['video']['type'], $allowedVideoTypes)) {
                $errors[] = "Type de vidéo non supporté. Utilisez MP4, WebM ou OGG";
            }
            if ($files['video']['size'] > 50 * 1024 * 1024) {
                $errors[] = "La vidéo est trop volumineuse (max 50MB)";
            }
        }
        
        if ($hasVideoUrl && !$hasVideoUpload && !empty($data['demo_url'])) {
            if(!filter_var($data['demo_url'], FILTER_VALIDATE_URL)) {
                $errors[] = "URL de démonstration invalide";
            }
        }
        
        // CSRF token validation - only if token is provided
        if(isset($data['csrf_token']) && !empty($data['csrf_token'])) {
            if($data['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $errors[] = "Token de sécurité invalide";
            }
        } else {
            // If no CSRF token is provided, generate one for next time but don't fail validation
            // (This allows forms without CSRF to work, but it's less secure)
            if(empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
        
        return $errors;
    }

    public function getPublishedGames() {
        try {
            // Vérifier si la colonne approval_status existe
            $checkQuery = "SHOW COLUMNS FROM games LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            if($hasApprovalStatus) {
                $query = "SELECT g.*, gc.name as category_name 
                          FROM games g 
                          LEFT JOIN game_categories gc ON g.category_id = gc.id 
                          WHERE g.status = 'published' AND g.approval_status = 'approved' 
                          ORDER BY g.created_at DESC";
            } else {
                // Fallback si la colonne n'existe pas encore
                $query = "SELECT g.*, gc.name as category_name 
                          FROM games g 
                          LEFT JOIN game_categories gc ON g.category_id = gc.id 
                          WHERE g.status = 'published' 
                          ORDER BY g.created_at DESC";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ajouter les notes
            require_once __DIR__ . '/../models/GameRating.php';
            $ratingModel = new GameRating($this->conn);
            $userId = $_SESSION['user_id'] ?? null;

            foreach ($games as &$game) {
                $stats = $ratingModel->getAverageRating($game['id']);
                $game['rating_average'] = $stats['average'];
                $game['rating_count'] = $stats['count'];
                
                if ($userId) {
                    $game['user_rating'] = $ratingModel->getUserRating($userId, $game['id']);
                } else {
                    $game['user_rating'] = 0;
                }
            }

            return $games;
        } catch(PDOException $e) {
            error_log("Erreur getPublishedGames: " . $e->getMessage());
            return [];
        }
    }
// Dans la classe GameController, ajoutez cette méthode :

public function getGamesByCategory($categoryId) {
    try {
        $query = "SELECT g.*, gc.name as category_name 
                  FROM games g 
                  LEFT JOIN game_categories gc ON g.category_id = gc.idCategory 
                  WHERE g.category_id = :category_id 
                  AND g.status = 'published'
                  ORDER BY g.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getGamesByCategory: " . $e->getMessage());
        return [];
    }
}
    public function getDevelopmentGames() {
        $allGames = $this->index();
        $developmentGames = [];
        
        foreach ($allGames as $game) {
            if ($game['status'] === 'development') {
                $developmentGames[] = $game;
            }
        }
        
        return $developmentGames;
    }

    // Noter un jeu
    public function rate() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour noter un jeu']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['game_id']) || !isset($input['rating'])) {
            echo json_encode(['success' => false, 'error' => 'Données manquantes']);
            exit;
        }

        $gameId = (int)$input['game_id'];
        $rating = (int)$input['rating'];
        $userId = $_SESSION['user_id'];

        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'error' => 'La note doit être comprise entre 1 et 5']);
            exit;
        }

        require_once __DIR__ . '/../models/GameRating.php';
        $ratingModel = new GameRating($this->conn);

        if ($ratingModel->addOrUpdateRating($userId, $gameId, $rating)) {
            $newStats = $ratingModel->getAverageRating($gameId);
            echo json_encode([
                'success' => true, 
                'message' => 'Note enregistrée avec succès',
                'new_average' => $newStats['average'],
                'new_count' => $newStats['count']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement de la note']);
        }
        exit;
    }
    // Rechercher des jeux (with published and approved filter)
    public function searchGames($searchTerm = null, $categoryId = null, $rating = null) {
        try {
            // Check if approval_status column exists
            $checkQuery = "SHOW COLUMNS FROM games LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            // Build query with search and published/approved filters
            $query = "SELECT g.*, gc.name as category_name,
                             COALESCE(AVG(gr.rating), 0) as average_rating,
                             COUNT(gr.id) as rating_count
                      FROM games g 
                      LEFT JOIN game_categories gc ON g.category_id = gc.id 
                      LEFT JOIN game_ratings gr ON g.id = gr.game_id
                      WHERE g.status = 'published'";
            
            if ($hasApprovalStatus) {
                $query .= " AND g.approval_status = 'approved'";
            }
            
            $params = [];
            
            // Add search filter
            if (!empty($searchTerm)) {
                $query .= " AND (g.name LIKE :search OR g.description LIKE :search OR g.impact_social LIKE :search)";
                $params[':search'] = "%{$searchTerm}%";
            }
            
            // Filtre par catégorie
            if (!empty($categoryId)) {
                $query .= " AND g.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }
            
            // Group by pour les agrégations
            $query .= " GROUP BY g.id, gc.name";
            
            // Filtre par note moyenne
            if (!empty($rating) && is_numeric($rating)) {
                $ratingValue = (float)$rating;
                $query .= " HAVING average_rating >= :rating_min AND average_rating < :rating_max AND average_rating > 0";
                $params[':rating_min'] = $ratingValue - 0.5;
                $params[':rating_max'] = $ratingValue + 0.5;
            }
            
            $query .= " ORDER BY g.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add ratings like getPublishedGames does
            require_once __DIR__ . '/../models/GameRating.php';
            $ratingModel = new GameRating($this->conn);
            $userId = $_SESSION['user_id'] ?? null;
            
            foreach ($games as &$game) {
                $stats = $ratingModel->getAverageRating($game['id']);
                $game['rating_average'] = round($stats['average'] ?? ($game['average_rating'] ?? 0), 1);
                $game['rating_count'] = (int)($stats['count'] ?? ($game['rating_count'] ?? 0));
                
                if ($userId) {
                    $game['user_rating'] = $ratingModel->getUserRating($userId, $game['id']);
                } else {
                    $game['user_rating'] = 0;
            }
            }
            
            return $games;
        } catch(PDOException $e) {
            error_log("Erreur searchGames: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtenir les jeux d'un utilisateur
    public function getUserGames($userId) {
        try {
            $tableName = $this->gameModel->getTableName();
            $query = "SELECT g.*, c.name as category_name 
                      FROM " . $tableName . " g
                      LEFT JOIN game_categories c ON g.category_id = c.id
                      WHERE g.user_id = ?
                      ORDER BY g.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur getUserGames: " . $e->getMessage());
            return [];
        }
    }
    
    // Compter les jeux publiés et acceptés d'un utilisateur
    public function countPublishedGames($userId) {
        try {
            $tableName = $this->gameModel->getTableName();
            
            // Vérifier si la colonne approval_status existe
            $checkQuery = "SHOW COLUMNS FROM " . $tableName . " LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            if ($hasApprovalStatus) {
                $query = "SELECT COUNT(*) as count FROM " . $tableName . " 
                         WHERE user_id = ? AND status = 'published' AND approval_status = 'approved'";
            } else {
                $query = "SELECT COUNT(*) as count FROM " . $tableName . " 
                         WHERE user_id = ? AND status = 'published'";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch(PDOException $e) {
            error_log("Erreur countPublishedGames: " . $e->getMessage());
            return 0;
        }
    }
    
    // Calculer et mettre à jour la médaille d'un utilisateur
    public function updateUserMedal($userId) {
        try {
            // Vérifier si la colonne medal existe
            $checkQuery = "SHOW COLUMNS FROM users LIKE 'medal'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasMedalColumn = $checkStmt->rowCount() > 0;
            
            if (!$hasMedalColumn) {
                // Créer la colonne si elle n'existe pas
                try {
                    $alterQuery = "ALTER TABLE users ADD COLUMN medal ENUM('none', 'bronze', 'silver', 'gold') DEFAULT 'none'";
                    $this->conn->exec($alterQuery);
                } catch(PDOException $e) {
                    error_log("Erreur création colonne medal: " . $e->getMessage());
                    return false;
                }
            }
            
            // Compter les jeux publiés
            $publishedCount = $this->countPublishedGames($userId);
            
            // Déterminer la médaille
            $medal = 'none';
            if ($publishedCount >= 16) {
                $medal = 'gold';
            } elseif ($publishedCount >= 11) {
                $medal = 'silver';
            } elseif ($publishedCount >= 1) {
                $medal = 'bronze';
            }
            
            // Mettre à jour la médaille
            $updateQuery = "UPDATE users SET medal = ? WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([$medal, $userId]);
            
            return true;
        } catch(PDOException $e) {
            error_log("Erreur updateUserMedal: " . $e->getMessage());
            return false;
        }
    }
    
    // Approuver un jeu (admin)
    public function approveGame($gameId) {
        try {
            $tableName = $this->gameModel->getTableName();
            
            // Récupérer l'ID de l'utilisateur avant la mise à jour
            $getUserQuery = "SELECT user_id FROM " . $tableName . " WHERE id = ?";
            $getUserStmt = $this->conn->prepare($getUserQuery);
            $getUserStmt->execute([$gameId]);
            $game = $getUserStmt->fetch(PDO::FETCH_ASSOC);
            $userId = $game['user_id'] ?? null;
            
            $query = "UPDATE " . $tableName . " 
                      SET approval_status = 'approved', status = 'published'
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$gameId]);
            
            if ($stmt->rowCount() > 0) {
                // Mettre à jour la médaille de l'utilisateur
                if ($userId) {
                    $this->updateUserMedal($userId);
                }
                return ['success' => true, 'message' => 'Jeu approuvé avec succès'];
            }
            return ['success' => false, 'message' => 'Jeu non trouvé'];
        } catch(PDOException $e) {
            error_log("Erreur approveGame: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'approbation'];
        }
    }
    
    // Rejeter un jeu (admin)
    public function rejectGame($gameId) {
        try {
            $tableName = $this->gameModel->getTableName();
            $query = "UPDATE " . $tableName . " 
                      SET approval_status = 'rejected'
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$gameId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Jeu rejeté avec succès'];
            }
            return ['success' => false, 'message' => 'Jeu non trouvé'];
        } catch(PDOException $e) {
            error_log("Erreur rejectGame: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du rejet'];
        }
    }
    
    // Obtenir les statistiques des jeux
    public function getStats() {
        try {
            $stats = [];
            $tableName = $this->gameModel->getTableName();
            
            // Vérifier si la colonne approval_status existe
            $checkQuery = "SHOW COLUMNS FROM " . $tableName . " LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            // Total des jeux
            $query = "SELECT COUNT(*) as total FROM " . $tableName;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Jeux publiés
            if ($hasApprovalStatus) {
                $query = "SELECT COUNT(*) as published FROM " . $tableName . " WHERE status = 'published' AND approval_status = 'approved'";
            } else {
                $query = "SELECT COUNT(*) as published FROM " . $tableName . " WHERE status = 'published'";
            }
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['published'] = $stmt->fetch(PDO::FETCH_ASSOC)['published'];
            
            // Jeux en attente
            if ($hasApprovalStatus) {
                $query = "SELECT COUNT(*) as pending FROM " . $tableName . " WHERE approval_status = 'pending'";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
            } else {
                $stats['pending'] = 0;
            }
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return ['total' => 0, 'published' => 0, 'pending' => 0];
        }
    }
    
    // Obtenir les jeux en attente d'approbation
    public function getPendingGames() {
        try {
            $tableName = $this->gameModel->getTableName();
            
            // Vérifier si la colonne approval_status existe
            $checkQuery = "SHOW COLUMNS FROM " . $tableName . " LIKE 'approval_status'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasApprovalStatus = $checkStmt->rowCount() > 0;
            
            if (!$hasApprovalStatus) {
                // Si la colonne n'existe pas, retourner un tableau vide
                return [];
            }
            
            $query = "SELECT g.*, c.name as category_name, u.username as author_name
                      FROM " . $tableName . " g
                      LEFT JOIN game_categories c ON g.category_id = c.id
                      LEFT JOIN users u ON g.user_id = u.id
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
}
?>