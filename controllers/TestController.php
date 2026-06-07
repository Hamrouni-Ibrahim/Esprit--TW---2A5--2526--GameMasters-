<?php
require_once "models/TestRequest.php";
require_once "models/TestQuestion.php";
require_once "models/TestAttempt.php";
require_once "models/TestAnswer.php";
require_once "models/Medal.php";

class TestController {
    
    // User: Request test access
    public function requestAccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $testRequest = new TestRequest();
        
        // Check if user has already completed a test (only 1 test per account allowed)
        require_once "models/TestAttempt.php";
        $testAttempt = new TestAttempt();
        $completedAttempt = $testAttempt->getCompletedByUserId($userId);
        if ($completedAttempt) {
            $_SESSION['error_message'] = "Vous avez déjà passé le test. Un seul test est autorisé par compte.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Check if user already has a pending request
        if ($testRequest->hasPendingRequest($userId)) {
            $_SESSION['error_message'] = "Vous avez déjà une demande en attente d'approbation.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Check if user has approved request that hasn't been completed yet
        $approvedRequest = $testRequest->getByUserId($userId);
        if ($approvedRequest && $approvedRequest['status'] === 'approved') {
            // Check if user has an in-progress attempt
            $attempt = $testAttempt->getByUserId($userId);
            
            // If there's an approved request but no completed attempt, allow to take test
            if (!$attempt || $attempt['status'] === 'in_progress') {
                $_SESSION['info_message'] = "Votre demande a déjà été approuvée. Vous pouvez passer le test.";
                header("Location: ?controller=test&action=takeTest");
                exit;
            }
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['motivational_letter'])) {
            $motivational_letter = trim($_POST['motivational_letter']);
            
            if (strlen($motivational_letter) < 50) {
                $_SESSION['error_message'] = "Votre lettre de motivation doit contenir au moins 50 caractères.";
            } else {
                $result = $testRequest->create($userId, $motivational_letter);
                if ($result) {
                    $_SESSION['success_message'] = "Votre demande a été envoyée avec succès. En attente d'approbation par l'administrateur.";
                    error_log("Test request created successfully for user ID: " . $userId);
                    header("Location: ?controller=test&action=status");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Erreur lors de l'envoi de votre demande. Veuillez réessayer.";
                    error_log("Failed to create test request for user ID: " . $userId);
                }
            }
        }
        
        include "views/front/test_request.php";
    }
    
    // User: View test request status
    public function status() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $testRequest = new TestRequest();
        $request = $testRequest->getByUserId($userId);
        
        // Check if user has completed attempts to show "create new request" option
        require_once "models/TestAttempt.php";
        $testAttempt = new TestAttempt();
        $completedAttempts = $testAttempt->getAll('completed');
        $userHasCompletedTest = false;
        
        foreach ($completedAttempts as $attempt) {
            if ($attempt['user_id'] == $userId) {
                $userHasCompletedTest = true;
                break;
            }
        }
        
        // Also check by getting user's attempt directly
        $userAttempt = $testAttempt->getByUserId($userId);
        if ($userAttempt && in_array($userAttempt['status'], ['completed', 'expired'])) {
            $userHasCompletedTest = true;
        }
        
