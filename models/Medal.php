<?php
require_once "config/database.php";
require_once "models/Game.php";

class Medal {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get the best test score for a user
     * @param int $user_id
     * @return float Best score (0-100) or 0 if no completed test
     */
    private function getBestTestScore($user_id) {
        try {
            require_once "models/TestAttempt.php";
            $testAttemptModel = new TestAttempt();
            $attempt = $testAttemptModel->getCompletedByUserId($user_id);
            
            if ($attempt && isset($attempt['score']) && $attempt['status'] === 'completed') {
                return (float)$attempt['score'];
            }
            return 0;
        } catch (Exception $e) {
            error_log("🏆 Error getting best test score for user " . $user_id . ": " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate and assign medal based on test score and published games count
     * 
     * Rules:
     * - Bronze: 30-50% score AND 5+ published games
     * - Silver: 50-99% score AND 10+ published games
     * - Gold: 100% score AND 10+ published games
     * 
     * @param int $user_id User ID
     * @param float|null $test_score Optional test score. If null, will fetch the best score from database
     * @param bool $force_update If true, will update even if user has a manually assigned medal
     * @return string The assigned medal ('none', 'bronze', 'silver', 'gold')
     */
    public function assignMedal($user_id, $test_score = null, $force_update = false) {
        // Get test score if not provided
        if ($test_score === null) {
            $test_score = $this->getBestTestScore($user_id);
        }

        // Get user's published games count
        require_once "config/database.php";
        $database = new Database();
        $conn = $database->getConnection();
        $gameModel = new Game($conn);
        $publishedGames = $gameModel->getPublishedGamesByUserId($user_id);
        $gamesCount = count($publishedGames);

        // Get current medal if exists
        $currentMedal = $this->getMedal($user_id);
        
        // If user has a manually assigned medal and we're not forcing update, check if we should keep it
        if (!$force_update && $currentMedal !== 'none') {
            $medalHierarchy = ['none' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3];
            $currentLevel = $medalHierarchy[$currentMedal] ?? 0;
            
            // Calculate what medal should be assigned automatically
            $calculatedMedal = $this->calculateMedal($test_score, $gamesCount);
            $calculatedLevel = $medalHierarchy[$calculatedMedal] ?? 0;
            
            // Only update if calculated medal is better than current
            if ($calculatedLevel <= $currentLevel) {
                error_log("🏆 Keeping existing medal " . $currentMedal . " (calculated " . $calculatedMedal . " is not better) for user ID: " . $user_id);
                return $currentMedal;
            }
        }

        $medal = $this->calculateMedal($test_score, $gamesCount);

        // Update user's medal
        $query = "UPDATE " . $this->table_name . " SET medal = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$medal, $user_id]);
        
        if ($result) {
            error_log("🏆 Medal assigned: " . $medal . " to user ID: " . $user_id . " (Score: " . $test_score . "%, Games: " . $gamesCount . ")");
        } else {
            error_log("🏆 Failed to assign medal: " . $medal . " to user ID: " . $user_id);
        }

        return $medal;
    }

    /**
     * Calculate medal based on test score and games count
     * @param float $test_score Test score (0-100)
     * @param int $gamesCount Number of published games
     * @return string Medal type
     */
    private function calculateMedal($test_score, $gamesCount) {
        $medal = 'none';

        // Bronze: 30-50% score AND 5+ games
        if ($test_score >= 30 && $test_score < 50 && $gamesCount >= 5) {
            $medal = 'bronze';
        }
        // Silver: 50-99% score AND 10+ games
        elseif ($test_score >= 50 && $test_score < 100 && $gamesCount >= 10) {
            $medal = 'silver';
        }
        // Gold: 100% score AND 10+ games
        elseif ($test_score == 100 && $gamesCount >= 10) {
            $medal = 'gold';
        }

        return $medal;
    }

    /**
     * Recalculate and assign medal for a user based on their current test score and published games
     * This is useful when a game is published/applied, to update the medal automatically
     * @param int $user_id User ID
     * @return string The assigned medal
     */
    public function recalculateMedal($user_id) {
        return $this->assignMedal($user_id, null, true);
    }

    public function getMedal($user_id) {
        try {
            $query = "SELECT medal FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $medal = $result ? ($result['medal'] ?? 'none') : 'none';
            
            // Normalize medal value (handle NULL, empty string, etc.)
            if (empty($medal) || !in_array($medal, ['none', 'bronze', 'silver', 'gold'])) {
                $medal = 'none';
            }
            
            error_log("🏆 Medal::getMedal() - User ID: " . $user_id . ", Medal: " . $medal);
            return $medal;
        } catch (PDOException $e) {
            error_log("🏆 Error in Medal::getMedal(): " . $e->getMessage());
            return 'none';
        }
    }

    public function getUserStats($user_id) {
        $gameModel = new Game();
        $publishedGames = $gameModel->getPublishedGamesByUserId($user_id);
        
        return [
            'medal' => $this->getMedal($user_id),
            'published_games_count' => count($publishedGames)
        ];
    }
}
?>

