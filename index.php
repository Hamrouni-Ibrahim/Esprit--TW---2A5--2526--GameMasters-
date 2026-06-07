<?php
// Start session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection for auth/games
require_once "config/database.php";

// Load reCAPTCHA config if it exists
if (file_exists("config/recaptcha.php")) {
    require_once "config/recaptcha.php";
}

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Load AuthController for authentication checks
require_once "models/User.php";
require_once "controllers/AuthController.php";
$authController = new AuthController($db);

// Check if this is an auth or game route (using 'action' parameter)
$action = $_GET['action'] ?? null;

// Define public routes that don't require authentication
$publicRoutes = ['login', 'register', 'forgot_password', 'reset_password', 'verify_email', 'verify_email_page', 'resend_verification', 'games', 'search_games', 'game_details'];

// Define routes that require authentication (actions, not just viewing)
// Note: face_login and verify_face are public (for login), not protected
$protectedActions = ['add_game', 'my_games', 'rate_game', 'profile', 'complete_profile', 'edit_profile', 'save_face', 'remove_face', 'toggle_face', 'face_info', 'face_registration', 'face_choice'];

// Check authentication for protected actions
if (in_array($action, $protectedActions) && !AuthController::isLoggedIn()) {
    // Redirect to login if trying to perform protected action without authentication
    header("Location: ?action=login");
    exit;
}

