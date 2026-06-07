<?php
require_once "config/database.php";

class TestAttempt {
    private $conn;
    private $table_name = "test_attempts";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($user_id, $test_request_id, $time_limit = 1800) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, test_request_id, time_limit, status) 
                  VALUES (?, ?, ?, 'in_progress')";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([$user_id, $test_request_id, $time_limit])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT ta.*, u.username, u.email 
                  FROM " . $this->table_name . " ta 
                  JOIN users u ON ta.user_id = u.id 
                  WHERE ta.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = ? 
                  ORDER BY started_at DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInProgressByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = ? AND status = 'in_progress' 
                  ORDER BY started_at DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function submit($id, $score, $total_questions, $correct_answers, $time_taken) {
        $query = "UPDATE " . $this->table_name . " 
                  SET score = ?, total_questions = ?, correct_answers = ?, 
                      time_taken = ?, status = 'completed', submitted_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$score, $total_questions, $correct_answers, $time_taken, $id]);
    }

    public function getAll($status = null) {
        $query = "SELECT ta.*, u.username, u.email, tr.motivational_letter 
                  FROM " . $this->table_name . " ta 
                  JOIN users u ON ta.user_id = u.id 
                  JOIN test_requests tr ON ta.test_request_id = tr.id";
        
        if ($status) {
            $query .= " WHERE ta.status = ?";
        }
        
        $query .= " ORDER BY ta.started_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->execute([$status]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function expireOldAttempts() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'expired' 
                  WHERE status = 'in_progress' 
                  AND TIMESTAMPDIFF(SECOND, started_at, NOW()) > time_limit";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    public function getRemainingTime($id) {
        $query = "SELECT time_limit, TIMESTAMPDIFF(SECOND, started_at, NOW()) as elapsed 
                  FROM " . $this->table_name . " 
                  WHERE id = ? AND status = 'in_progress'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $remaining = $result['time_limit'] - $result['elapsed'];
            return max(0, $remaining);
        }
        return 0;
    }

    public function getCompletedByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = ? AND status IN ('completed', 'expired')
                  ORDER BY started_at DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

