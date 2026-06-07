<?php

class FaceAuth {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Sauvegarder le descripteur facial d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param array $descriptor Descripteur facial (tableau de nombres)
     * @return bool Succès de l'opération
     */
    public function saveFaceDescriptor($userId, $descriptor) {
        try {
            // Convertir le descripteur en JSON
            $descriptorJson = json_encode($descriptor);
            
            $stmt = $this->db->prepare("
                UPDATE users 
                SET face_descriptor = ?,
                    face_registered_at = NOW(),
                    face_enabled = TRUE
                WHERE id = ?
            ");
            
            $stmt->execute([$descriptorJson, $userId]);
            $rowCount = $stmt->rowCount();
            error_log("FaceAuth Model: Update executed. Row count: " . $rowCount);
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde du descripteur facial: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer le descripteur facial d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array|null Descripteur facial ou null si non trouvé
     */
    public function getFaceDescriptor($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT face_descriptor 
                FROM users 
                WHERE id = ? AND face_enabled = TRUE
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['face_descriptor']) {
                return json_decode($result['face_descriptor'], true);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du descripteur facial: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Trouver un utilisateur par similarité faciale
     * @param array $descriptor Descripteur facial à comparer
     * @param float $threshold Seuil de distance euclidienne (plus petit = plus strict)
     * @return array|null Informations de l'utilisateur trouvé ou null
     */
    public function findUserByFace($descriptor, $threshold = 0.6) {
        try {
            // Récupérer tous les utilisateurs ayant un visage enregistré
            $stmt = $this->db->prepare("
                SELECT id, username, email, face_descriptor, role
                FROM users 
                WHERE face_descriptor IS NOT NULL AND face_enabled = TRUE
            ");
            
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $bestMatch = null;
            $bestDistance = PHP_FLOAT_MAX;
            
            // Comparer avec chaque utilisateur
            foreach ($users as $user) {
                $storedDescriptor = json_decode($user['face_descriptor'], true);
                
                if (!$storedDescriptor || !is_array($storedDescriptor)) {
                    continue;
                }
                
                // Calculer la distance euclidienne
                $distance = $this->euclideanDistance($descriptor, $storedDescriptor);
                
                // Garder le meilleur match
                if ($distance < $bestDistance && $distance < $threshold) {
                    $bestDistance = $distance;
                    $bestMatch = $user;
                    $bestMatch['match_distance'] = $distance;
                }
            }
            
            return $bestMatch;
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche par visage: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculer la distance euclidienne entre deux descripteurs
     * @param array $descriptor1 Premier descripteur
     * @param array $descriptor2 Deuxième descripteur
     * @return float Distance euclidienne
     */
    private function euclideanDistance($descriptor1, $descriptor2) {
        if (count($descriptor1) !== count($descriptor2)) {
            return PHP_FLOAT_MAX;
        }
        
        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }
        
        return sqrt($sum);
    }
    
    /**
     * Supprimer le descripteur facial d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function deleteFaceDescriptor($userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET face_descriptor = NULL,
                    face_registered_at = NULL,
                    face_enabled = FALSE
                WHERE id = ?
            ");
            
            $stmt->execute([$userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du descripteur facial: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activer/désactiver la reconnaissance faciale pour un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param bool $enabled Activer ou désactiver
     * @return bool Succès de l'opération
     */
    public function updateFaceStatus($userId, $enabled) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET face_enabled = ?
                WHERE id = ? AND face_descriptor IS NOT NULL
            ");
            
            $stmt->execute([$enabled ? 1 : 0, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut facial: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier si un utilisateur a un visage enregistré
     * @param int $userId ID de l'utilisateur
     * @return bool True si un visage est enregistré
     */
    public function hasFaceRegistered($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE id = ? AND face_descriptor IS NOT NULL
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du visage: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les informations de reconnaissance faciale d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array|null Informations sur la reconnaissance faciale
     */
    public function getFaceInfo($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    face_descriptor IS NOT NULL as has_face,
                    face_enabled,
                    face_registered_at
                FROM users 
                WHERE id = ?
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des infos faciales: " . $e->getMessage());
            return null;
        }
    }
}

?>