// Handle authentication and games routes
if ($action && in_array($action, ['login', 'register', 'logout', 'forgot_password', 'reset_password', 'verify_email', 'verify_email_page', 'resend_verification', 'games', 'game_details', 'add_game', 'my_games', 'rate_game', 'search_games', 'profile', 'complete_profile', 'edit_profile', 'save_face', 'verify_face', 'remove_face', 'toggle_face', 'face_info', 'face_registration', 'face_login', 'face_choice', 'donation', 'donation_add', 'donation_update', 'donation_delete', 'projects', 'project_details', 'generate_project_summary', 'events', 'event_participate', 'download_ticket', 'my_participations', 'cancel_participation', 'reclamation_create', 'mes_reclamations', 'reclamation_edit', 'reclamation_update', 'reclamation_delete', 'admin_dashboard', 'admin_games', 'admin_edit_game', 'admin_users', 'admin_user_edit', 'admin_user_delete', 'admin_user_ban', 'admin_user_unban', 'admin_user_create', 'admin_game_approve', 'admin_game_reject', 'admin_game_delete', 'admin_game_categories', 'admin_search_games', 'admin_export_users_pdf', 'admin_export_games_pdf', 'admin_get_game_ratings', 'admin_user_update_medal', 'admin_donations', 'admin_donation_delete', 'admin_projects', 'admin_project_add', 'admin_project_edit', 'admin_project_delete', 'admin_events', 'admin_event_add', 'admin_event_edit', 'admin_event_delete', 'admin_participations', 'admin_participation_add', 'admin_participation_edit', 'admin_participation_delete', 'admin_reclamations', 'admin_reclamation_respond', 'admin_reclamation_edit', 'admin_reclamation_delete', 'get_user_data'])) {
    // Load auth/games controllers (AuthController already loaded above)
    require_once "models/Game.php";
    require_once "models/Profile.php";
    require_once "controllers/GameController.php";
    
    $gameController = new GameController($db);
    
    // Route to appropriate handler
    switch($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    if ($authController->login($_POST['email'], $_POST['password'])) {
                        // Wait a moment for session to be fully set
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        
                        // Check if user needs to see medal notification
                        if (isset($_SESSION['user_id'])) {
                            $userId = $_SESSION['user_id'];
                            error_log("🏆 Checking medal notification for user ID: " . $userId);
                            
                            require_once "config/database.php";
                            $database = new Database();
                            $conn = $database->getConnection();
                            
                            // First check if medal_notification_seen column exists
                            $columnCheck = "SHOW COLUMNS FROM users LIKE 'medal_notification_seen'";
                            $columnStmt = $conn->prepare($columnCheck);
                            $columnStmt->execute();
                            $hasColumn = $columnStmt->rowCount() > 0;
                            
                            error_log("🏆 Column medal_notification_seen exists: " . ($hasColumn ? 'YES' : 'NO'));
                            
                            if ($hasColumn) {
                                // Column exists, check for medal notification
                                try {
                                    $checkQuery = "SELECT medal, medal_notification_seen FROM users WHERE id = ?";
                                    $checkStmt = $conn->prepare($checkQuery);
                                    $checkStmt->execute([$userId]);
                                    $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($userData) {
                                        $medal = $userData['medal'] ?? 'none';
                                        $notificationSeen = $userData['medal_notification_seen'] ?? 1;
                                        
                                        error_log("🏆 User medal: " . $medal . ", notification_seen: " . $notificationSeen);
                                        
                                        // If user has a medal and hasn't seen the notification, show it
                                        if ($medal !== 'none' && $notificationSeen == 0) {
                                            error_log("🏆 Showing medal notification for user ID: " . $userId . " with medal: " . $medal);
                                            header("Location: ?controller=test&action=showMedalNotification&medal=" . urlencode($medal));
                                            exit;
                                        } else {
                                            error_log("🏆 No notification needed - medal: " . $medal . ", seen: " . $notificationSeen);
                                        }
                                    } else {
                                        error_log("🏆 No user data found for ID: " . $userId);
                                    }
                                } catch (PDOException $e) {
                                    // If there's an error (column doesn't exist or other issue), just skip notification
                                    error_log("🏆 Error checking medal notification: " . $e->getMessage());
                                }
                            } else {
                                // Column doesn't exist yet, just check medal without notification
                                try {
                                    $checkQuery = "SELECT medal FROM users WHERE id = ?";
                                    $checkStmt = $conn->prepare($checkQuery);
                                    $checkStmt->execute([$userId]);
                                    $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                                    // Just log that medal exists but notification system not ready
                                    if ($userData && ($userData['medal'] ?? 'none') !== 'none') {
                                        error_log("🏆 User has medal (" . ($userData['medal'] ?? 'none') . ") but medal_notification_seen column doesn't exist yet");
                                    }
                                } catch (PDOException $e) {
                                    error_log("🏆 Error checking medal: " . $e->getMessage());
                                }
                            }
                        } else {
                            error_log("🏆 No user_id in session after login");
                        }
                        
                        // All users (including admins) go to accueil (userDashboard) after login
                        // Admins will see "Open Administration Page" button in navbar to access backoffice
                        header("Location: ?controller=formation&action=userDashboard");
                        exit;
                    } else {
                        $error = "Email ou mot de passe incorrect";
                    }
                } catch(Exception $e) {
                    $error = $e->getMessage();
                }
            }
            // Include login view (you'll need to create this)
            if (file_exists("views/front/login.php")) {
                include "views/front/login.php";
            } else {
                echo "Login page not found. Please create views/front/login.php";
            }
            exit;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $authController->register($_POST);
                if ($result['success']) {
                    // Always redirect to verification page after successful registration
                    if (isset($result['requires_verification']) && $result['requires_verification']) {
                        // Store success message in session for verification page
                        if (isset($result['message'])) {
                            $_SESSION['success_message'] = $result['message'];
                        }
                        // In development, also store the code in session for display
                        $isDevelopment = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                                         strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
                        if ($isDevelopment && isset($result['verification_code'])) {
                            $_SESSION['dev_verification_code'] = $result['verification_code'];
                        }
                        header('Location: ?action=verify_email_page');
                        exit;
                    } else {
                        // If no verification required (shouldn't happen), redirect to login
                        header("Location: ?action=login");
                        exit;
                    }
                } else {
                    $errors = $result['errors'];
                }
            }
            // Include register view
            if (file_exists("views/front/register.php")) {
                include "views/front/register.php";
            } else {
                echo "Register page not found. Please create views/front/register.php";
            }
            exit;
            
        case 'logout':
            $authController->logout();
            // Redirect to accueil (home) page after logout
            header("Location: ?controller=formation&action=userDashboard");
            exit;
            
        case 'forgot_password':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $result = $authController->requestPasswordReset($email);
                if ($result['success']) {
                    $success = true;
                    $message = $result['message'];
                } else {
                    $error = $result['errors'][0] ?? 'Une erreur est survenue';
                }
            }
            if (file_exists("views/front/forgot_password.php")) {
                include "views/front/forgot_password.php";
            } else {
                echo "Forgot password page not found";
            }
            exit;

        case 'reset_password':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $code = $_POST['reset_code'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $result = $authController->resetPassword($email, $code, $password);
                
                if ($result['success']) {
                    // Redirect to login with success message
                    header("Location: ?action=login&message=" . urlencode($result['message']));
                    exit;
                } else {
                    $error = $result['errors'][0] ?? 'Une erreur est survenue';
                    // Pass parameters back to view to avoid re-entering
                    $_GET['email'] = $email;
                    $_GET['code'] = $code;
                }
            }
            
            if (file_exists("views/front/reset_password.php")) {
                include "views/front/reset_password.php";
            } else {
                echo "Reset password page not found";
            }
            exit;
            
        case 'games':
            // Games page will fetch its own data, but we can pass the controller for consistency
            // The games.php view creates its own GameController instance
            if (file_exists("views/front/games.php")) {
                include "views/front/games.php";
            } else {
                echo "Games page not found";
            }
            exit;
            
        case 'game_details':
            $id = $_GET['id'] ?? 0;
            if ($id > 0) {
                $game = $gameController->show($id);
                if ($game && file_exists("views/front/game_details.php")) {
                    include "views/front/game_details.php";
                } else {
                    header("Location: ?action=games");
                }
            } else {
                header("Location: ?action=games");
            }
            exit;
            
        case 'rate_game':
            $gameController->rate();
            exit;
            
        case 'add_game':
            // Check if user is admin
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            
            // Handle add game form
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // For admins, set userId to null (admin-created games)
                $userId = $isAdmin ? null : ($_SESSION['user_id'] ?? null);
                
                // Debug: Log POST data (remove in production)
                error_log("Add Game POST data: " . print_r($_POST, true));
                error_log("Add Game FILES data: " . print_r($_FILES, true));
                
                // For admins, games are automatically published
                // For regular users, games need approval
                if ($isAdmin) {
                    $_POST['status'] = 'published';
                    $_POST['approval_status'] = 'approved';
                } elseif (isset($_POST['approve']) && $_POST['approve'] === '1') {
                    $_POST['status'] = 'published';
                } elseif (!isset($_POST['status']) || empty($_POST['status'])) {
                    $_POST['status'] = 'development';
                }
                
                $result = $gameController->create($_POST, $_FILES, $userId);
                
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                    // Redirect admins to admin games page, regular users to frontend games page
                    if ($isAdmin) {
                        header("Location: ?action=admin_games");
                    } else {
                    header("Location: ?action=games");
                    }
                    exit;
                } else {
                    $errors = $result['errors'];
                }
            }
            
            // Load game categories for the form
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                error_log("Erreur chargement catégories jeux: " . $e->getMessage());
                $categories = [];
            }
            
            // Include add game view - use admin header/footer if admin, frontend if regular user
            if (file_exists("views/front/add_game.php")) {
                $pageTitle = 'Ajouter un Jeu - Game Master';
                $currentPage = 'games';
                
                if ($isAdmin) {
                    // Use admin layout for admins
                    include "views/admin/includes/header.php";
                    include "views/front/add_game.php";
                    include "views/admin/includes/footer.php";
                } else {
                    // Use frontend layout for regular users
                include "views/front/includes/header.php";
                include "views/front/add_game.php";
                include "views/front/includes/footer.php";
                }
            } else {
                echo "Add game page not found";
            }
            exit;
            
        case 'my_games':
            // Show user's games
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $userGames = $gameController->getUserGames($userId);
                if (file_exists("views/front/my_games.php")) {
                    $pageTitle = 'Mes Jeux - Game Master';
                    $currentPage = 'games';
                    include "views/front/includes/header.php";
                    // Pass variables to the view
                    $games = $userGames;
                    include "views/front/my_games.php";
                    include "views/front/includes/footer.php";
                } else {
                    echo "My games page not found";
                }
            } else {
                header("Location: ?action=login");
            }
            exit;
            
        case 'search_games':
            // Search games - handle both GET and POST
            require_once "controllers/CategoryController.php";
            $categoryController = new CategoryController();
            
            // Initialize variables
            $games = [];
            $selectedCategory = null;
            $searchTerm = null;
            $errors = [];
            $success = false;
            
            // Handle POST request (form submission)
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_games'])) {
                $categoryId = $_POST['category_id'] ?? '';
                $searchTerm = isset($_POST['search_term']) ? trim(strip_tags($_POST['search_term'])) : '';
                
                if (empty($categoryId) && empty($searchTerm)) {
                    $games = $gameController->searchGames();
                    $success = true;
                } else {
                    // Basic validation for category_id (must be numeric if provided)
                    if (!empty($categoryId) && !is_numeric($categoryId)) {
                        $errors[] = "ID de catégorie invalide";
                    }
                    
                    if (empty($errors)) {
                        $categoryId = !empty($categoryId) ? (int)$categoryId : null;
                        $games = $gameController->searchGames($searchTerm, $categoryId);
                        if ($categoryId) {
                            // Get category from game_categories table
                            try {
                                $catQuery = "SELECT id, name FROM game_categories WHERE id = ?";
                                $catStmt = $db->prepare($catQuery);
                                $catStmt->execute([$categoryId]);
                                $selectedCategory = $catStmt->fetch(PDO::FETCH_ASSOC);
                            } catch(Exception $e) {
                                error_log("Erreur récupération catégorie: " . $e->getMessage());
                                $selectedCategory = null;
                            }
                        }
                        $success = true;
                    }
                }
            } else {
                // Handle GET request (initial page load or search from URL)
                $searchTerm = $_GET['q'] ?? '';
                $categoryId = $_GET['category_id'] ?? null;
                if ($searchTerm || $categoryId) {
                    $games = $gameController->searchGames($searchTerm, $categoryId);
                    if ($categoryId) {
                        // Get category from game_categories table
                        try {
                            $catQuery = "SELECT id, name FROM game_categories WHERE id = ?";
                            $catStmt = $db->prepare($catQuery);
                            $catStmt->execute([$categoryId]);
                            $selectedCategory = $catStmt->fetch(PDO::FETCH_ASSOC);
                        } catch(Exception $e) {
                            error_log("Erreur récupération catégorie: " . $e->getMessage());
                            $selectedCategory = null;
                        }
                    }
                    $success = true;
                }
            }
            
            // Get game categories for the form (from game_categories table, not categories)
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                error_log("Erreur chargement catégories jeux (search_games): " . $e->getMessage());
                $categories = [];
            }
            
            if (file_exists("views/front/searchGames.php")) {
                $pageTitle = 'Recherche de Jeux - Game Master';
                $currentPage = 'search_games';
                include "views/front/includes/header.php";
                include "views/front/searchGames.php";
                include "views/front/includes/footer.php";
            } else {
                echo "Search games page not found";
            }
            exit;
            
        case 'verify_email_page':
            // Show email verification page
            if (file_exists("views/front/verify_email.php")) {
                include "views/front/verify_email.php";
            } else {
                echo "Verification page not found";
            }
            exit;
            
        case 'verify_email':
            // Handle email verification
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $code1 = $_POST['code1'] ?? '';
                $code2 = $_POST['code2'] ?? '';
                $code3 = $_POST['code3'] ?? '';
                $code4 = $_POST['code4'] ?? '';
                $code5 = $_POST['code5'] ?? '';
                $code6 = $_POST['code6'] ?? '';
                $code = $code1 . $code2 . $code3 . $code4 . $code5 . $code6;
                $userId = $_POST['user_id'] ?? ($_SESSION['pending_verification_user_id'] ?? null);
                
                if ($userId && strlen($code) === 6) {
                    $result = $authController->verifyEmail($userId, $code);
                    if ($result['success']) {
                        // Clean up development verification code from session
                        unset($_SESSION['dev_verification_code']);
                        
                        // Connecter l'utilisateur automatiquement après vérification
                        $_SESSION['user_id'] = $userId;
                        require_once "models/User.php";
                        $user = new User($db);
                        $user->id = $userId;
                        if ($user->readOne()) {
                            $_SESSION['username'] = $user->username;
                            $_SESSION['email'] = $user->email;
                            $_SESSION['role'] = $user->role;
                            $_SESSION['avatar'] = $user->avatar ?? null;
                        }
                        
                        // Rediriger vers le formulaire de profil après vérification email
                        header("Location: ?action=complete_profile");
                        exit;
                    } else {
                        $_SESSION['error_message'] = $result['errors'][0] ?? 'Code invalide';
                        header("Location: ?action=verify_email_page");
                        exit;
                    }
                } else {
                    $_SESSION['error_message'] = 'Code invalide';
                    header("Location: ?action=verify_email_page");
                    exit;
                }
            } else {
                header("Location: ?action=verify_email_page");
                exit;
            }
            
        case 'resend_verification':
            // Resend verification code
            $userId = $_SESSION['pending_verification_user_id'] ?? null;
            if ($userId) {
                $result = $authController->resendVerificationCode($userId);
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = $result['errors'][0] ?? 'Erreur lors de l\'envoi du code';
                }
            }
            header("Location: ?action=verify_email_page");
            exit;
            
        case 'complete_profile':
            // Profile completion form after email verification
            if (!isset($_SESSION['user_id'])) {
                header("Location: ?action=login");
                exit;
            }
            
            // Check if profile is already completed
            if (isset($_SESSION['profile_completed']) && $_SESSION['profile_completed']) {
                header("Location: ?action=face_choice");
                exit;
            }
            
            // Load ProfileController
            if (file_exists("controllers/ProfileController.php")) {
                require_once "controllers/ProfileController.php";
                $profileController = new ProfileController($db);
                
                // Handle form submission
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $userId = $_SESSION['user_id'];
                    $result = $profileController->save($userId, $_POST);
                    
                    if ($result['success']) {
                        $_SESSION['profile_completed'] = true;
                        // Redirect to face recognition after profile completion
                        header("Location: ?action=face_choice");
                        exit;
                    } else {
                        $errors = $result['errors'] ?? [];
                    }
                }
                
                // Get existing profile if any
                $userId = $_SESSION['user_id'];
                require_once "models/Profile.php";
                $profileModel = new Profile($db);
                $profileModel->user_id = $userId;
                $profile = null;
                if ($profileModel->readByUserId()) {
                    $profile = [
                        'first_name' => $profileModel->first_name,
                        'last_name' => $profileModel->last_name,
                        'discord' => $profileModel->discord,
                        'country' => $profileModel->country,
                        'nationality' => $profileModel->nationality,
                        'gender' => $profileModel->gender,
                        'birth_date' => $profileModel->birth_date,
                        'career_level' => $profileModel->career_level,
                        'expertise' => $profileModel->expertise,
                        'tech_stack' => $profileModel->tech_stack,
                        'timezone' => $profileModel->timezone
                    ];
                }
                
                // Display the profile form
                $pageTitle = 'Complétez votre profil - Game Master';
                $currentPage = 'profile';
                include "views/front/includes/header.php";
                include "views/front/profile.php";
                include "views/front/includes/footer.php";
            } else {
                echo "Profile controller not found";
            }
            exit;
            
        case 'profile':
            // Load ProfileController if it exists
            if (file_exists("controllers/ProfileController.php")) {
                require_once "controllers/ProfileController.php";
                $profileController = new ProfileController($db);
                if (method_exists($profileController, 'show')) {
                    $profileController->show();
                } else {
                    // Fallback: include profile view directly
                    if (file_exists("views/front/profile.php")) {
                        $pageTitle = 'Mon Profil - Game Master';
                        $currentPage = 'profile';
                        include "views/front/includes/header.php";
                        include "views/front/profile.php";
                        include "views/front/includes/footer.php";
                    } else {
                        echo "Profile page not found";
                    }
                }
            } else {
                // Fallback: include profile view directly
                if (file_exists("views/front/profile.php")) {
                    $pageTitle = 'Mon Profil - Game Master';
                    $currentPage = 'profile';
                    include "views/front/includes/header.php";
                    include "views/front/profile.php";
                    include "views/front/includes/footer.php";
                } else {
                    echo "Profile page not found";
                }
            }
            exit;
            
        case 'edit_profile':
            // Load ProfileController if it exists
            if (file_exists("controllers/ProfileController.php")) {
                require_once "controllers/ProfileController.php";
                $profileController = new ProfileController($db);
                if (method_exists($profileController, 'edit')) {
                    $profileController->edit();
                } else {
                    // Fallback: include edit profile view directly
                    if (file_exists("views/front/edit_profile.php")) {
                        $pageTitle = 'Modifier mon Profil - Game Master';
                        $currentPage = 'profile';
                        include "views/front/includes/header.php";
                        include "views/front/edit_profile.php";
                        include "views/front/includes/footer.php";
                    } else {
                        echo "Edit profile page not found";
                    }
                }
            } else {
                // Fallback: include edit profile view directly
                if (file_exists("views/front/edit_profile.php")) {
                    $pageTitle = 'Modifier mon Profil - Game Master';
                    $currentPage = 'profile';
                    include "views/front/includes/header.php";
                    include "views/front/edit_profile.php";
                    include "views/front/includes/footer.php";
                } else {
                    echo "Edit profile page not found";
                }
            }
            exit;
            
        // Face Authentication Routes
        case 'save_face':
        case 'verify_face':
        case 'remove_face':
        case 'toggle_face':
        case 'face_info':
            header('Content-Type: application/json');
            require_once "controllers/FaceAuthController.php";
            $faceAuthController = new FaceAuthController($db);
            
            if ($action === 'save_face') {
                $faceAuthController->registerFace();
            } elseif ($action === 'verify_face') {
                $faceAuthController->verifyFace();
            } elseif ($action === 'remove_face') {
                $faceAuthController->removeFace();
            } elseif ($action === 'toggle_face') {
                $faceAuthController->toggleFaceAuth();
            } elseif ($action === 'face_info') {
                $faceAuthController->getFaceInfo();
            }
            exit;
            
        case 'face_choice':
            // Page de choix pour enregistrer le visage après vérification d'email
            if (!isset($_SESSION['user_id'])) {
                header("Location: ?action=login");
                exit;
            }
            if (file_exists("views/front/face_choice.php")) {
                include "views/front/face_choice.php";
            } else {
                // Si la page n'existe pas, rediriger vers le dashboard
                header("Location: ?controller=formation&action=userDashboard");
            }
            exit;

        case 'donation':
            // Load DonationController
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->index();
            exit;

        case 'donation_add':
            // Add donation
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->add();
            exit;

        case 'donation_update':
            // Update donation
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->update();
            exit;

        case 'donation_delete':
            // Delete donation
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->delete();
            exit;

        case 'projects':
            // Load ProjectController for front-end
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->index();
            exit;

        case 'project_details':
            // Load ProjectController for project details
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->details();
            exit;
            
        case 'generate_project_summary':
            // Generate AI summary for a project
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->generateAISummary();
            exit;

        case 'events':
            // Load EventController for front-end events list
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->index();
            exit;

        case 'event_participate':
            // Handle event participation
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->participate();
            exit;

        case 'download_ticket':
            // Download event participation ticket
            if (!isset($_GET['participation_id'])) {
                header("Location: ?action=events");
                exit;
            }
            require_once "controllers/TicketController.php";
            $ticketController = new TicketController($db);
            $ticketController->generateTicket($_GET['participation_id']);
            exit;

        case 'my_participations':
            // Load user participations
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->myParticipations();
            exit;

        case 'cancel_participation':
            // Cancel user participation
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->cancelParticipation();
            exit;

        // Reclamation Routes (User)
        case 'reclamation_create':
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->create();
            exit;

        case 'mes_reclamations':
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->mesReclamations();
            exit;

        case 'reclamation_edit':
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->edit();
            exit;

        case 'reclamation_update':
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->update();
            exit;

        case 'reclamation_delete':
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->delete();
            exit;
            
        case 'face_registration':
            require_once "controllers/FaceAuthController.php";
            $faceAuthController = new FaceAuthController($db);
            $faceAuthController->showRegistrationPage();
            exit;
            
        case 'face_login':
            require_once "controllers/FaceAuthController.php";
            $faceAuthController = new FaceAuthController($db);
            $faceAuthController->showFaceLoginPage();
            exit;
            
        // Admin Routes
        case 'admin_dashboard':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/UserController.php";
            require_once "controllers/GameController.php";
            require_once "models/Formation.php";
            require_once "models/Education.php";
            
            $userController = new UserController($db);
            $gameController = new GameController($db);
            
            // Get stats
            $userStats = $userController->getStats();
            $gameStats = $gameController->getStats();
            
            // Get formations/educations stats
            $formationModel = new Formation();
            $formationStmt = $formationModel->getAll();
            $formations = $formationStmt->fetchAll(PDO::FETCH_ASSOC);
            $formationStats = [
                'total' => count($formations),
                'by_difficulty' => []
            ];
            foreach ($formations as $f) {
                $diff = $f['difficulte'] ?? 'N/A';
                $formationStats['by_difficulty'][$diff] = ($formationStats['by_difficulty'][$diff] ?? 0) + 1;
            }
            
            $educationModel = new Education();
            $educationStmt = $educationModel->getAll();
            $educations = $educationStmt->fetchAll(PDO::FETCH_ASSOC);
            $educationStats = [
                'total' => count($educations),
                'by_difficulty' => []
            ];
            foreach ($educations as $e) {
                $diff = $e['difficulte'] ?? 'N/A';
                $educationStats['by_difficulty'][$diff] = ($educationStats['by_difficulty'][$diff] ?? 0) + 1;
            }
            
            // Helper function for time ago
            if (!function_exists('timeAgo')) {
                function timeAgo($datetime) {
                    if (empty($datetime)) return 'récemment';
                    $time = time() - strtotime($datetime);
                    if ($time < 60) return 'il y a ' . $time . ' seconde' . ($time > 1 ? 's' : '');
                    $time = floor($time / 60);
                    if ($time < 60) return 'il y a ' . $time . ' minute' . ($time > 1 ? 's' : '');
                    $time = floor($time / 60);
                    if ($time < 24) return 'il y a ' . $time . ' heure' . ($time > 1 ? 's' : '');
                    $time = floor($time / 24);
                    if ($time < 7) return 'il y a ' . $time . ' jour' . ($time > 1 ? 's' : '');
                    $time = floor($time / 7);
                    if ($time < 4) return 'il y a ' . $time . ' semaine' . ($time > 1 ? 's' : '');
                    $time = floor($time / 4);
                    return 'il y a ' . $time . ' mois';
                }
            }
            
            // Get recent activities
            $recentActivities = [];
            
            // Recent users (last 5)
            $recentUsersQuery = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5";
            $recentUsersStmt = $db->prepare($recentUsersQuery);
            $recentUsersStmt->execute();
            $recentUsers = $recentUsersStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($recentUsers as $user) {
                $recentActivities[] = [
                    'type' => 'user',
                    'icon' => '👤',
                    'color' => 'rgba(0, 209, 255, 0.2)',
                    'title' => 'Nouvel utilisateur inscrit',
                    'description' => htmlspecialchars($user['username']) . ' s\'est inscrit ' . timeAgo($user['created_at']),
                    'time' => $user['created_at']
                ];
            }
            
            // Recent games (last 5)
            $recentGamesQuery = "SELECT id, name, created_at, approval_status FROM games ORDER BY created_at DESC LIMIT 5";
            $recentGamesStmt = $db->prepare($recentGamesQuery);
            $recentGamesStmt->execute();
            $recentGames = $recentGamesStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($recentGames as $game) {
                $statusText = ($game['approval_status'] === 'approved') ? 'publié' : 'soumis';
                $recentActivities[] = [
                    'type' => 'game',
                    'icon' => '🎮',
                    'color' => 'rgba(153, 69, 255, 0.2)',
                    'title' => 'Nouveau jeu ' . $statusText,
                    'description' => '"' . htmlspecialchars($game['name']) . '" a été ' . $statusText . ' ' . timeAgo($game['created_at']),
                    'time' => $game['created_at']
                ];
            }
            
            // Recent formations (last 5)
            $recentFormationsQuery = "SELECT id, title, created_at FROM formations ORDER BY created_at DESC LIMIT 5";
            $recentFormationsStmt = $db->prepare($recentFormationsQuery);
            $recentFormationsStmt->execute();
            $recentFormations = $recentFormationsStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($recentFormations as $formation) {
                $recentActivities[] = [
                    'type' => 'formation',
                    'icon' => '📚',
                    'color' => 'rgba(0, 255, 136, 0.2)',
                    'title' => 'Nouvelle formation créée',
                    'description' => '"' . htmlspecialchars($formation['title']) . '" a été créée ' . timeAgo($formation['created_at']),
                    'time' => $formation['created_at']
                ];
            }
            
            // Sort by time (most recent first) and limit to 10
            usort($recentActivities, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });
            $recentActivities = array_slice($recentActivities, 0, 10);
            
            if (file_exists("views/admin/dashboard.php")) {
                $pageTitle = 'Tableau de Bord Admin - Game Master';
                $currentPage = 'admin';
                include "views/admin/includes/header.php";
                include "views/admin/dashboard.php";
                include "views/admin/includes/footer.php";
            } else {
                echo "Admin dashboard not found";
            }
            exit;
            
        case 'admin_search_games':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Search games for admin - handle both GET and POST
            require_once "controllers/CategoryController.php";
            $categoryController = new CategoryController();
            
            // Initialize variables
            $games = [];
            $selectedCategory = null;
            $searchTerm = null;
            $errors = [];
            $success = false;
            
            // Handle POST request (form submission)
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_games'])) {
                $categoryId = $_POST['category_id'] ?? '';
                $searchTerm = isset($_POST['search_term']) ? trim(strip_tags($_POST['search_term'])) : '';
                $rating = $_POST['rating'] ?? '';
                
                if (empty($categoryId) && empty($searchTerm) && empty($rating)) {
                    $games = $gameController->index(); // Get all games for admin
                    $success = true;
                } else {
                    // Basic validation for category_id (must be numeric if provided)
                    if (!empty($categoryId) && !is_numeric($categoryId)) {
                        $errors[] = "ID de catégorie invalide";
                    }
                    
                    // Basic validation for rating (must be numeric between 1 and 5 if provided)
                    if (!empty($rating) && (!is_numeric($rating) || $rating < 1 || $rating > 5)) {
                        $errors[] = "Note invalide (doit être entre 1 et 5)";
                    }
                    
                    if (empty($errors)) {
                        $categoryId = !empty($categoryId) ? (int)$categoryId : null;
                        $rating = !empty($rating) ? (int)$rating : null;
                        $games = $gameController->searchGames($searchTerm, $categoryId, $rating);
                        if ($categoryId) {
                            // Get category from game_categories table
                            try {
                                $catQuery = "SELECT id, name FROM game_categories WHERE id = ?";
                                $catStmt = $db->prepare($catQuery);
                                $catStmt->execute([$categoryId]);
                                $selectedCategory = $catStmt->fetch(PDO::FETCH_ASSOC);
                            } catch(Exception $e) {
                                error_log("Erreur récupération catégorie: " . $e->getMessage());
                                $selectedCategory = null;
                            }
                        }
                        $success = true;
                    }
                }
            } else {
                // Handle GET request (initial page load or search from URL)
                $searchTerm = $_GET['q'] ?? $_GET['search_term'] ?? '';
                $categoryId = $_GET['category_id'] ?? null;
                if ($searchTerm || $categoryId) {
                    $games = $gameController->searchGames($searchTerm, $categoryId);
                    if ($categoryId) {
                        // Get category from game_categories table
                        try {
                            $catQuery = "SELECT id, name FROM game_categories WHERE id = ?";
                            $catStmt = $db->prepare($catQuery);
                            $catStmt->execute([$categoryId]);
                            $selectedCategory = $catStmt->fetch(PDO::FETCH_ASSOC);
                        } catch(Exception $e) {
                            error_log("Erreur récupération catégorie: " . $e->getMessage());
                            $selectedCategory = null;
                        }
                    }
                    $success = true;
                }
            }
            
            // Get game categories for the form (from game_categories table, not categories)
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                error_log("Erreur chargement catégories jeux (admin_search_games): " . $e->getMessage());
                $categories = [];
            }
            
            if (file_exists("views/admin/searchGames.php")) {
                $pageTitle = 'Recherche de Jeux - Admin';
                $currentPage = 'admin_search_games';
                include "views/admin/includes/header.php";
                include "views/admin/searchGames.php";
                include "views/admin/includes/footer.php";
            } else {
                echo "Admin search games page not found";
            }
            exit;
            
        case 'admin_games':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Gérer les actions sur les jeux AVANT de charger les données
            if (isset($_GET['approve_game']) && isset($_GET['id'])) {
                require_once "controllers/GameController.php";
                $gameController = new GameController($db);
                $result = $gameController->approveGame((int)$_GET['id']);
                header("Location: ?action=admin_games&message=" . urlencode($result['message'] ?? 'Jeu approuvé'));
                exit;
            }
            
            if (isset($_GET['reject_game']) && isset($_GET['id'])) {
                require_once "controllers/GameController.php";
                $gameController = new GameController($db);
                $result = $gameController->rejectGame((int)$_GET['id']);
                header("Location: ?action=admin_games&message=" . urlencode($result['message'] ?? 'Jeu rejeté'));
                exit;
            }
            
            if (isset($_GET['delete_game']) && isset($_GET['id'])) {
                require_once "controllers/GameController.php";
                $gameController = new GameController($db);
                $result = $gameController->delete((int)$_GET['id']);
                header("Location: ?action=admin_games&message=" . urlencode($result['message'] ?? 'Jeu supprimé'));
                exit;
            }
            
            if (isset($_GET['publish_game']) && isset($_GET['id'])) {
                $gameId = (int)$_GET['id'];
                try {
                    // Récupérer l'ID de l'utilisateur avant la mise à jour
                    $getUserQuery = "SELECT user_id FROM games WHERE id = ?";
                    $getUserStmt = $db->prepare($getUserQuery);
                    $getUserStmt->execute([$gameId]);
                    $game = $getUserStmt->fetch(PDO::FETCH_ASSOC);
                    $userId = $game['user_id'] ?? null;
                    
                    $query = "UPDATE games SET status = 'published', approval_status = 'approved' WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$gameId]);
                    
                    // Mettre à jour la médaille de l'utilisateur
                    if ($userId) {
                        require_once "controllers/GameController.php";
                        $gameController = new GameController($db);
                        $gameController->updateUserMedal($userId);
                    }
                    
                    header("Location: ?action=admin_games&message=" . urlencode('Jeu publié avec succès'));
                } catch(Exception $e) {
                    header("Location: ?action=admin_games&message=" . urlencode('Erreur lors de la publication'));
                }
                exit;
            }
            
            
            // Initialiser les variables avec des valeurs par défaut
            $games = [];
            $pendingGames = [];
            $gameStats = ['total' => 0, 'published' => 0, 'pending' => 0];
            $categories = [];
            $message = null;
            $success = true;
            
            // Charger les données de manière sécurisée
            require_once "controllers/GameController.php";
            $gameController = new GameController($db);
            
            // Charger les jeux - avec timeout de sécurité
            set_time_limit(10);
            try {
                // Si une recherche (nom ou catégorie) est effectuée, utiliser searchGames
                $searchTerm = isset($_GET['search_term']) ? trim($_GET['search_term']) : null;
                $categoryId = isset($_GET['category_id']) && !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
                
                if ($searchTerm || $categoryId) {
                    $games = $gameController->searchGames($searchTerm, $categoryId);
                } else {
                $games = $gameController->index();
                }
                if (!is_array($games)) {
                    $games = [];
                }
            } catch(Exception $e) {
                error_log("Erreur index(): " . $e->getMessage());
                $games = [];
            }
            
            // Charger les jeux en attente (filtrer par catégorie et/ou nom si sélectionnés)
            try {
                $pendingGames = $gameController->getPendingGames();
                if (!is_array($pendingGames)) {
                    $pendingGames = [];
                }
                // Filtrer par catégorie si une catégorie est sélectionnée
                $categoryId = isset($_GET['category_id']) && !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
                $searchTerm = isset($_GET['search_term']) ? trim(strtolower($_GET['search_term'])) : null;
                
                if ($categoryId || $searchTerm) {
                    $pendingGames = array_filter($pendingGames, function($game) use ($categoryId, $searchTerm) {
                        $matchCategory = true;
                        $matchSearch = true;
                        
                        if ($categoryId) {
                            $matchCategory = isset($game['category_id']) && (int)$game['category_id'] === $categoryId;
                        }
                        
                        if ($searchTerm) {
                            $gameName = isset($game['name']) ? strtolower($game['name']) : '';
                            $gameDescription = isset($game['description']) ? strtolower($game['description']) : '';
                            $matchSearch = strpos($gameName, $searchTerm) !== false || strpos($gameDescription, $searchTerm) !== false;
                        }
                        
                        return $matchCategory && $matchSearch;
                    });
                    $pendingGames = array_values($pendingGames); // Réindexer le tableau
                }
            } catch(Exception $e) {
                error_log("Erreur getPendingGames(): " . $e->getMessage());
                $pendingGames = [];
            }
            
            // Charger les statistiques
            try {
                $gameStats = $gameController->getStats();
                if (!is_array($gameStats)) {
                    $gameStats = ['total' => 0, 'published' => 0, 'pending' => 0];
                }
            } catch(Exception $e) {
                error_log("Erreur getStats(): " . $e->getMessage());
                $gameStats = ['total' => 0, 'published' => 0, 'pending' => 0];
            }
            
            // Charger les catégories
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $categories = [];
            }
            
            // Gérer les messages
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                $success = strpos(strtolower($message), 'erreur') === false;
            }
            
            $pageTitle = 'Gestion des Jeux - Game Master';
            $currentPage = 'games';
            
            // Désactiver le buffer pour voir les erreurs
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            include "views/admin/includes/header.php";
            include "views/admin/games.php";
            include "views/admin/includes/footer.php";
            exit;
            
        case 'admin_edit_game':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/GameController.php";
            $gameController = new GameController($db);
            
            // Gérer la soumission du formulaire (POST)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_game'])) {
                $gameId = (int)$_POST['game_id'];
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'impact_social' => $_POST['impact_social'] ?? '',
                    'status' => $_POST['status'] ?? 'development',
                    'category_id' => $_POST['category_id'] ?? null,
                    'image_url' => $_POST['image_url'] ?? '',
                    'demo_url' => $_POST['demo_url'] ?? ''
                ];
                
                $result = $gameController->update($gameId, $data, $_FILES);
                
                if ($result['success']) {
                    header("Location: ?action=admin_games&message=" . urlencode($result['message'] ?? 'Jeu mis à jour avec succès'));
                } else {
                    $errors = $result['errors'] ?? ['Erreur lors de la mise à jour'];
                    $errorMessage = implode(', ', $errors);
                    header("Location: ?action=admin_edit_game&id=" . $gameId . "&error=" . urlencode($errorMessage));
                }
                exit;
            }
            
            // Afficher le formulaire d'édition (GET)
            $gameId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($gameId <= 0) {
                header("Location: ?action=admin_games&message=" . urlencode('ID de jeu invalide'));
                exit;
            }
            
            $game = $gameController->show($gameId);
            
            if (!$game) {
                header("Location: ?action=admin_games&message=" . urlencode('Jeu non trouvé'));
                exit;
            }
            
            // Charger les catégories
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $categories = [];
            }
            
            $pageTitle = 'Modifier le Jeu - Admin';
            $currentPage = 'games';
            $error = isset($_GET['error']) ? $_GET['error'] : null;
            
            include "views/admin/includes/header.php";
            include "views/admin/edit_game.php";
            include "views/admin/includes/footer.php";
            exit;
            
        case 'admin_export_users_pdf':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/UserController.php";
            $userController = new UserController($db);
            
            // Charger l'autoloader si ce n'est pas déjà fait
            $autoloadPaths = [
                __DIR__ . '/vendor/autoload.php',
                __DIR__ . '/../vendor/autoload.php'
            ];
            
            $autoloadLoaded = false;
            foreach ($autoloadPaths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $autoloadLoaded = true;
                    break;
                }
            }
            
            // Charger TCPDF directement si l'autoloader ne le charge pas
            if (!class_exists('TCPDF')) {
                $tcpdfPaths = [
                    __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php',
                    __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php'
                ];
                
                foreach ($tcpdfPaths as $tcpdfPath) {
                    if (file_exists($tcpdfPath)) {
                        require_once $tcpdfPath;
                        break;
                    }
                }
            }
            
            try {
                $users = $userController->getUsersWithProfiles();
            } catch (Exception $e) {
                error_log("Erreur getUsersWithProfiles: " . $e->getMessage());
                $users = [];
            }
            
            // Nettoyer le buffer de sortie pour éviter l'erreur TCPDF
            if (ob_get_length()) ob_end_clean();
            
            // Vérifier si TCPDF est disponible
            if (!class_exists('TCPDF')) {
                die("Erreur: TCPDF n'est pas installé. Veuillez installer la bibliothèque TCPDF via Composer.");
            }
            
            // Définir les constantes TCPDF si elles n'existent pas
            if (!defined('PDF_PAGE_ORIENTATION')) {
                define('PDF_PAGE_ORIENTATION', 'P'); // Portrait
            }
            if (!defined('PDF_UNIT')) {
                define('PDF_UNIT', 'mm');
            }
            if (!defined('PDF_PAGE_FORMAT')) {
                define('PDF_PAGE_FORMAT', 'A4');
            }
            if (!defined('PDF_FONT_NAME_MAIN')) {
                define('PDF_FONT_NAME_MAIN', 'helvetica');
            }
            if (!defined('PDF_FONT_SIZE_MAIN')) {
                define('PDF_FONT_SIZE_MAIN', 10);
            }
            if (!defined('PDF_FONT_NAME_DATA')) {
                define('PDF_FONT_NAME_DATA', 'helvetica');
            }
            if (!defined('PDF_FONT_SIZE_DATA')) {
                define('PDF_FONT_SIZE_DATA', 8);
            }
            if (!defined('PDF_FONT_MONOSPACED')) {
                define('PDF_FONT_MONOSPACED', 'courier');
            }
            if (!defined('PDF_MARGIN_LEFT')) {
                define('PDF_MARGIN_LEFT', 15);
            }
            if (!defined('PDF_MARGIN_TOP')) {
                define('PDF_MARGIN_TOP', 27);
            }
            if (!defined('PDF_MARGIN_RIGHT')) {
                define('PDF_MARGIN_RIGHT', 15);
            }
            if (!defined('PDF_MARGIN_HEADER')) {
                define('PDF_MARGIN_HEADER', 5);
            }
            if (!defined('PDF_MARGIN_FOOTER')) {
                define('PDF_MARGIN_FOOTER', 10);
            }
            if (!defined('PDF_MARGIN_BOTTOM')) {
                define('PDF_MARGIN_BOTTOM', 25);
            }
            if (!defined('PDF_IMAGE_SCALE_RATIO')) {
                define('PDF_IMAGE_SCALE_RATIO', 1.25);
            }
            if (!defined('PDF_CREATOR')) {
                define('PDF_CREATOR', 'Game Masters');
            }
            
            // Créer le PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Informations du document
            $pdf->SetCreator('Game Masters');
            $pdf->SetAuthor('Game Masters');
            $pdf->SetTitle('Liste des Utilisateurs');
            $pdf->SetSubject('Liste complète des utilisateurs inscrits');
            
            // En-tête et pied de page
            $pdf->SetHeaderData('', 0, 'Game Masters - Administration', 'Liste des utilisateurs générée le ' . date('d/m/Y à H:i'));
            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array('helvetica', '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont('courier');
            $pdf->SetMargins(15, 25, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 15);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // Ajouter une page
            $pdf->AddPage();
            
            // Titre
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Cell(0, 15, 'Liste des Utilisateurs', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Tableau principal avec informations de base
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(60, 60, 60);
            $pdf->SetTextColor(255, 255, 255);
            
            // En-têtes du tableau principal
            $pdf->Cell(12, 8, 'ID', 1, 0, 'C', 1);
            $pdf->Cell(40, 8, 'Utilisateur', 1, 0, 'C', 1);
            $pdf->Cell(55, 8, 'Email', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Rôle', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Statut', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Médaille', 1, 0, 'C', 1);
            $pdf->Cell(18, 8, 'Inscription', 1, 1, 'C', 1);
            
            // Données principales
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $fill = 0;
            
            foreach($users as $user) {
                // Get medal value, default to 'none' if not set
                $medal = $user['medal'] ?? 'none';
                $medalDisplay = '—';
                
                // Convert medal value to display format
                if ($medal === 'bronze') {
                    $medalDisplay = 'Bronze';
                } elseif ($medal === 'silver') {
                    $medalDisplay = 'Argent';
                } elseif ($medal === 'gold') {
                    $medalDisplay = 'Or';
                }
                
                $pdf->SetFillColor(245, 245, 245);
                $pdf->Cell(12, 7, $user['id'], 1, 0, 'C', $fill);
                $pdf->Cell(40, 7, substr($user['username'], 0, 18), 1, 0, 'L', $fill);
                $pdf->Cell(55, 7, substr($user['email'], 0, 28), 1, 0, 'L', $fill);
                $pdf->Cell(20, 7, ucfirst($user['role']), 1, 0, 'C', $fill);
                $pdf->Cell(20, 7, ucfirst($user['status']), 1, 0, 'C', $fill);
                $pdf->Cell(25, 7, $medalDisplay, 1, 0, 'C', $fill);
                $pdf->Cell(18, 7, date('d/m/Y', strtotime($user['created_at'])), 1, 1, 'C', $fill);
                $fill = !$fill;
            }
            
            // Section détaillée avec informations de profil
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Détails des Profils Utilisateurs', 0, 1, 'C');
            $pdf->Ln(5);
            
            $pdf->SetFont('helvetica', '', 9);
            foreach($users as $index => $user) {
                if ($index > 0 && $index % 2 == 0) {
                    $pdf->AddPage();
                }
                
                // Informations de base
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->SetFillColor(200, 200, 200);
                $pdf->Cell(0, 8, 'Utilisateur #' . $user['id'] . ' - ' . $user['username'], 1, 1, 'L', 1);
                
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetFillColor(250, 250, 250);
                
                // Informations principales
                $pdf->Cell(50, 6, 'Email:', 1, 0, 'L', 1);
                $pdf->Cell(0, 6, $user['email'], 1, 1, 'L', 0);
                
                $pdf->Cell(50, 6, 'Rôle:', 1, 0, 'L', 1);
                $pdf->Cell(0, 6, ucfirst($user['role']), 1, 1, 'L', 0);
                
                $pdf->Cell(50, 6, 'Statut:', 1, 0, 'L', 1);
                $pdf->Cell(0, 6, ucfirst($user['status']), 1, 1, 'L', 0);
                
                $pdf->Cell(50, 6, 'Date d\'inscription:', 1, 0, 'L', 1);
                $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($user['created_at'])), 1, 1, 'L', 0);
                
                if (!empty($user['last_login'])) {
                    $pdf->Cell(50, 6, 'Dernière connexion:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($user['last_login'])), 1, 1, 'L', 0);
                }
                
                // Informations de profil
                $profile = $user['profile'] ?? [];
                if (!empty($profile['first_name']) || !empty($profile['last_name'])) {
                    $pdf->Cell(50, 6, 'Nom complet:', 1, 0, 'L', 1);
                    $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
                    $pdf->Cell(0, 6, $fullName ?: 'Non renseigné', 1, 1, 'L', 0);
                }
                
                if (!empty($profile['discord'])) {
                    $pdf->Cell(50, 6, 'Discord:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['discord'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['country'])) {
                    $pdf->Cell(50, 6, 'Pays:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['country'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['nationality'])) {
                    $pdf->Cell(50, 6, 'Nationalité:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['nationality'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['gender'])) {
                    $pdf->Cell(50, 6, 'Genre:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['gender'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['birth_date'])) {
                    $pdf->Cell(50, 6, 'Date de naissance:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, date('d/m/Y', strtotime($profile['birth_date'])), 1, 1, 'L', 0);
                }
                
                if (!empty($profile['career_level'])) {
                    $pdf->Cell(50, 6, 'Niveau de carrière:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['career_level'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['expertise'])) {
                    $pdf->Cell(50, 6, 'Expertise:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['expertise'], 1, 1, 'L', 0);
                }
                
                if (!empty($profile['tech_stack'])) {
                    $pdf->Cell(50, 6, 'Stack technique:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, substr($profile['tech_stack'], 0, 100), 1, 1, 'L', 0);
                }
                
                if (!empty($profile['timezone'])) {
                    $pdf->Cell(50, 6, 'Fuseau horaire:', 1, 0, 'L', 1);
                    $pdf->Cell(0, 6, $profile['timezone'], 1, 1, 'L', 0);
                }
                
                $pdf->Ln(5);
            }
            
            // Statistiques
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Statistiques', 0, 1, 'C');
            $pdf->Ln(5);
            
            $pdf->SetFont('helvetica', '', 11);
            $totalUsers = count($users);
            $activeUsers = count(array_filter($users, function($u) { return $u['status'] === 'active'; }));
            $adminUsers = count(array_filter($users, function($u) { return $u['role'] === 'admin'; }));
            $moderatorUsers = count(array_filter($users, function($u) { return $u['role'] === 'moderator'; }));
            $playerUsers = count(array_filter($users, function($u) { return $u['role'] === 'player'; }));
            $profilesCompleted = count(array_filter($users, function($u) { 
                $p = $u['profile'] ?? []; 
                return !empty($p['first_name']); 
            }));
            
            $pdf->Cell(0, 7, 'Total utilisateurs: ' . $totalUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Utilisateurs actifs: ' . $activeUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Administrateurs: ' . $adminUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Modérateurs: ' . $moderatorUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Joueurs: ' . $playerUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Profils complétés: ' . $profilesCompleted . ' (' . round(($profilesCompleted / max($totalUsers, 1)) * 100, 1) . '%)', 0, 1, 'L');
            
            // Sortie
            $pdf->Output('liste_utilisateurs_gamemasters_' . date('Y-m-d') . '.pdf', 'D');
            exit;
            break;
            
        case 'admin_export_games_pdf':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/UserController.php";
            require_once "controllers/GameController.php";
            $userController = new UserController($db);
            $gameController = new GameController($db);
            
            // Charger l'autoloader si ce n'est pas déjà fait
            $autoloadPaths = [
                __DIR__ . '/vendor/autoload.php',
                __DIR__ . '/../vendor/autoload.php'
            ];
            
            $autoloadLoaded = false;
            foreach ($autoloadPaths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $autoloadLoaded = true;
                    break;
                }
            }
            
            // Charger TCPDF directement si l'autoloader ne le charge pas
            if (!class_exists('TCPDF')) {
                $tcpdfPaths = [
                    __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php',
                    __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php'
                ];
                
                foreach ($tcpdfPaths as $tcpdfPath) {
                    if (file_exists($tcpdfPath)) {
                        require_once $tcpdfPath;
                        break;
                    }
                }
            }
            
            // Récupérer tous les utilisateurs avec leurs jeux
            try {
                $users = $userController->getUsersWithProfiles();
                
                // Éliminer les doublons basés sur l'ID utilisateur
                $uniqueUsers = [];
                $seenIds = [];
                foreach ($users as $user) {
                    $userId = $user['id'];
                    if (!in_array($userId, $seenIds)) {
                        $seenIds[] = $userId;
                        $uniqueUsers[] = $user;
                    }
                }
                $users = $uniqueUsers;
                
                // Pour chaque utilisateur, récupérer ses jeux
                foreach ($users as &$user) {
                    $userGames = $gameController->getUserGames($user['id']);
                    $user['games'] = $userGames;
                }
            } catch (Exception $e) {
                error_log("Erreur getUsersWithProfiles: " . $e->getMessage());
                $users = [];
            }
            
            // Nettoyer le buffer de sortie pour éviter l'erreur TCPDF
            if (ob_get_length()) ob_end_clean();
            
            // Vérifier si TCPDF est disponible
            if (!class_exists('TCPDF')) {
                die("Erreur: TCPDF n'est pas installé. Veuillez installer la bibliothèque TCPDF via Composer.");
            }
            
            // Définir les constantes TCPDF si elles n'existent pas
            if (!defined('PDF_PAGE_ORIENTATION')) {
                define('PDF_PAGE_ORIENTATION', 'P');
            }
            if (!defined('PDF_UNIT')) {
                define('PDF_UNIT', 'mm');
            }
            if (!defined('PDF_PAGE_FORMAT')) {
                define('PDF_PAGE_FORMAT', 'A4');
            }
            if (!defined('PDF_FONT_NAME_MAIN')) {
                define('PDF_FONT_NAME_MAIN', 'helvetica');
            }
            if (!defined('PDF_FONT_SIZE_MAIN')) {
                define('PDF_FONT_SIZE_MAIN', 10);
            }
            if (!defined('PDF_FONT_NAME_DATA')) {
                define('PDF_FONT_NAME_DATA', 'helvetica');
            }
            if (!defined('PDF_FONT_SIZE_DATA')) {
                define('PDF_FONT_SIZE_DATA', 8);
            }
            if (!defined('PDF_FONT_MONOSPACED')) {
                define('PDF_FONT_MONOSPACED', 'courier');
            }
            if (!defined('PDF_MARGIN_LEFT')) {
                define('PDF_MARGIN_LEFT', 15);
            }
            if (!defined('PDF_MARGIN_TOP')) {
                define('PDF_MARGIN_TOP', 27);
            }
            if (!defined('PDF_MARGIN_RIGHT')) {
                define('PDF_MARGIN_RIGHT', 15);
            }
            if (!defined('PDF_MARGIN_HEADER')) {
                define('PDF_MARGIN_HEADER', 5);
            }
            if (!defined('PDF_MARGIN_FOOTER')) {
                define('PDF_MARGIN_FOOTER', 10);
            }
            if (!defined('PDF_MARGIN_BOTTOM')) {
                define('PDF_MARGIN_BOTTOM', 25);
            }
            if (!defined('PDF_IMAGE_SCALE_RATIO')) {
                define('PDF_IMAGE_SCALE_RATIO', 1.25);
            }
            if (!defined('PDF_CREATOR')) {
                define('PDF_CREATOR', 'Game Masters');
            }
            
            // Créer le PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Informations du document
            $pdf->SetCreator('Game Masters');
            $pdf->SetAuthor('Game Masters');
            $pdf->SetTitle('Liste des Jeux par Utilisateur');
            $pdf->SetSubject('Liste des jeux ajoutés par chaque utilisateur');
            
            // En-tête et pied de page
            $pdf->SetHeaderData('', 0, 'Game Masters - Administration', 'Liste des jeux par utilisateur générée le ' . date('d/m/Y à H:i'));
            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array('helvetica', '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont('courier');
            $pdf->SetMargins(15, 25, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 15);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // Ajouter une page
            $pdf->AddPage();
            
            // Titre
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Cell(0, 15, 'Liste des Jeux par Utilisateur', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Tableau récapitulatif avec noms des jeux
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(60, 60, 60);
            $pdf->SetTextColor(255, 255, 255);
            
            // En-têtes du tableau récapitulatif
            $pdf->Cell(12, 8, 'ID', 1, 0, 'C', 1);
            $pdf->Cell(38, 8, 'Utilisateur', 1, 0, 'C', 1);
            $pdf->Cell(38, 8, 'Email', 1, 0, 'C', 1);
            $pdf->Cell(15, 8, 'Nb', 1, 0, 'C', 1);
            $pdf->Cell(87, 8, 'Jeux ajoutés', 1, 1, 'C', 1);
            
            // Données récapitulatives avec noms des jeux
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(0, 0, 0);
            $fill = 0;
            
            foreach($users as $user) {
                $gamesCount = count($user['games'] ?? []);
                $gamesList = [];
                foreach($user['games'] ?? [] as $game) {
                    $gamesList[] = $game['name'] ?? 'Sans nom';
                }
                $gamesNames = !empty($gamesList) ? implode(', ', $gamesList) : 'Aucun jeu';
                // Limiter la longueur pour éviter que le texte soit trop long
                if (strlen($gamesNames) > 100) {
                    $gamesNames = substr($gamesNames, 0, 97) . '...';
                }
                
                $pdf->SetFillColor(245, 245, 245);
                
                // Sauvegarder la position
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                
                // Dessiner les cellules fixes d'abord
                $pdf->Cell(12, 6, $user['id'], 1, 0, 'C', $fill);
                $pdf->Cell(38, 6, substr($user['username'], 0, 18), 1, 0, 'L', $fill);
                $pdf->Cell(38, 6, substr($user['email'], 0, 23), 1, 0, 'L', $fill);
                $pdf->Cell(15, 6, $gamesCount, 1, 0, 'C', $fill);
                
                // Calculer la hauteur nécessaire pour MultiCell
                $lineHeight = 5;
                $maxWidth = 87;
                $textWidth = $pdf->GetStringWidth($gamesNames);
                $estimatedLines = max(1, ceil($textWidth / $maxWidth) + 1);
                $cellHeight = $estimatedLines * $lineHeight;
                
                // Dessiner la cellule des jeux avec MultiCell
                $pdf->MultiCell(87, $lineHeight, $gamesNames, 1, 'L', $fill);
                
                // Ajuster les autres cellules pour correspondre à la hauteur
                $newY = $pdf->GetY();
                $actualHeight = $newY - $y;
                
                if ($actualHeight > 6) {
                    // Redessiner les cellules avec la bonne hauteur
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(12, $actualHeight, $user['id'], 1, 0, 'C', $fill);
                    $pdf->Cell(38, $actualHeight, substr($user['username'], 0, 18), 1, 0, 'L', $fill);
                    $pdf->Cell(38, $actualHeight, substr($user['email'], 0, 23), 1, 0, 'L', $fill);
                    $pdf->Cell(15, $actualHeight, $gamesCount, 1, 0, 'C', $fill);
                    $pdf->SetXY($x + 103, $y);
                    $pdf->MultiCell(87, $lineHeight, $gamesNames, 1, 'L', $fill);
                }
                
                $fill = !$fill;
            }
            
            // Section détaillée : chaque utilisateur avec ses jeux
            foreach($users as $user) {
                if (empty($user['games'])) {
                    continue; // Passer les utilisateurs sans jeux
                }
                
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 16);
                $pdf->Cell(0, 10, 'Utilisateur: ' . $user['username'] . ' (ID: ' . $user['id'] . ')', 0, 1, 'L');
                $pdf->Ln(3);
                
                // Informations utilisateur
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(40, 6, 'Email:', 0, 0, 'L');
                $pdf->Cell(0, 6, $user['email'], 0, 1, 'L');
                $pdf->Cell(40, 6, 'Rôle:', 0, 0, 'L');
                $pdf->Cell(0, 6, ucfirst($user['role']), 0, 1, 'L');
                $pdf->Cell(40, 6, 'Statut:', 0, 0, 'L');
                $pdf->Cell(0, 6, ucfirst($user['status']), 0, 1, 'L');
                $pdf->Ln(5);
                
                // Tableau des jeux
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 8, 'Jeux ajoutés (' . count($user['games']) . ')', 0, 1, 'L');
                $pdf->Ln(3);
                
                // En-têtes du tableau des jeux
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(60, 60, 60);
                $pdf->SetTextColor(255, 255, 255);
                
                $pdf->Cell(15, 7, 'ID', 1, 0, 'C', 1);
                $pdf->Cell(60, 7, 'Nom du Jeu', 1, 0, 'C', 1);
                $pdf->Cell(40, 7, 'Catégorie', 1, 0, 'C', 1);
                $pdf->Cell(30, 7, 'Statut', 1, 0, 'C', 1);
                $pdf->Cell(45, 7, 'Date d\'ajout', 1, 1, 'C', 1);
                
                // Données des jeux
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(0, 0, 0);
                $fill = 0;
                
                foreach($user['games'] as $game) {
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->Cell(15, 6, $game['id'], 1, 0, 'C', $fill);
                    $pdf->Cell(60, 6, substr($game['name'] ?? 'Sans nom', 0, 35), 1, 0, 'L', $fill);
                    $pdf->Cell(40, 6, substr($game['category_name'] ?? 'Non catégorisé', 0, 25), 1, 0, 'L', $fill);
                    $pdf->Cell(30, 6, ucfirst($game['status'] ?? 'N/A'), 1, 0, 'C', $fill);
                    $pdf->Cell(45, 6, date('d/m/Y', strtotime($game['created_at'] ?? 'now')), 1, 1, 'C', $fill);
                    $fill = !$fill;
                }
            }
            
            // Page de statistiques
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Statistiques', 0, 1, 'C');
            $pdf->Ln(5);
            
            $pdf->SetFont('helvetica', '', 11);
            $totalUsers = count($users);
            $usersWithGames = count(array_filter($users, function($u) { return !empty($u['games']); }));
            $totalGames = 0;
            $publishedGames = 0;
            $pendingGames = 0;
            
            foreach($users as $user) {
                $totalGames += count($user['games'] ?? []);
                foreach($user['games'] ?? [] as $game) {
                    if (($game['status'] ?? '') === 'published') {
                        $publishedGames++;
                    } elseif (($game['approval_status'] ?? '') === 'pending') {
                        $pendingGames++;
                    }
                }
            }
            
            $pdf->Cell(0, 7, 'Total utilisateurs: ' . $totalUsers, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Utilisateurs avec jeux: ' . $usersWithGames, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Total jeux ajoutés: ' . $totalGames, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Jeux publiés: ' . $publishedGames, 0, 1, 'L');
            $pdf->Cell(0, 7, 'Jeux en attente: ' . $pendingGames, 0, 1, 'L');
            
            // Sortie
            $pdf->Output('liste_jeux_par_utilisateur_' . date('Y-m-d') . '.pdf', 'D');
            exit;
            break;
            
        case 'admin_get_game_ratings':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
                exit;
            }
            
            if (!isset($_GET['game_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'ID de jeu manquant']);
                exit;
            }
            
            require_once "models/GameRating.php";
            $ratingModel = new GameRating($db);
            
            $gameId = (int)$_GET['game_id'];
            $ratings = $ratingModel->getGameRatingsWithUsers($gameId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'ratings' => $ratings]);
            exit;
            break;
            
        case 'admin_users':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/UserController.php";
            $userController = new UserController($db);
            
            // Gérer les actions ban/unban via paramètres GET
            if (isset($_GET['ban_user']) && isset($_GET['id'])) {
                // Si c'est une requête POST avec durée, utiliser la nouvelle méthode
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ban_duration'])) {
                    $duration = $_POST['ban_duration'];
                    $result = $userController->ban((int)$_POST['user_id'], $duration);
                } else {
                    // Ancien système : bannissement permanent par défaut
                    $result = $userController->ban((int)$_GET['id'], 'permanent');
                }
                $message = $result['message'] ?? 'Utilisateur banni avec succès';
                header("Location: ?action=admin_users&message=" . urlencode($message));
                exit;
            }
            
            // Gérer le bannissement via POST (avec modal)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ban_user_modal'])) {
                $userId = (int)$_POST['user_id'];
                $duration = $_POST['ban_duration'] ?? 'permanent';
                
                // Si c'est une date personnalisée, utiliser directement la date
                if (isset($_POST['ban_custom_date']) && !empty($_POST['ban_custom_date'])) {
                    $duration = $_POST['ban_custom_date'];
                }
                
                $result = $userController->ban($userId, $duration);
                $message = $result['message'] ?? 'Utilisateur banni avec succès';
                header("Location: ?action=admin_users&message=" . urlencode($message));
                exit;
            }
            
            if (isset($_GET['unban_user']) && isset($_GET['id'])) {
                $result = $userController->unban((int)$_GET['id']);
                $message = $result['message'] ?? 'Utilisateur débanni avec succès';
                header("Location: ?action=admin_users&message=" . urlencode($message));
                exit;
            }
            
            // Gérer la suppression via paramètre GET
            if (isset($_GET['delete_user']) && isset($_GET['id'])) {
                $result = $userController->delete((int)$_GET['id']);
                $message = $result['message'] ?? 'Utilisateur supprimé avec succès';
                header("Location: ?action=admin_users&message=" . urlencode($message));
                exit;
            }
            
            // Gérer la modification via POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
                $userId = (int)$_POST['id'];
                $result = $userController->update($userId, $_POST);
                
                if ($result['success']) {
                    $message = $result['message'] ?? 'Utilisateur mis à jour avec succès';
                    header("Location: ?action=admin_users&message=" . urlencode($message));
                } else {
                    $errors = $result['errors'] ?? ['Erreur lors de la mise à jour'];
                    $errorMessage = implode(', ', $errors);
                    header("Location: ?action=admin_users&error=" . urlencode($errorMessage));
                }
                exit;
            }
            
            $users = $userController->getUsersWithProfiles();
            $userStats = $userController->getStats();
            
            // Gérer les messages
            $message = null;
            $success = true;
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                $success = strpos(strtolower($message), 'erreur') === false;
            }
            
            if (file_exists("views/admin/users.php")) {
                $pageTitle = 'Gestion des Utilisateurs - Game Master';
                $currentPage = 'users';
                include "views/admin/includes/header.php";
                include "views/admin/users.php";
                include "views/admin/includes/footer.php";
            } else {
                echo "Admin users page not found";
            }
            exit;
            
        case 'admin_user_edit':
        case 'admin_user_delete':
        case 'admin_user_ban':
        case 'admin_user_unban':
        case 'admin_user_create':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/UserController.php";
            $userController = new UserController($db);
            
            if ($action === 'admin_user_edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $userController->update($_POST['id'], $_POST);
                $_SESSION['success_message'] = $result['message'] ?? 'Utilisateur mis à jour';
                header("Location: ?action=admin_users");
                exit;
            } elseif ($action === 'admin_user_delete' && isset($_GET['id'])) {
                $result = $userController->delete($_GET['id']);
                $_SESSION['success_message'] = $result['message'] ?? 'Utilisateur supprimé';
                header("Location: ?action=admin_users");
                exit;
            } elseif ($action === 'admin_user_ban' && isset($_GET['id'])) {
                $result = $userController->ban($_GET['id']);
                $_SESSION['success_message'] = $result['message'] ?? 'Utilisateur banni';
                header("Location: ?action=admin_users");
                exit;
            } elseif ($action === 'admin_user_unban' && isset($_GET['id'])) {
                $result = $userController->unban($_GET['id']);
                $_SESSION['success_message'] = $result['message'] ?? 'Utilisateur débanni';
                header("Location: ?action=admin_users");
                exit;
            } elseif ($action === 'admin_user_create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $userController->create($_POST);
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = implode(', ', $result['errors'] ?? []);
                }
                header("Location: ?action=admin_users");
                exit;
            }
            exit;
            
        case 'admin_game_approve':
        case 'admin_game_reject':
        case 'admin_game_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/GameController.php";
            $gameController = new GameController($db);
            
            if (isset($_GET['id'])) {
                $gameId = (int)$_GET['id'];
                
                if ($action === 'admin_game_approve') {
                    $result = $gameController->approveGame($gameId);
                    $_SESSION['success_message'] = $result['message'] ?? 'Jeu approuvé';
                } elseif ($action === 'admin_game_reject') {
                    $result = $gameController->rejectGame($gameId);
                    $_SESSION['success_message'] = $result['message'] ?? 'Jeu rejeté';
                } elseif ($action === 'admin_game_delete') {
                    $result = $gameController->delete($gameId);
                    $_SESSION['success_message'] = $result['message'] ?? 'Jeu supprimé';
                }
                
                header("Location: ?action=admin_games");
                exit;
            }
            exit;

        case 'admin_donations':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use DonationController to handle admin donations
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->adminList();
            exit;

        case 'admin_donation_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use DonationController to handle admin donation deletion
            require_once "controllers/DonationController.php";
            $donationController = new DonationController($db);
            $donationController->adminDelete();
            exit;

        case 'admin_projects':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->adminList();
            exit;

        case 'admin_project_add':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->adminAdd();
            exit;

        case 'admin_project_edit':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->adminEdit();
            exit;

        case 'admin_project_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ProjectController.php";
            $projectController = new ProjectController($db);
            $projectController->adminDelete();
            exit;

        case 'admin_events':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin events list
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminList();
            exit;

        case 'admin_event_add':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin event add
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminAdd();
            exit;

        case 'admin_event_edit':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin event edit
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminEdit();
            exit;

        case 'admin_event_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin event deletion
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminDelete();
            exit;

        case 'admin_participations':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin participations list
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminParticipations();
            exit;

        case 'admin_participation_add':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin participation add
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminAddParticipation();
            exit;

        case 'admin_participation_edit':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin participation edit
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminEditParticipation();
            exit;

        case 'admin_participation_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Use EventController to handle admin participation deletion
            require_once "controllers/EventController.php";
            $eventController = new EventController($db);
            $eventController->adminDeleteParticipation();
            exit;

        // Reclamation Routes (Admin)
        case 'admin_reclamations':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->adminList();
            exit;

        case 'admin_reclamation_respond':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->adminRespond();
            exit;

        case 'admin_reclamation_edit':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->adminEdit();
            exit;

        case 'admin_reclamation_delete':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            require_once "controllers/ReclamationController.php";
            $reclamationController = new ReclamationController($db);
            $reclamationController->adminDelete();
            exit;

        case 'get_user_data':
            // Nettoyer TOUT le buffer de sortie AVANT tout
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Désactiver l'affichage des erreurs pour éviter le HTML dans la réponse JSON
            ini_set('display_errors', 0);
            error_reporting(E_ALL);
            
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => 'Accès non autorisé'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            header('Content-Type: application/json; charset=utf-8');
            
            $userId = $_GET['id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            try {
                require_once "controllers/UserController.php";
                require_once "controllers/ProfileController.php";
                
                $userController = new UserController($db);
                $profileController = new ProfileController($db);
                
                // Get user data
                $user = $userController->getUserById($userId);
                if (!$user || empty($user)) {
                    echo json_encode(['success' => false, 'error' => 'Utilisateur non trouvé'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                
                // Check if medal column exists
                $hasMedal = false;
                try {
                    $checkMedal = "SHOW COLUMNS FROM users LIKE 'medal'";
                    $checkStmt = $db->prepare($checkMedal);
                    $checkStmt->execute();
                    $hasMedal = $checkStmt->rowCount() > 0;
                } catch (Exception $e) {
                    $hasMedal = false;
                }
                
                // Get profile data
                $profile = null;
                try {
                    $profile = $profileController->getByUserId($userId);
                } catch (Exception $e) {
                    error_log("Error getting profile: " . $e->getMessage());
                    $profile = null;
                }
                
                // Build response
                $userData = [
                    'id' => $user['id'] ?? null,
                    'username' => $user['username'] ?? '',
                    'email' => $user['email'] ?? '',
                    'role' => $user['role'] ?? 'player',
                    'status' => $user['status'] ?? 'active',
                    'avatar' => $user['avatar'] ?? '',
                    'created_at' => $user['created_at'] ?? '',
                    'medal' => ($hasMedal && isset($user['medal'])) ? $user['medal'] : 'none',
                    'profile' => $profile ? [
                        'first_name' => $profile['first_name'] ?? '',
                        'last_name' => $profile['last_name'] ?? '',
                        'discord' => $profile['discord'] ?? '',
                        'country' => $profile['country'] ?? '',
                        'nationality' => $profile['nationality'] ?? '',
                        'gender' => $profile['gender'] ?? '',
                        'birth_date' => $profile['birth_date'] ?? '',
                        'career_level' => $profile['career_level'] ?? '',
                        'expertise' => $profile['expertise'] ?? '',
                        'tech_stack' => $profile['tech_stack'] ?? '',
                        'timezone' => $profile['timezone'] ?? 'Europe/Paris',
                        'bio' => $profile['bio'] ?? $profile['description'] ?? ''
                    ] : []
                ];
                
                echo json_encode([
                    'success' => true,
                    'user' => $userData
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } catch (Exception $e) {
                error_log("Erreur get_user_data: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la récupération des données utilisateur: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            } catch (Error $e) {
                error_log("Erreur fatale get_user_data: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur fatale lors de la récupération des données utilisateur: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;
            
        case 'admin_game_categories':
            // Check admin access
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: ?action=login");
                exit;
            }
            
            // Handle POST requests (create, update, delete)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['create_category'])) {
                    $name = trim($_POST['name'] ?? '');
                    if (!empty($name)) {
                        $query = "INSERT INTO game_categories (name) VALUES (:name)";
                        $stmt = $db->prepare($query);
                        $stmt->execute(['name' => $name]);
                        $_SESSION['success_message'] = 'Catégorie créée avec succès';
                    }
                } elseif (isset($_POST['update_category'])) {
                    $id = (int)$_POST['category_id'];
                    $name = trim($_POST['name'] ?? '');
                    if (!empty($name) && $id > 0) {
                        $query = "UPDATE game_categories SET name = :name WHERE id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute(['name' => $name, 'id' => $id]);
                        $_SESSION['success_message'] = 'Catégorie mise à jour avec succès';
                    }
                } elseif (isset($_POST['delete_category'])) {
                    $id = (int)$_POST['category_id'];
                    if ($id > 0) {
                        // Check if category is used
                        $checkQuery = "SELECT COUNT(*) as count FROM games WHERE category_id = :id";
                        $checkStmt = $db->prepare($checkQuery);
                        $checkStmt->execute(['id' => $id]);
                        $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($checkResult['count'] == 0) {
                            $query = "DELETE FROM game_categories WHERE id = :id";
                            $stmt = $db->prepare($query);
                            $stmt->execute(['id' => $id]);
                            $_SESSION['success_message'] = 'Catégorie supprimée avec succès';
                        } else {
                            $_SESSION['error_message'] = 'Impossible de supprimer cette catégorie car elle est utilisée par ' . $checkResult['count'] . ' jeu(x)';
                        }
                    }
                }
                header("Location: ?action=admin_game_categories");
                exit;
            }
            
            // Handle GET requests (list, edit)
            $editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
            $deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : null;
            
            if ($deleteId) {
                // Check if category is used
                $checkQuery = "SELECT COUNT(*) as count FROM games WHERE category_id = :id";
                $checkStmt = $db->prepare($checkQuery);
                $checkStmt->execute(['id' => $deleteId]);
                $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($checkResult['count'] == 0) {
                    $query = "DELETE FROM game_categories WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->execute(['id' => $deleteId]);
                    $_SESSION['success_message'] = 'Catégorie supprimée avec succès';
                } else {
                    $_SESSION['error_message'] = 'Impossible de supprimer cette catégorie car elle est utilisée par ' . $checkResult['count'] . ' jeu(x)';
                }
                header("Location: ?action=admin_game_categories");
                exit;
            }
            
            // Get all game categories
            $categoriesQuery = "SELECT gc.*, COUNT(g.id) as game_count 
                              FROM game_categories gc 
                              LEFT JOIN games g ON gc.id = g.category_id 
                              GROUP BY gc.id 
                              ORDER BY gc.name ASC";
            $categoriesStmt = $db->prepare($categoriesQuery);
            $categoriesStmt->execute();
            $gameCategories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get category to edit if any
            $editCategory = null;
            if ($editId) {
                $editQuery = "SELECT * FROM game_categories WHERE id = :id";
                $editStmt = $db->prepare($editQuery);
                $editStmt->execute(['id' => $editId]);
                $editCategory = $editStmt->fetch(PDO::FETCH_ASSOC);
            }
            
            $pageTitle = 'Gestion des Catégories Jeux - Admin';
            $currentPage = 'game_categories';
            include "views/admin/includes/header.php";
            include "views/admin/game_categories.php";
            include "views/admin/includes/footer.php";
            exit;
            
        default:
            // For other auth actions, you can add them here
            echo "Action '$action' not yet implemented in integrated routing";
            exit;
    }
}

// Original routing system for formations, educations, etc.
// Get controller and action from URL parameters
// Default to accueil (userDashboard) page when accessing root URL
if (empty($_GET) || (!isset($_GET['controller']) && !isset($_GET['action']))) {
    // Everyone (authenticated or not) sees accueil page by default
        $controller = 'formation';
        $action = 'userDashboard';
} else {
    // Only set controller if it's explicitly provided in URL
    // If only 'action' is provided, don't set controller to avoid controller-based routing
    $controller = isset($_GET['controller']) ? $_GET['controller'] : null;
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // Check authentication for protected controller-based routes
    // Allow public access to view pages (userDashboard, list, search, etc.)
    // But protect actions that modify data (create, update, delete, etc.)
    if ($controller) {
        $protectedControllerActions = ['create', 'update', 'delete', 'store', 'edit', 'toggleFormation', 'toggleEducation', 'manageTree'];
        if (in_array($action, $protectedControllerActions) && !AuthController::isLoggedIn()) {
            header("Location: ?action=login");
            exit;
        }
    }
}

// Handle unified chatbot requests
if ($controller === 'unifiedchatbot' && $action === 'handleRequest') {
    require_once "controllers/UnifiedChatbotController.php";
    $unifiedChatbotController = new UnifiedChatbotController();
    $unifiedChatbotController->handleRequest();
    exit;
}

// Legacy chatbot routes (kept for backward compatibility)
if ($controller === 'chatbot' && $action === 'handleRequest') {
    require_once "controllers/ChatbotController.php";
    $chatbotController = new ChatbotController();
    $chatbotController->handleRequest();
    exit;
}

if ($controller === 'gamechatbot' && $action === 'handleRequest') {
    require_once "controllers/GameChatbotController.php";
    $gameChatbotController = new GameChatbotController();
    $gameChatbotController->handleRequest();
    exit;
}

if ($controller === 'projectchatbot' && $action === 'handleRequest') {
    require_once "controllers/ProjectChatbotController.php";
    $projectChatbotController = new ProjectChatbotController();
    $projectChatbotController->handleRequest();
    exit;
}

if ($controller === 'eventchatbot' && $action === 'handleRequest') {
    require_once "controllers/EventChatbotController.php";
    $eventChatbotController = new EventChatbotController();
    $eventChatbotController->handleRequest();
    exit;
}

// Only proceed with controller-based routing if controller is explicitly set
if ($controller) {
    // Convert controller name to Controller class name (e.g., 'formation' -> 'FormationController')
    // Handle special case for gameLibrary -> GameLibraryController
    if ($controller === 'gameLibrary') {
        $controllerClass = 'GameLibraryController';
    } else {
        $controllerClass = ucfirst($controller) . 'Controller';
    }

    // Controller file path
    if ($controller === 'gameLibrary') {
        $controllerFile = "controllers/GameLibraryController.php";
    } else {
        $controllerFile = "controllers/" . $controllerClass . ".php";
    }

    // Check if controller file exists
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Check if controller class exists
        if (class_exists($controllerClass)) {
            // Pass $db to controller constructor if it accepts it
            $reflection = new ReflectionClass($controllerClass);
            $constructor = $reflection->getConstructor();
            if ($constructor && $constructor->getNumberOfParameters() > 0) {
                $controllerInstance = new $controllerClass($db);
            } else {
                $controllerInstance = new $controllerClass();
            }
            
            // Handle actions that require parameters
            $paramActions = ['detail', 'edit', 'delete', 'toggleFormation', 'toggleEducation', 'manageTree', 'reviewRequest', 'editQuestion', 'deleteQuestion'];
            $paramId = $_GET['id'] ?? null;
            
            // Special handling for test controller actions
            if ($controller === 'test' && in_array($action, ['testPage', 'results', 'submitTest', 'saveAnswer']) && isset($_GET['attempt_id'])) {
                // These actions don't need parameters passed to method
                $controllerInstance->$action();
            } elseif ($controller === 'adminTest' && in_array($action, ['reviewResult', 'assignMedal']) && isset($_GET['attempt_id'])) {
                $controllerInstance->$action();
            } elseif ($controller === 'certificate' && $action === 'downloadCertificate' && isset($_GET['attempt_id'])) {
                $controllerInstance->$action();
            } elseif (in_array($action, $paramActions) && $paramId) {
                $controllerInstance->$action($paramId);
            } else {
                // Handle actions without parameters
                if (method_exists($controllerInstance, $action)) {
                    $controllerInstance->$action();
                } else {
                    die("Action '$action' not found in $controllerClass");
                }
            }
        } else {
            die("Controller class '$controllerClass' not found");
        }
    } else {
        die("Controller file '$controllerFile' not found");
    }
}
?>

