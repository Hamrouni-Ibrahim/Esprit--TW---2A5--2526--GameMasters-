<?php
require_once "config/database.php";

class TestAnswer {
    private $conn;
    private $table_name = "test_answers";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function saveAnswer($test_attempt_id, $question_id, $user_answer, $is_correct) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (test_attempt_id, question_id, user_answer, is_correct) 
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                  user_answer = VALUES(user_answer), 
                  is_correct = VALUES(is_correct)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$test_attempt_id, $question_id, $user_answer, $is_correct]);
    }

    public function getAnswersByAttempt($test_attempt_id) {
        $query = "SELECT ta.*, tq.question, tq.option_a, tq.option_b, tq.option_c, tq.option_d, 
                         tq.correct_answer, tq.explanation 
                  FROM " . $this->table_name . " ta 
                  JOIN test_questions tq ON ta.question_id = tq.id 
                  WHERE ta.test_attempt_id = ? 
                  ORDER BY ta.question_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$test_attempt_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>






