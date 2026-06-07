<?php
require_once "config/database.php";

class TestRequest {
    private $conn;
    private $table_name = "test_requests";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($user_id, $motivational_letter) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (user_id, motivational_letter) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$user_id, $motivational_letter]);
            
            if ($result) {
                $insertId = $this->conn->lastInsertId();
                error_log("TestRequest created successfully. ID: " . $insertId . ", User ID: " . $user_id);
                return true;
            } else {
                error_log("TestRequest::create() failed - execute returned false for user ID: " . $user_id);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error creating test request: " . $e->getMessage() . " - User ID: " . $user_id);
            return false;
        }
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT tr.*, u.username, u.email 
                  FROM " . $this->table_name . " tr 
                  JOIN users u ON tr.user_id = u.id 
                  WHERE tr.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($status = null) {
        try {
            $query = "SELECT tr.*, u.username, u.email 
                      FROM " . $this->table_name . " tr 
                      LEFT JOIN users u ON tr.user_id = u.id";
            
            if ($status) {
                $query .= " WHERE tr.status = ?";
            }
            
            $query .= " ORDER BY tr.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            if ($status) {
                $stmt->execute([$status]);
            } else {
                $stmt->execute();
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log for debugging if empty
            if (empty($results)) {
                error_log("TestRequest::getAll() - No requests found. Status filter: " . ($status ?? 'none'));
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error in TestRequest::getAll(): " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus($id, $status, $admin_id, $admin_response = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = ?, admin_id = ?, admin_response = ?, updated_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $admin_id, $admin_response, $id]);
    }

    public function hasPendingRequest($user_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE user_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function hasApprovedRequest($user_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE user_id = ? AND status = 'approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }
}
?>