        include "views/front/test_status.php";
    }
    
    // User: Take the test
    public function takeTest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $testRequest = new TestRequest();
        $request = $testRequest->getByUserId($userId);
        
        // Check if user has approved request
        if (!$request || $request['status'] !== 'approved') {
            $_SESSION['error_message'] = "Vous devez d'abord faire une demande et être approuvé pour passer le test.";
            header("Location: ?controller=test&action=requestAccess");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        
        // Check if user already has an in-progress attempt
        $inProgressAttempt = $testAttempt->getInProgressByUserId($userId);
        
        if ($inProgressAttempt) {
            // Check if attempt is expired
            $remainingTime = $testAttempt->getRemainingTime($inProgressAttempt['id']);
            if ($remainingTime <= 0) {
                // Expire the attempt
                $testAttempt->expireOldAttempts();
                $_SESSION['error_message'] = "Votre tentative précédente a expiré. Vous devez recommencer.";
                header("Location: ?controller=test&action=takeTest");
                exit;
            }
            // Redirect to continue existing attempt
            header("Location: ?controller=test&action=testPage&attempt_id=" . $inProgressAttempt['id']);
            exit;
        }
        
        // Start new test attempt
        $attemptId = $testAttempt->create($userId, $request['id'], 1800); // 30 minutes
        
        if ($attemptId) {
            header("Location: ?controller=test&action=testPage&attempt_id=" . $attemptId);
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la création de la tentative de test.";
            header("Location: ?controller=test&action=status");
            exit;
        }
    }
    
    // User: Test page with questions
    public function testPage() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $attemptId = $_GET['attempt_id'] ?? null;
        
        if (!$attemptId) {
            $_SESSION['error_message'] = "Tentative de test invalide.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt || $attempt['user_id'] != $userId) {
            $_SESSION['error_message'] = "Tentative de test non trouvée.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        if ($attempt['status'] !== 'in_progress') {
            $_SESSION['error_message'] = "Cette tentative de test est déjà terminée.";
            header("Location: ?controller=test&action=results&attempt_id=" . $attemptId);
            exit;
        }
        
        // Check remaining time
        $remainingTime = $testAttempt->getRemainingTime($attemptId);
        if ($remainingTime <= 0) {
            $testAttempt->expireOldAttempts();
            $_SESSION['error_message'] = "Le temps est écoulé.";
            header("Location: ?controller=test&action=results&attempt_id=" . $attemptId);
            exit;
        }
        
        // Get questions (10 random questions)
        $testQuestion = new TestQuestion();
        $questions = $testQuestion->getRandomQuestions(10);
        
        // Get saved answers if any
        $testAnswer = new TestAnswer();
        $savedAnswers = [];
        if ($attempt['status'] === 'in_progress') {
            $answers = $testAnswer->getAnswersByAttempt($attemptId);
            foreach ($answers as $answer) {
                $savedAnswers[$answer['question_id']] = $answer['user_answer'];
            }
        }
        
        // Pass variables to view
        $attemptId = $attempt['id'];
        
        include "views/front/test_page.php";
    }
    
    // User: Save answer during test
    public function saveAnswer() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non autorisé']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $attemptId = $_POST['attempt_id'] ?? null;
        $questionId = $_POST['question_id'] ?? null;
        $userAnswer = $_POST['answer'] ?? null;
        
        if (!$attemptId || !$questionId || !$userAnswer) {
            echo json_encode(['success' => false, 'error' => 'Données manquantes']);
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt || $attempt['user_id'] != $userId || $attempt['status'] !== 'in_progress') {
            echo json_encode(['success' => false, 'error' => 'Tentative invalide']);
            exit;
        }
        
        // Get correct answer
        $testQuestion = new TestQuestion();
        $question = $testQuestion->getById($questionId);
        
        if (!$question) {
            echo json_encode(['success' => false, 'error' => 'Question non trouvée']);
            exit;
        }
        
        $isCorrect = ($userAnswer === $question['correct_answer']);
        
        $testAnswer = new TestAnswer();
        if ($testAnswer->saveAnswer($attemptId, $questionId, $userAnswer, $isCorrect)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
        }
        exit;
    }
    
    // User: Submit test
    public function submitTest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $attemptId = $_POST['attempt_id'] ?? null;
        
        if (!$attemptId) {
            $_SESSION['error_message'] = "Tentative de test invalide.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt || $attempt['user_id'] != $userId || $attempt['status'] !== 'in_progress') {
            $_SESSION['error_message'] = "Tentative de test invalide.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Get all answers from form submission or from saved answers
        $testAnswer = new TestAnswer();
        $submittedAnswers = $_POST['answers'] ?? [];
        
        // If answers came from form, save them first
        if (!empty($submittedAnswers)) {
            foreach ($submittedAnswers as $questionId => $userAnswer) {
                // Get question to check correct answer
                $testQuestion = new TestQuestion();
                $question = $testQuestion->getById($questionId);
                if ($question) {
                    $isCorrect = ($userAnswer === $question['correct_answer']);
                    $testAnswer->saveAnswer($attemptId, $questionId, $userAnswer, $isCorrect);
                }
            }
        }
        
        // Get all saved answers to calculate score
        $answers = $testAnswer->getAnswersByAttempt($attemptId);
        $totalQuestions = count($answers);
        $correctAnswers = 0;
        
        foreach ($answers as $answer) {
            if ($answer['is_correct']) {
                $correctAnswers++;
            }
        }
        
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $score = round($score, 2);
        
        // Calculate time taken
        $timeTaken = time() - strtotime($attempt['started_at']);
        
        // Submit the attempt
        $testAttempt->submit($attemptId, $score, $totalQuestions, $correctAnswers, $timeTaken);
        
        // Create approval request for admin review
        require_once "models/TestApproval.php";
        $testApproval = new TestApproval();
        
        // Check if approval already exists
        $existingApproval = $testApproval->getByAttemptId($attemptId);
        if (!$existingApproval) {
            // Find an admin user ID from database
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            $adminQuery = "SELECT id FROM users WHERE role = 'admin' AND status = 'active' LIMIT 1";
            $adminStmt = $conn->prepare($adminQuery);
            $adminStmt->execute();
            $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && isset($admin['id'])) {
                $adminId = (int)$admin['id'];
                $testApproval->create($attemptId, $adminId);
            } else {
                // Log error but don't fail - admin can create approval manually when reviewing
                error_log("Warning: No active admin user found. Test approval not created automatically for attempt ID: " . $attemptId);
            }
        }
        
        $_SESSION['success_message'] = "Test soumis avec succès! En attente de validation par l'administrateur.";
        header("Location: ?controller=test&action=results&attempt_id=" . $attemptId);
        exit;
    }
    
    // User: View test results
    public function results() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $attemptId = $_GET['attempt_id'] ?? null;
        
        if (!$attemptId) {
            $_SESSION['error_message'] = "Tentative de test invalide.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt || $attempt['user_id'] != $userId) {
            $_SESSION['error_message'] = "Tentative de test non trouvée.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        $testAnswer = new TestAnswer();
        // getAnswersByAttempt already joins with test_questions table
        $answers = $testAnswer->getAnswersByAttempt($attemptId);
        
        require_once "models/TestApproval.php";
        $testApproval = new TestApproval();
        $approval = $testApproval->getByAttemptId($attemptId);
        
        // Pass userId for medal check
        $userId = $_SESSION['user_id'];
        
        include "views/front/test_results.php";
    }

    // User: Show medal notification (full screen animation)
    public function showMedalNotification() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $medal = $_GET['medal'] ?? 'none';
        error_log("🏆 showMedalNotification called - Medal: " . $medal . ", User ID: " . ($_SESSION['user_id'] ?? 'none'));
        
        if ($medal === 'none' || !in_array($medal, ['bronze', 'silver', 'gold'])) {
            error_log("🏆 Invalid medal type or missing medal parameter");
            header("Location: ?controller=formation&action=userDashboard");
            exit;
        }
        
        // Verify user actually has this medal
        require_once "config/database.php";
        $database = new Database();
        $conn = $database->getConnection();
        try {
            $checkQuery = "SELECT medal FROM users WHERE id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$_SESSION['user_id']]);
            $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userData || ($userData['medal'] ?? 'none') !== $medal) {
                error_log("🏆 User medal mismatch - Expected: " . $medal . ", Got: " . ($userData['medal'] ?? 'none'));
                header("Location: ?controller=formation&action=userDashboard");
                exit;
            }
        } catch (PDOException $e) {
            error_log("🏆 Error verifying medal: " . $e->getMessage());
            // Continue anyway to show notification
        }

        $medalIcons = [
            'bronze' => '🥉',
            'silver' => '🥈',
            'gold' => '🥇'
        ];

        $medalNames = [
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold'
        ];

        $medalIcon = $medalIcons[$medal] ?? '🏅';
        $medalName = $medalNames[$medal] ?? 'Médaille';

        include "views/front/medal_notification.php";
    }

    // User: Mark medal notification as seen
    public function markMedalNotificationSeen() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        require_once "config/database.php";
        $database = new Database();
        $conn = $database->getConnection();

        try {
            // Check if column exists
            $checkQuery = "SHOW COLUMNS FROM users LIKE 'medal_notification_seen'";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute();
            $hasColumn = $checkStmt->rowCount() > 0;

            if ($hasColumn) {
                $updateQuery = "UPDATE users SET medal_notification_seen = 1 WHERE id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->execute([$_SESSION['user_id']]);
                echo json_encode(['success' => true]);
            } else {
                // Column doesn't exist, but that's okay - just return success
                echo json_encode(['success' => true, 'note' => 'Column does not exist yet']);
            }
        } catch (PDOException $e) {
            error_log("Error marking medal notification as seen: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    // Debug: Check medal value
    public function debugMedal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        include "debug_medal_check.php";
        exit;
    }
}
?>

