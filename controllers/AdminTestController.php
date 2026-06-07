<?php
require_once "models/TestRequest.php";
require_once "models/TestQuestion.php";
require_once "models/TestAttempt.php";
require_once "models/TestApproval.php";
require_once "models/Medal.php";

class AdminTestController {
    
    // Admin: List test requests
    public function listRequests() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $testRequest = new TestRequest();
        $status = $_GET['status'] ?? null;
        $requests = $testRequest->getAll($status);
        
        // Debug: Log request count
        error_log("AdminTestController::listRequests() - Found " . count($requests) . " requests. Status filter: " . ($status ?? 'all'));
        
        include "views/admin/test_requests_list.php";
    }
    
    // Admin: Review test request
    public function reviewRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $requestId = $_GET['id'] ?? null;
        if (!$requestId) {
            header("Location: ?controller=adminTest&action=listRequests");
            exit;
        }
        
        $testRequest = new TestRequest();
        $request = $testRequest->getById($requestId);
        
        if (!$request) {
            $_SESSION['error_message'] = "Demande non trouvée.";
            header("Location: ?controller=adminTest&action=listRequests");
            exit;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $status = $_POST['status'] ?? null;
            $admin_response = $_POST['admin_response'] ?? null;
            
            if ($status && in_array($status, ['approved', 'rejected'])) {
                if ($testRequest->updateStatus($requestId, $status, $_SESSION['user_id'], $admin_response)) {
                    $_SESSION['success_message'] = "Demande " . ($status === 'approved' ? 'approuvée' : 'rejetée') . " avec succès.";
                    header("Location: ?controller=adminTest&action=listRequests");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
                }
            }
        }
        
        include "views/admin/test_request_review.php";
    }
    
    // Admin: List test attempts
    public function listAttempts() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $testAttempt->expireOldAttempts(); // Clean up expired attempts
        $status = $_GET['status'] ?? null;
        $attempts = $testAttempt->getAll($status);
        
        include "views/admin/test_attempts_list.php";
    }
    
    // Admin: List test approvals
    public function listApprovals() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $testApproval = new TestApproval();
        $status = $_GET['status'] ?? null;
        $approvals = $testApproval->getAll($status);
        
        include "views/admin/test_approvals_list.php";
    }
    
    // Admin: Review test result
    public function reviewResult() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $attemptId = $_GET['attempt_id'] ?? null;
        if (!$attemptId) {
            header("Location: ?controller=adminTest&action=listAttempts");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt) {
            $_SESSION['error_message'] = "Tentative de test non trouvée.";
            header("Location: ?controller=adminTest&action=listAttempts");
            exit;
        }
        
        require_once "models/TestAnswer.php";
        $testAnswer = new TestAnswer();
        $answers = $testAnswer->getAnswersByAttempt($attemptId);
        
        $testApproval = new TestApproval();
        $approval = $testApproval->getByAttemptId($attemptId);
        
        // Store attemptId for view
        $attemptId = $attempt['id'];
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $status = $_POST['status'] ?? null;
            $admin_notes = $_POST['admin_notes'] ?? null;
            
            if ($status && in_array($status, ['approved', 'rejected'])) {
                if ($approval) {
                    $testApproval->updateStatus($attemptId, $status, $admin_notes);
                } else {
                    // Create approval if it doesn't exist yet
                    $testApproval->create($attemptId, $_SESSION['user_id']);
                    $testApproval->updateStatus($attemptId, $status, $admin_notes);
                }
                
                // If approved, assign medal AUTOMATICALLY
                // BUT: Don't overwrite manually assigned medals unless automatic is better
                if ($status === 'approved') {
                    try {
                        require_once "models/Medal.php";
                        $medal = new Medal();
                        
                        // Check current medal before automatic assignment
                        $currentMedal = $medal->getMedal($attempt['user_id']);
                        error_log("🏆 Current medal before automatic assignment: " . $currentMedal);
                        
                        // Medal hierarchy: none < bronze < silver < gold
                        $medalHierarchy = ['none' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3];
                        $currentMedalLevel = $medalHierarchy[$currentMedal] ?? 0;
                        
                        error_log("🏆 Starting automatic medal assignment for user ID: " . $attempt['user_id'] . " with score: " . $attempt['score']);
                        
                        $assignedMedal = $medal->assignMedal($attempt['user_id'], $attempt['score']);
                        
                        error_log("🏆 Medal assignment result: " . $assignedMedal . " for user ID: " . $attempt['user_id']);
                        
                        // If user already has a manually assigned medal, only overwrite if automatic is better
                        $assignedMedalLevel = $medalHierarchy[$assignedMedal] ?? 0;
                        if ($currentMedal !== 'none' && $assignedMedalLevel <= $currentMedalLevel) {
                            error_log("🏆 Keeping existing medal " . $currentMedal . " (automatic " . $assignedMedal . " is not better)");
                            // Restore the original medal
                            require_once "config/database.php";
                            $database = new Database();
                            $conn = $database->getConnection();
                            $restoreQuery = "UPDATE users SET medal = ? WHERE id = ?";
                            $restoreStmt = $conn->prepare($restoreQuery);
                            $restoreStmt->execute([$currentMedal, $attempt['user_id']]);
                            $assignedMedal = $currentMedal; // Use current medal for notification logic
                        }
                        
                        // Verify the medal was actually saved
                        $verifyMedal = $medal->getMedal($attempt['user_id']);
                        if ($verifyMedal !== $assignedMedal) {
                            error_log("🏆 WARNING: Medal mismatch! Expected: " . $assignedMedal . ", Got: " . $verifyMedal);
                            // Try to update again
                            require_once "config/database.php";
                            $database = new Database();
                            $conn = $database->getConnection();
                            $updateQuery = "UPDATE users SET medal = ? WHERE id = ?";
                            $updateStmt = $conn->prepare($updateQuery);
                            $updateStmt->execute([$assignedMedal, $attempt['user_id']]);
                            error_log("🏆 Forced medal update to: " . $assignedMedal);
                        }
                        
                        // Mark that user needs to see medal notification on next login
                        if ($assignedMedal !== 'none') {
                            require_once "config/database.php";
                            $database = new Database();
                            $conn = $database->getConnection();
                            
                            // Check if medal_notification_seen column exists
                            $columnCheck = "SHOW COLUMNS FROM users LIKE 'medal_notification_seen'";
                            $columnStmt = $conn->prepare($columnCheck);
                            $columnStmt->execute();
                            $hasColumn = $columnStmt->rowCount() > 0;
                            
                            if ($hasColumn) {
                                try {
                                    $query = "UPDATE users SET medal_notification_seen = 0 WHERE id = ?";
                                    $stmt = $conn->prepare($query);
                                    $result = $stmt->execute([$attempt['user_id']]);
                                    if ($result) {
                                        error_log("🏆 Medal notification flag set to 0 for user ID: " . $attempt['user_id'] . " - Medal: " . $assignedMedal);
                                    } else {
                                        error_log("🏆 Failed to set medal_notification_seen = 0 for user ID: " . $attempt['user_id']);
                                    }
                                } catch (PDOException $e) {
                                    error_log("🏆 Error setting medal_notification_seen: " . $e->getMessage());
                                }
                            } else {
                                error_log("🏆 medal_notification_seen column doesn't exist - notification system not available");
                            }
                        } else {
                            error_log("🏆 No medal assigned (medal = none) for user ID: " . $attempt['user_id'] . " - Score: " . $attempt['score'] . "%");
                        }
                        
                        // Add success message about medal
                        if ($assignedMedal !== 'none') {
                            $_SESSION['success_message'] = "Résultat du test approuvé avec succès. Médaille " . ucfirst($assignedMedal) . " attribuée automatiquement.";
                        } else {
                            $_SESSION['success_message'] = "Résultat du test approuvé avec succès. Aucune médaille attribuée (critères non remplis).";
                        }
                    } catch (Exception $e) {
                        error_log("🏆 ERROR in automatic medal assignment: " . $e->getMessage());
                        error_log("🏆 Stack trace: " . $e->getTraceAsString());
                        $_SESSION['error_message'] = "Erreur lors de l'attribution automatique de la médaille: " . $e->getMessage();
                    }
                } else {
                    // If rejected, clear any existing medal assignment from this test
                    // (We don't remove medals, but we log it)
                    error_log("🏆 Test rejected - no medal assignment for user ID: " . $attempt['user_id']);
                    $_SESSION['success_message'] = "Résultat du test rejeté avec succès.";
                }
                
                header("Location: ?controller=adminTest&action=listAttempts");
                exit;
            }
        }
        
        include "views/admin/test_result_review.php";
    }
    
    // Admin: Manually assign medal to user
    public function assignMedal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $attemptId = $_GET['attempt_id'] ?? null;
        if (!$attemptId) {
            $_SESSION['error_message'] = "ID de tentative invalide.";
            header("Location: ?controller=adminTest&action=listAttempts");
            exit;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $medal = $_POST['medal'] ?? 'none';
            
            // Validate medal
            $validMedals = ['none', 'bronze', 'silver', 'gold'];
            if (!in_array($medal, $validMedals)) {
                $_SESSION['error_message'] = "Médaille invalide.";
                header("Location: ?controller=adminTest&action=reviewResult&attempt_id=" . $attemptId);
                exit;
            }
            
            // Get attempt to find user_id
            $testAttempt = new TestAttempt();
            $attempt = $testAttempt->getById($attemptId);
            
            if (!$attempt) {
                $_SESSION['error_message'] = "Tentative non trouvée.";
                header("Location: ?controller=adminTest&action=listAttempts");
                exit;
            }
            
            // Update medal directly
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            
            try {
                // Check if medal column exists
                $columnCheck = "SHOW COLUMNS FROM users LIKE 'medal'";
                $columnStmt = $conn->prepare($columnCheck);
                $columnStmt->execute();
                $hasColumn = $columnStmt->rowCount() > 0;
                
                if ($hasColumn) {
                    $query = "UPDATE users SET medal = ? WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $result = $stmt->execute([$medal, $attempt['user_id']]);
                    
                    error_log("🏆 UPDATE Query executed - Result: " . ($result ? 'SUCCESS' : 'FAILED'));
                    error_log("🏆 UPDATE Query - Medal value: " . var_export($medal, true));
                    error_log("🏆 UPDATE Query - User ID: " . $attempt['user_id']);
                    error_log("🏆 UPDATE Query - Rows affected: " . $stmt->rowCount());
                    
                    // Verify the update actually worked by querying the database
                    $verifyQuery = "SELECT medal FROM users WHERE id = ?";
                    $verifyStmt = $conn->prepare($verifyQuery);
                    $verifyStmt->execute([$attempt['user_id']]);
                    $verifyResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                    $verifiedMedal = $verifyResult ? ($verifyResult['medal'] ?? 'none') : 'none';
                    
                    error_log("🏆 VERIFICATION - Medal in database after update: " . var_export($verifiedMedal, true));
                    
                    if ($result && $verifiedMedal === $medal) {
                        $medalNames = ['none' => 'Aucune', 'bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold'];
                        $_SESSION['success_message'] = "Médaille " . $medalNames[$medal] . " attribuée manuellement avec succès.";
                        error_log("🏆 Manual medal assignment: " . $medal . " to user ID: " . $attempt['user_id'] . " by admin ID: " . $_SESSION['user_id']);
                        error_log("🏆 Medal updated in database - VERIFIED");
                        
                        // If medal is not 'none', set notification flag
                        if ($medal !== 'none') {
                            $notifCheck = "SHOW COLUMNS FROM users LIKE 'medal_notification_seen'";
                            $notifStmt = $conn->prepare($notifCheck);
                            $notifStmt->execute();
                            $hasNotifColumn = $notifStmt->rowCount() > 0;
                            
                            if ($hasNotifColumn) {
                                $notifQuery = "UPDATE users SET medal_notification_seen = 0 WHERE id = ?";
                                $notifStmt = $conn->prepare($notifQuery);
                                $notifStmt->execute([$attempt['user_id']]);
                                error_log("🏆 Medal notification flag set to 0 for user ID: " . $attempt['user_id']);
                            }
                        }
                    } else {
                        $errorMsg = "Erreur lors de l'attribution de la médaille. ";
                        if ($verifiedMedal !== $medal) {
                            $errorMsg .= "La médaille n'a pas été sauvegardée correctement (attendu: " . $medal . ", obtenu: " . $verifiedMedal . ").";
                        }
                        $_SESSION['error_message'] = $errorMsg;
                        error_log("🏆 ERROR - Medal update failed or verification failed!");
                        error_log("🏆 ERROR - Expected: " . $medal . ", Got: " . $verifiedMedal);
                    }
                } else {
                    $_SESSION['error_message'] = "La colonne 'medal' n'existe pas dans la base de données.";
                }
            } catch (PDOException $e) {
                error_log("🏆 Error assigning medal manually: " . $e->getMessage());
                $_SESSION['error_message'] = "Erreur lors de l'attribution de la médaille: " . $e->getMessage();
            }
            
            header("Location: ?controller=adminTest&action=reviewResult&attempt_id=" . $attemptId);
            exit;
        }
        
        header("Location: ?controller=adminTest&action=listAttempts");
        exit;
    }
    
    // Admin: Manage questions
    public function manageQuestions() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $testQuestion = new TestQuestion();
        $questions = $testQuestion->getAll(false);
        
        include "views/admin/test_questions_list.php";
    }
    
    // Admin: Add question
    public function addQuestion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $testQuestion = new TestQuestion();
            
            if ($testQuestion->create(
                $_POST['question'],
                $_POST['option_a'],
                $_POST['option_b'],
                $_POST['option_c'],
                $_POST['option_d'],
                $_POST['correct_answer'],
                $_POST['explanation'] ?? null
            )) {
                $_SESSION['success_message'] = "Question ajoutée avec succès.";
                header("Location: ?controller=adminTest&action=manageQuestions");
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'ajout de la question.";
            }
        }
        
        include "views/admin/test_question_add.php";
    }
    
    // Admin: Edit question
    public function editQuestion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $questionId = $_GET['id'] ?? null;
        if (!$questionId) {
            header("Location: ?controller=adminTest&action=manageQuestions");
            exit;
        }
        
        $testQuestion = new TestQuestion();
        $question = $testQuestion->getById($questionId);
        
        if (!$question) {
            $_SESSION['error_message'] = "Question non trouvée.";
            header("Location: ?controller=adminTest&action=manageQuestions");
            exit;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($testQuestion->update(
                $questionId,
                $_POST['question'],
                $_POST['option_a'],
                $_POST['option_b'],
                $_POST['option_c'],
                $_POST['option_d'],
                $_POST['correct_answer'],
                $_POST['explanation'] ?? null,
                isset($_POST['is_active']) ? 1 : 0
            )) {
                $_SESSION['success_message'] = "Question modifiée avec succès.";
                header("Location: ?controller=adminTest&action=manageQuestions");
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la modification.";
            }
        }
        
        include "views/admin/test_question_edit.php";
    }
    
    // Admin: Delete question
    public function deleteQuestion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $questionId = $_GET['id'] ?? null;
        if ($questionId) {
            $testQuestion = new TestQuestion();
            if ($testQuestion->delete($questionId)) {
                $_SESSION['success_message'] = "Question supprimée avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression.";
            }
        }
        
        header("Location: ?controller=adminTest&action=manageQuestions");
        exit;
    }
}
?>

