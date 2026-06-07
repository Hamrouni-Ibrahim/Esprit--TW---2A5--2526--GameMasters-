<?php
class Donation {
    private $conn;
    private $table_name = "donations";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addDonation($name, $email, $amount, $project_id = null) {
        try {
            $sql = "INSERT INTO donations (name, email, amount, project_id, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $email, $amount, $project_id]);
            return true;
        } catch (Exception $e) {
            error_log("Donation error: " . $e->getMessage());
            return false;
        }
    }

    public function getDonationsByEmail($email) {
        try {
            $sql = "SELECT d.*, p.title as project_title 
                    FROM donations d 
                    LEFT JOIN projects p ON d.project_id = p.id 
                    WHERE d.email = ? 
                    ORDER BY d.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get donations by email error: " . $e->getMessage());
            return [];
        }
    }

    public function getAllDonations() {
        try {
            // Try to include user_id from users table via email
            try {
                $sql = "SELECT d.*, p.title as project_title, u.id as user_id, u.username, u.role, u.status, u.avatar, u.created_at as user_created_at
                        FROM donations d 
                        LEFT JOIN projects p ON d.project_id = p.id 
                        LEFT JOIN users u ON d.email = u.email
                        ORDER BY d.created_at DESC";
                $stmt = $this->conn->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Fallback if users table doesn't exist or join fails
                error_log("Error joining with users table: " . $e->getMessage());
                $sql = "SELECT d.*, p.title as project_title 
                        FROM donations d 
                        LEFT JOIN projects p ON d.project_id = p.id 
                        ORDER BY d.created_at DESC";
                $stmt = $this->conn->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Get all donations error: " . $e->getMessage());
            return [];
        }
    }

    public function updateDonation($id, $name, $email, $amount, $project_id = null) {
        try {
            $sql = "UPDATE donations SET name = ?, email = ?, amount = ?, project_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $email, $amount, $project_id, $id]);
            return true;
        } catch (Exception $e) {
            error_log("Update donation error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDonation($id) {
        try {
            $sql = "DELETE FROM donations WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Delete donation error: " . $e->getMessage());
            return false;
        }
    }

    public function getStatistics() {
        $stats = [];
        try {
            // Total donations
            $stmt = $this->conn->query("SELECT COUNT(*) as count, SUM(amount) as total FROM donations");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_donations'] = $result['count'] ?? 0;
            $stats['total_amount'] = $result['total'] ?? 0;
            
            // Donations per project
            $stmt = $this->conn->query("SELECT p.title, COUNT(d.id) as count, SUM(d.amount) as total 
                                       FROM donations d 
                                       LEFT JOIN projects p ON d.project_id = p.id 
                                       GROUP BY d.project_id, p.title");
            $stats['donations_per_project'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get donation statistics error: " . $e->getMessage());
            $stats['total_donations'] = 0;
            $stats['total_amount'] = 0;
            $stats['donations_per_project'] = [];
        }
        return $stats;
    }
}
?>



