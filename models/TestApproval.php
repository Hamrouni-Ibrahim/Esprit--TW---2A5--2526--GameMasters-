<?php
require_once "config/database.php";

class TestApproval {
    private $conn;
    private $table_name = "test_approvals";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($test_attempt_id, $admin_id) {
        $query = "INSERT INTO " . $this->table_name . " (test_attempt_id, admin_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$test_attempt_id, $admin_id]);
    }

    public function getByAttemptId($test_attempt_id) {
        $query = "SELECT ta.*, u.username as admin_username 
                  FROM " . $this->table_name . " ta 
                  JOIN users u ON ta.admin_id = u.id 
                  WHERE ta.test_attempt_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$test_attempt_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($test_attempt_id, $status, $admin_notes = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = ?, admin_notes = ?, updated_at = NOW() 
                  WHERE test_attempt_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $admin_notes, $test_attempt_id]);
    }

    public function getAll($status = null) {
        $query = "SELECT ta.*, u.username as admin_username, 
                         tat.user_id, usr.username, usr.email, tat.score, tat.id as attempt_id
                  FROM " . $this->table_name . " ta 
                  LEFT JOIN users u ON ta.admin_id = u.id 
                  LEFT JOIN test_attempts tat ON ta.test_attempt_id = tat.id 
                  LEFT JOIN users usr ON tat.user_id = usr.id";
        
        if ($status) {
            $query .= " WHERE ta.status = ?";
        }
        
        $query .= " ORDER BY ta.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->execute([$status]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


