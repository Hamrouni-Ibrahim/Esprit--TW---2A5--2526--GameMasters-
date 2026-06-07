<?php
require_once "config/database.php";

class TestQuestion {
    private $conn;
    private $table_name = "test_questions";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll($active_only = true) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($active_only) {
            $query .= " WHERE is_active = 1";
        }
        $query .= " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRandomQuestions($limit = 10) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  ORDER BY RAND() 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (question, option_a, option_b, option_c, option_d, correct_answer, explanation) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation]);
    }

    public function update($id, $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation = null, $is_active = 1) {
        $query = "UPDATE " . $this->table_name . " 
                  SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, 
                      correct_answer = ?, explanation = ?, is_active = ?, updated_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation, $is_active, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function toggleActive($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_active = NOT is_active, updated_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>






