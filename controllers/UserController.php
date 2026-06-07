<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $conn;
    private $userModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    // Méthode pour récupérer tous les utilisateurs avec leurs profils
    public function getUsersWithProfiles() {
        // Vérifier quelles colonnes existent dans la table users
        $checkCanAddGames = "SHOW COLUMNS FROM users LIKE 'can_add_games'";
        $checkStmt1 = $this->conn->prepare($checkCanAddGames);
        $checkStmt1->execute();
        $hasCanAddGames = $checkStmt1->rowCount() > 0;
        
        $checkLastLogin = "SHOW COLUMNS FROM users LIKE 'last_login'";
        $checkStmt2 = $this->conn->prepare($checkLastLogin);
        $checkStmt2->execute();
        $hasLastLogin = $checkStmt2->rowCount() > 0;
        
        // Vérifier si la colonne medal existe
        $checkMedal = "SHOW COLUMNS FROM users LIKE 'medal'";
        $checkStmt3 = $this->conn->prepare($checkMedal);
        $checkStmt3->execute();
        $hasMedal = $checkStmt3->rowCount() > 0;
        
        // Vérifier si la colonne banned_until existe
        $checkBannedUntil = "SHOW COLUMNS FROM users LIKE 'banned_until'";
        $checkStmt4 = $this->conn->prepare($checkBannedUntil);
        $checkStmt4->execute();
        $hasBannedUntil = $checkStmt4->rowCount() > 0;
        
        // Construire la requête selon les colonnes disponibles
        $selectFields = "SELECT DISTINCT u.id, u.username, u.email, u.role, u.status, u.avatar, u.created_at";
        
        if ($hasCanAddGames) {
            $selectFields .= ", u.can_add_games";
        }
        
        if ($hasLastLogin) {
            $selectFields .= ", u.last_login";
        }
        
        if ($hasMedal) {
            $selectFields .= ", u.medal";
        }
        
        if ($hasBannedUntil) {
            $selectFields .= ", u.banned_until";
        }
        
        $selectFields .= ", p.first_name, p.last_name, p.discord, p.country, p.nationality,
                             p.gender, p.birth_date, p.career_level, p.expertise, p.tech_stack, p.timezone
                      FROM users u 
                      LEFT JOIN profiles p ON u.id = p.user_id 
                      ORDER BY u.created_at DESC";
        
        $query = $selectFields;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $users = [];
        $seenIds = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userId = $row['id'];
            // Éviter les doublons basés sur l'ID utilisateur
            if (!in_array($userId, $seenIds)) {
                $seenIds[] = $userId;
                $users[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'status' => $row['status'],
                    'avatar' => $row['avatar'] ?? '',
                    'can_add_games' => $hasCanAddGames ? ($row['can_add_games'] ?? 0) : 1,
                    'created_at' => $row['created_at'],
                    'last_login' => $hasLastLogin ? ($row['last_login'] ?? null) : null,
                    'medal' => $hasMedal ? ($row['medal'] ?? 'none') : 'none',
                    'banned_until' => $hasBannedUntil ? ($row['banned_until'] ?? null) : null,
                    'profile' => [
                        'first_name' => $row['first_name'] ?? '',
                        'last_name' => $row['last_name'] ?? '',
                        'discord' => $row['discord'] ?? '',
                        'country' => $row['country'] ?? '',
                        'nationality' => $row['nationality'] ?? '',
                        'gender' => $row['gender'] ?? '',
                        'birth_date' => $row['birth_date'] ?? '',
                        'career_level' => $row['career_level'] ?? '',
                        'expertise' => $row['expertise'] ?? '',
                        'tech_stack' => $row['tech_stack'] ?? '',
                        'timezone' => $row['timezone'] ?? ''
                    ]
                ];
            }
        }
        
        return $users;
    }

    // Mettre à jour la médaille d'un utilisateur (admin)
    public function updateMedal($userId, $medal) {
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
                    return ['success' => false, 'message' => 'Erreur lors de la création de la colonne medal'];
                }
            }
            
            // Valider la médaille
            $validMedals = ['none', 'bronze', 'silver', 'gold'];
            if (!in_array($medal, $validMedals)) {
                return ['success' => false, 'message' => 'Médaille invalide'];
            }
            
            // Mettre à jour la médaille
            $updateQuery = "UPDATE users SET medal = ? WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $result = $updateStmt->execute([$medal, $userId]);
            
            if ($result && $updateStmt->rowCount() > 0) {
                error_log("🏆 Medal updated via UserController: " . $medal . " for user ID: " . $userId);
                error_log("🏆 Medal updated in database - user will see it on next page load/refresh");
                return ['success' => true, 'message' => 'Médaille mise à jour avec succès'];
            }
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        } catch(PDOException $e) {
            error_log("Erreur updateMedal: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la médaille'];
        }
    }
    
    // Récupérer tous les utilisateurs
    public function index() {
        return $this->userModel->readAll();
    }

    // Récupérer les statistiques des utilisateurs
    public function getStats() {
        $stats = [];
        
        // Total des utilisateurs
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Utilisateurs actifs
        $query = "SELECT COUNT(*) as active FROM users WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['active_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
        
        // Nouveaux utilisateurs (7 derniers jours)
        $query = "SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['new_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];
        
        // Taux de complétion des profils
        $query = "SELECT COUNT(*) as completed FROM profiles WHERE first_name != '' AND last_name != ''";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        $stats['completion_rate'] = $stats['total_users'] > 0 ? round(($completed / $stats['total_users']) * 100) : 0;
        
        return $stats;
    }

    // Méthode pour mettre à jour un utilisateur
    public function update($user_id, $data) {
        $errors = $this->validateUserUpdateData($data);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Vérifier si la colonne existe
            $checkQuery = "SHOW COLUMNS FROM users LIKE 'can_add_games'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasColumn = $checkStmt->rowCount() > 0;
            
            // Vérifier si la colonne medal existe
            $checkMedalQuery = "SHOW COLUMNS FROM users LIKE 'medal'";
            $checkMedalStmt = $this->conn->prepare($checkMedalQuery);
            $checkMedalStmt->execute();
            $hasMedalColumn = $checkMedalStmt->rowCount() > 0;
            
            // Sanitization des données
            $username = htmlspecialchars(strip_tags(trim($data['username'])));
            $email = htmlspecialchars(strip_tags(trim($data['email'])));
            $role = htmlspecialchars(strip_tags(trim($data['role'])));
            $status = htmlspecialchars(strip_tags(trim($data['status'])));
            $canAddGames = isset($data['can_add_games']) ? (int)$data['can_add_games'] : 0;
            $medal = isset($data['medal']) ? htmlspecialchars(strip_tags(trim($data['medal']))) : 'none';
            
            // Valider la médaille
            $validMedals = ['none', 'bronze', 'silver', 'gold'];
            if (!in_array($medal, $validMedals)) {
                $medal = 'none';
            }
            
            // Construire la requête dynamiquement
            $updateFields = ["username = :username", "email = :email", "role = :role", "status = :status"];
            $params = [
                ":username" => $username,
                ":email" => $email,
                ":role" => $role,
                ":status" => $status,
                ":id" => $user_id
            ];
            
            if($hasColumn) {
                $updateFields[] = "can_add_games = :can_add_games";
                $params[":can_add_games"] = $canAddGames;
            }
            
            if($hasMedalColumn) {
                $updateFields[] = "medal = :medal";
                $params[":medal"] = $medal;
                
                error_log("🏆 UserController::update - Medal value: " . var_export($medal, true) . " for user ID: " . $user_id);
            }
            
            $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $executeResult = $stmt->execute();
            $rowsAffected = $stmt->rowCount();
            
            error_log("🏆 UserController::update - Execute result: " . ($executeResult ? 'SUCCESS' : 'FAILED'));
            error_log("🏆 UserController::update - Rows affected: " . $rowsAffected);
            
            if($executeResult) {
                // Verify the medal was actually saved
                if($hasMedalColumn) {
                    $verifyQuery = "SELECT medal FROM users WHERE id = ?";
                    $verifyStmt = $this->conn->prepare($verifyQuery);
                    $verifyStmt->execute([$user_id]);
                    $verifyResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                    $verifiedMedal = $verifyResult ? ($verifyResult['medal'] ?? 'none') : 'none';
                    
                    error_log("🏆 UserController::update - Verified medal in DB: " . var_export($verifiedMedal, true));
                    
                    if($verifiedMedal !== $medal) {
                        error_log("🏆 UserController::update - WARNING: Medal mismatch! Expected: " . $medal . ", Got: " . $verifiedMedal);
                    }
                }
                
                return ['success' => true, 'message' => 'Utilisateur mis à jour avec succès'];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        } catch(PDOException $e) {
            error_log("Erreur UserController::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }
    
    // Mettre à jour la permission d'ajout de jeux
    public function updateGamePermission($userId, $canAdd) {
        return $this->userModel->updateGamePermission($userId, $canAdd);
    }

    // Validation pour la mise à jour d'utilisateur (RENFORCÉE)
    private function validateUserUpdateData($data) {
        $errors = [];
        
        // Validation ID utilisateur
        if(empty($data['id']) || !is_numeric($data['id'])) {
            $errors[] = "ID utilisateur invalide";
        }
        
        // Validation username (RENFORCÉE)
        if(empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        } else {
            $username = trim($data['username']);
            if(strlen($username) < 3) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
            } elseif(strlen($username) > 20) {
                $errors[] = "Le nom d'utilisateur ne peut pas dépasser 20 caractères";
            } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
            } elseif(preg_match('/^[0-9]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut pas être composé uniquement de chiffres";
            } elseif(!preg_match('/[a-zA-Z]/', $username)) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins une lettre";
            } else {
                // Vérifier si le username existe déjà pour un autre utilisateur
                $checkQuery = "SELECT id FROM users WHERE username = ? AND id != ?";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([$username, $data['id']]);
                if($checkStmt->rowCount() > 0) {
                    $errors[] = "Ce nom d'utilisateur est déjà utilisé par un autre utilisateur";
                }
            }
        }
        
        // Validation email (RENFORCÉE)
        if(empty($data['email'])) {
            $errors[] = "L'email est requis";
        } else {
            $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif(strlen($email) > 100) {
                $errors[] = "L'email ne peut pas dépasser 100 caractères";
            } elseif($this->isDisposableEmail($email)) {
                $errors[] = "Les emails jetables ne sont pas autorisés";
            } else {
                // Vérifier si l'email existe déjà pour un autre utilisateur
                $checkQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([$email, $data['id']]);
                if($checkStmt->rowCount() > 0) {
                    $errors[] = "Cet email est déjà utilisé par un autre utilisateur";
                }
            }
        }
        
        // Validation rôle
        if(empty($data['role'])) {
            $errors[] = "Le rôle est requis";
        } elseif(!in_array($data['role'], ['player', 'moderator', 'admin'])) {
            $errors[] = "Rôle invalide. Choisissez entre: player, moderator, admin";
        }
        
        // Validation statut
        if(empty($data['status'])) {
            $errors[] = "Le statut est requis";
        } elseif(!in_array($data['status'], ['active', 'inactive', 'pending', 'banned'])) {
            $errors[] = "Statut invalide. Choisissez entre: active, inactive, pending, banned";
        }
        
        // Validation CSRF
        if(empty($data['csrf_token']) || $data['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $errors[] = "Token de sécurité invalide";
        }
        
        return $errors;
    }

    // Vérifier les emails jetables
    private function isDisposableEmail($email) {
        $disposableDomains = [
            'tempmail.com', 'guerrillamail.com', 'mailinator.com', '10minutemail.com',
            'yopmail.com', 'throwawaymail.com', 'fakeinbox.com', 'trashmail.com'
        ];
        
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        return in_array($domain, $disposableDomains);
    }

    // Méthode pour récupérer un utilisateur par ID
    public function getUserById($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Bannir un utilisateur
    public function ban($user_id, $duration = null) {
        // Vérifier que l'utilisateur existe
        $user = $this->getUserById($user_id);
        if(!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        // Empêcher l'auto-bannissement de l'admin
        if($user_id == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas vous bannir vous-même'];
        }

        try {
            // Vérifier si la colonne banned_until existe
            $checkBannedUntil = "SHOW COLUMNS FROM users LIKE 'banned_until'";
            $checkStmt = $this->conn->prepare($checkBannedUntil);
            $checkStmt->execute();
            $hasBannedUntil = $checkStmt->rowCount() > 0;

            // Si la colonne n'existe pas, la créer
            if (!$hasBannedUntil) {
                $alterQuery = "ALTER TABLE users ADD COLUMN banned_until DATETIME NULL DEFAULT NULL";
                $this->conn->exec($alterQuery);
            }

            // Calculer la date d'expiration
            $bannedUntil = null;
            if ($duration !== null && $duration !== 'permanent' && $duration !== '') {
                // $duration est en jours
                if (is_numeric($duration)) {
                    $bannedUntil = date('Y-m-d H:i:s', strtotime('+' . (int)$duration . ' days'));
                } elseif (is_string($duration) && strtotime($duration) !== false) {
                    // $duration est une date personnalisée
                    $bannedUntil = date('Y-m-d H:i:s', strtotime($duration));
                }
            }
            // Si $duration est 'permanent' ou null, $bannedUntil reste null (bannissement permanent)

            $query = "UPDATE users SET status = 'banned', banned_until = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            if($stmt->execute([$bannedUntil, $user_id])) {
                $message = $bannedUntil ? 
                    'Utilisateur banni jusqu\'au ' . date('d/m/Y à H:i', strtotime($bannedUntil)) : 
                    'Utilisateur banni de manière permanente';
                return ['success' => true, 'message' => $message];
            }
            return ['success' => false, 'message' => 'Erreur lors du bannissement'];
        } catch(PDOException $e) {
            error_log("Erreur UserController::ban: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du bannissement: ' . $e->getMessage()];
        }
    }

    // Débannir un utilisateur
    public function unban($user_id) {
        // Vérifier que l'utilisateur existe
        $user = $this->getUserById($user_id);
        if(!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        try {
            // Vérifier si la colonne banned_until existe
            $checkBannedUntil = "SHOW COLUMNS FROM users LIKE 'banned_until'";
            $checkStmt = $this->conn->prepare($checkBannedUntil);
            $checkStmt->execute();
            $hasBannedUntil = $checkStmt->rowCount() > 0;

            if ($hasBannedUntil) {
                $query = "UPDATE users SET status = 'active', banned_until = NULL WHERE id = ?";
            } else {
                $query = "UPDATE users SET status = 'active' WHERE id = ?";
            }
            
            $stmt = $this->conn->prepare($query);
            
            if($stmt->execute([$user_id])) {
                return ['success' => true, 'message' => 'Utilisateur débanni avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors du débannissement'];
        } catch(PDOException $e) {
            error_log("Erreur UserController::unban: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du débannissement: ' . $e->getMessage()];
        }
    }

    // Supprimer un utilisateur
    public function delete($user_id) {
        // Vérifier que l'utilisateur existe
        $user = $this->getUserById($user_id);
        if(!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        // Empêcher l'auto-suppression de l'admin
        if($user_id == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte'];
        }

        // Vérifier si c'est le dernier admin
        if($user['role'] === 'admin') {
            $adminCountQuery = "SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin' AND status = 'active'";
            $adminCountStmt = $this->conn->prepare($adminCountQuery);
            $adminCountStmt->execute();
            $adminCount = $adminCountStmt->fetch(PDO::FETCH_ASSOC)['admin_count'];
            
            if($adminCount <= 1) {
                return ['success' => false, 'message' => 'Impossible de supprimer le dernier administrateur actif'];
            }
        }

        // Commencer par supprimer le profil
        $query = "DELETE FROM profiles WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        
        // Puis supprimer l'utilisateur
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute([$user_id])) {
            return ['success' => true, 'message' => 'Utilisateur supprimé avec succès'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    // Créer un utilisateur
    public function create($data) {
        $errors = $this->validateUserCreationData($data);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->userModel->username = $data['username'];
        $this->userModel->email = $data['email'];
        $this->userModel->password = $data['password'];
        $this->userModel->role = $data['role'] ?? 'player';
        $this->userModel->status = $data['status'] ?? 'active';

        if($this->userModel->create()) {
            return ['success' => true, 'message' => 'Utilisateur créé avec succès!'];
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de la création de l\'utilisateur']];
    }

    // Validation pour la création d'utilisateur (RENFORCÉE)
    private function validateUserCreationData($data) {
        $errors = [];
        
        // Validation username (RENFORCÉE)
        if(empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        } else {
            $username = trim($data['username']);
            if(strlen($username) < 3) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
            } elseif(strlen($username) > 20) {
                $errors[] = "Le nom d'utilisateur ne peut pas dépasser 20 caractères";
            } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
            } elseif(preg_match('/^[0-9]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut pas être composé uniquement de chiffres";
            } elseif(!preg_match('/[a-zA-Z]/', $username)) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins une lettre";
            } else {
                // Vérifier si le username existe déjà
                $checkQuery = "SELECT id FROM users WHERE username = ?";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([$username]);
                if($checkStmt->rowCount() > 0) {
                    $errors[] = "Ce nom d'utilisateur est déjà utilisé";
                }
            }
        }
        
        // Validation email (RENFORCÉE)
        if(empty($data['email'])) {
            $errors[] = "L'email est requis";
        } else {
            $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif(strlen($email) > 100) {
                $errors[] = "L'email ne peut pas dépasser 100 caractères";
            } elseif($this->isDisposableEmail($email)) {
                $errors[] = "Les emails jetables ne sont pas autorisés";
            } else {
                // Vérifier si l'email existe déjà
                $this->userModel->email = $email;
                if($this->userModel->emailExists()) {
                    $errors[] = "Cet email est déjà utilisé";
                }
            }
        }
        
        // Validation password (RENFORCÉE)
        if(empty($data['password'])) {
            $errors[] = "Le mot de passe est requis";
        } else {
            $password = $data['password'];
            if(strlen($password) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
            } elseif(strlen($password) > 128) {
                $errors[] = "Le mot de passe est trop long";
            } elseif(!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins une majuscule";
            } elseif(!preg_match('/[a-z]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins une minuscule";
            } elseif(!preg_match('/[0-9]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins un chiffre";
            } elseif(!preg_match('/[^A-Za-z0-9]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
            }
        }
        
        // Validation rôle
        if(empty($data['role'])) {
            $errors[] = "Le rôle est requis";
        } elseif(!in_array($data['role'], ['player', 'moderator', 'admin'])) {
            $errors[] = "Rôle invalide. Choisissez entre: player, moderator, admin";
        }
        
        // Validation statut
        if(empty($data['status'])) {
            $errors[] = "Le statut est requis";
        } elseif(!in_array($data['status'], ['active', 'inactive', 'pending', 'banned'])) {
            $errors[] = "Statut invalide. Choisissez entre: active, inactive, pending, banned";
        }
        
        // Validation CSRF
        if(empty($data['csrf_token']) || $data['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $errors[] = "Token de sécurité invalide";
        }
        
        return $errors;
    }

    // Méthode de secours si getUsersWithProfiles ne fonctionne pas
    public function getUsersWithProfilesFallback() {
        try {
            return $this->getUsersWithProfiles();
        } catch (Exception $e) {
            // En cas d'erreur, retourner les utilisateurs de base
            $users = [];
            $stmt = $this->userModel->readAll();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'status' => $row['status'],
                    'avatar' => $row['avatar'] ?? '',
                    'created_at' => $row['created_at'],
                    'last_login' => $row['last_login'] ?? null,
                    'profile' => [
                        'first_name' => '',
                        'last_name' => '',
                        'discord' => '',
                        'country' => '',
                        'nationality' => '',
                        'gender' => '',
                        'birth_date' => '',
                        'career_level' => '',
                        'expertise' => '',
                        'tech_stack' => '',
                        'timezone' => ''
                    ]
                ];
            }
            
            return $users;
        }
    }

    // Recherche d'utilisateurs
    public function searchUsers($searchTerm) {
        $query = "SELECT u.*, 
                         p.first_name, p.last_name, p.discord
                  FROM users u 
                  LEFT JOIN profiles p ON u.id = p.user_id 
                  WHERE u.username LIKE ? OR u.email LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?
                  ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'role' => $row['role'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'last_login' => $row['last_login'] ?? null,
                'profile' => [
                    'first_name' => $row['first_name'] ?? '',
                    'last_name' => $row['last_name'] ?? '',
                    'discord' => $row['discord'] ?? ''
                ]
            ];
        }
        
        return $users;
    }

    // Récupérer les utilisateurs par statut
    public function getUsersByStatus($status) {
        $query = "SELECT u.*, 
                         p.first_name, p.last_name
                  FROM users u 
                  LEFT JOIN profiles p ON u.id = p.user_id 
                  WHERE u.status = ?
                  ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'role' => $row['role'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'last_login' => $row['last_login'] ?? null,
                'profile' => [
                    'first_name' => $row['first_name'] ?? '',
                    'last_name' => $row['last_name'] ?? ''
                ]
            ];
        }
        
        return $users;
    }

    // Mettre à jour le dernier login
    public function updateLastLogin($user_id) {
        $query = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id]);
    }

    // Changer le mot de passe
    public function changePassword($user_id, $currentPassword, $newPassword) {
        // Récupérer l'utilisateur
        $user = $this->getUserById($user_id);
        if(!$user) {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé']];
        }

        // Vérifier l'ancien mot de passe
        if(!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'errors' => ['Mot de passe actuel incorrect']];
        }

        // Valider le nouveau mot de passe
        if(strlen($newPassword) < 8) {
            return ['success' => false, 'errors' => ['Le nouveau mot de passe doit contenir au moins 8 caractères']];
        } elseif(!preg_match('/[A-Z]/', $newPassword)) {
            return ['success' => false, 'errors' => ['Le nouveau mot de passe doit contenir au moins une majuscule']];
        } elseif(!preg_match('/[a-z]/', $newPassword)) {
            return ['success' => false, 'errors' => ['Le nouveau mot de passe doit contenir au moins une minuscule']];
        } elseif(!preg_match('/[0-9]/', $newPassword)) {
            return ['success' => false, 'errors' => ['Le nouveau mot de passe doit contenir au moins un chiffre']];
        } elseif(!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            return ['success' => false, 'errors' => ['Le nouveau mot de passe doit contenir au moins un caractère spécial']];
        }

        // Mettre à jour le mot de passe
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute([$newPasswordHash, $user_id])) {
            return ['success' => true, 'message' => 'Mot de passe mis à jour avec succès'];
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de la mise à jour du mot de passe']];
    }
}
?>