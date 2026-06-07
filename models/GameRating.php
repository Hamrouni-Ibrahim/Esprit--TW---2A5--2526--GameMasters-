<?php
class GameRating {
    private $conn;
    private $table_name = "game_ratings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter ou mettre à jour une note
    public function addOrUpdateRating($userId, $gameId, $rating) {
        try {
            // Vérifier si une note existe déjà
            $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = :user_id AND game_id = :game_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":game_id", $gameId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Mise à jour
                $query = "UPDATE " . $this->table_name . " SET rating = :rating, created_at = NOW() WHERE user_id = :user_id AND game_id = :game_id";
            } else {
                // Insertion
                $query = "INSERT INTO " . $this->table_name . " (user_id, game_id, rating) VALUES (:user_id, :game_id, :rating)";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":game_id", $gameId);
            $stmt->bindParam(":rating", $rating);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur GameRating::addOrUpdateRating: " . $e->getMessage());
            return false;
        }
    }

    // Obtenir la note moyenne d'un jeu
    public function getAverageRating($gameId) {
        try {
            $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM " . $this->table_name . " WHERE game_id = :game_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":game_id", $gameId);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'average' => $row['avg_rating'] ? round($row['avg_rating'], 1) : 0,
                'count' => $row['count']
            ];
        } catch (PDOException $e) {
            error_log("Erreur GameRating::getAverageRating: " . $e->getMessage());
            return ['average' => 0, 'count' => 0];
        }
    }

    // Obtenir la note d'un utilisateur pour un jeu
    public function getUserRating($userId, $gameId) {
        try {
            $query = "SELECT rating FROM " . $this->table_name . " WHERE user_id = :user_id AND game_id = :game_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":game_id", $gameId);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row['rating'];
            }
            return 0;
        } catch (PDOException $e) {
            error_log("Erreur GameRating::getUserRating: " . $e->getMessage());
            return 0;
        }
    }
    
    // Obtenir toutes les notes d'un jeu avec les informations des utilisateurs
    public function getGameRatingsWithUsers($gameId) {
        try {
            $query = "SELECT gr.rating, gr.created_at, u.id as user_id, u.username, u.email
                      FROM " . $this->table_name . " gr
                      INNER JOIN users u ON gr.user_id = u.id
                      WHERE gr.game_id = :game_id
                      ORDER BY gr.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":game_id", $gameId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur GameRating::getGameRatingsWithUsers: " . $e->getMessage());
            return [];
        }
    }
}
?>
