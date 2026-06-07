<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $status;
    public $email_verified;
    public $verification_code;
    public $reset_code;
    public $reset_code_expires;
    public $created_at;
    public $avatar;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un utilisateur
    public function create() {
        // Vérifier si les colonnes de vérification existent
        $hasVerificationColumns = $this->checkColumnExists('email_verified');
        $hasAvatarColumn = $this->checkColumnExists('avatar');
        
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, email=:email, password=:password, role=:role, status=:status";
        
        if($hasVerificationColumns) {
            $query .= ", email_verified=:email_verified, verification_code=:verification_code";
        }
        
        if($hasAvatarColumn) {
            $query .= ", avatar=:avatar";
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage des données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        // Hash du mot de passe
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":status", $this->status);
        
        if($hasVerificationColumns) {
            $emailVerified = isset($this->email_verified) ? $this->email_verified : 0;
            $verificationCode = isset($this->verification_code) ? $this->verification_code : null;
            $stmt->bindParam(":email_verified", $emailVerified);
            $stmt->bindParam(":verification_code", $verificationCode);
        }
        
        if($hasAvatarColumn) {
            $avatar = (!empty($this->avatar)) ? $this->avatar : null;
            error_log("User::create() - Avatar value: " . ($avatar ?? 'NULL'));
            $stmt->bindParam(":avatar", $avatar);
        }
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    // Vérifier si une colonne existe
    private function checkColumnExists($columnName) {
        try {
            $query = "SHOW COLUMNS FROM " . $this->table_name . " LIKE ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$columnName]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Vérifier le code de vérification
    public function verifyEmail($userId, $code) {
        try {
            if(!$this->checkColumnExists('verification_code')) {
                return false;
            }
            
            $query = "SELECT id, verification_code FROM " . $this->table_name . " 
                      WHERE id = ? AND verification_code = ? AND email_verified = 0 LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId, $code]);
            
            if($stmt->rowCount() > 0) {
                // Mettre à jour l'email comme vérifié
                $updateQuery = "UPDATE " . $this->table_name . " 
                                SET email_verified = 1, verification_code = NULL 
                                WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                return $updateStmt->execute([$userId]);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur verifyEmail: " . $e->getMessage());
            return false;
        }
    }
    
    // Mettre à jour le code de vérification
    public function updateVerificationCode($userId, $code) {
        try {
            if(!$this->checkColumnExists('verification_code')) {
                return false;
            }
            
            $query = "UPDATE " . $this->table_name . " 
                      SET verification_code = ? 
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$code, $userId]);
        } catch(PDOException $e) {
            error_log("Erreur updateVerificationCode: " . $e->getMessage());
            return false;
        }
    }
    
    // Vérifier si l'email est vérifié
    public function isEmailVerified($userId) {
        try {
            if(!$this->checkColumnExists('email_verified')) {
                return true; // Si la colonne n'existe pas, considérer comme vérifié
            }
            
            $query = "SELECT email_verified FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($row['email_verified']) && $row['email_verified'] == 1;
        } catch(PDOException $e) {
            error_log("Erreur isEmailVerified: " . $e->getMessage());
            return true;
        }
    }
    
    // Générer et sauvegarder un code de réinitialisation
    public function generateResetCode($email) {
        try {
            if(!$this->checkColumnExists('reset_code')) {
                return false;
            }
            
            $resetCode = bin2hex(random_bytes(16));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $query = "UPDATE " . $this->table_name . " 
                      SET reset_code = ?, reset_code_expires = ? 
                      WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            
            if($stmt->execute([$resetCode, $expiresAt, $email])) {
                return $resetCode;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur generateResetCode: " . $e->getMessage());
            return false;
        }
    }
    
    // Vérifier le code de réinitialisation
    public function verifyResetCode($email, $code) {
        try {
            if(!$this->checkColumnExists('reset_code')) {
                return false;
            }
            
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE email = ? AND reset_code = ? 
                      AND reset_code_expires > NOW() LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email, $code]);
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['id'];
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur verifyResetCode: " . $e->getMessage());
            return false;
        }
    }
    
    // Réinitialiser le mot de passe
    public function resetPassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $query = "UPDATE " . $this->table_name . " 
                      SET password = ?";
            
            if($this->checkColumnExists('reset_code')) {
                $query .= ", reset_code = NULL, reset_code_expires = NULL";
            }
            
            $query .= " WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$hashedPassword, $userId]);
        } catch(PDOException $e) {
            error_log("Erreur resetPassword: " . $e->getMessage());
            return false;
        }
    }

    // Vérifier si l'email existe
    public function emailExists() {
        // Check if avatar column exists
        $hasAvatarColumn = $this->checkColumnExists('avatar');
        
        $query = "SELECT id, username, password, role, status";
        if ($hasAvatarColumn) {
            $query .= ", avatar";
        }
        $query .= " FROM " . $this->table_name . " 
                  WHERE email = ? 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            if ($hasAvatarColumn && isset($row['avatar'])) {
                $this->avatar = $row['avatar'];
            }
            return true;
        }
        return false;
    }

    // Vérifier si le nom d'utilisateur existe
    public function checkUsernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Lire tous les utilisateurs
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire un utilisateur par ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'] ?? null;
            $this->role = $row['role'];
            $this->status = $row['status'];
            $this->avatar = $row['avatar'] ?? null;
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Mettre à jour un utilisateur
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username=:username, email=:email, role=:role, status=:status 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Vérifier si l'utilisateur peut ajouter des jeux
    // DEPRECATED: Tous les utilisateurs peuvent maintenant proposer des jeux
    public function canAddGames($userId) {
        // Tous les utilisateurs connectés peuvent proposer des jeux
        return true;
    }
    
    // Mettre à jour la permission d'ajout de jeux
    // DEPRECATED: Cette fonction n'est plus utilisée
    public function updateGamePermission($userId, $canAdd) {
        // Ne fait plus rien car tous les utilisateurs peuvent proposer des jeux
        return true;
    }

    // Supprimer un utilisateur
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
}
?>
