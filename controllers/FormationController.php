<?php
require_once "models/Formation.php";
require_once "config/database.php";
require_once "controllers/CategoryController.php";

class FormationController {

    // List for front with search
    public function list() {
        $formation = new Formation();
        
        // Handle search
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
        if (!empty($searchQuery)) {
            $stmt = $formation->search($searchQuery);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $formation->getAll();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        include "views/front/formations_list.php";
    }

    // Detail page - Updated to show related educations
    public function detail($id) {
        try {
            require_once "models/Education.php";
            require_once "models/Favorite.php";
            require_once "models/RecommendationService.php";
            
            // Get formation details
            $formation = new Formation();
            $result = $formation->getById($id);
            
            // Get related educations
            if ($result) {
                $education = new Education();
                $relatedEducations = $education->getByFormationId($id);
                $skillTree = $education->getTreeByFormationId($id);
                
                // Get AI Recommendations
                $recommendationService = new RecommendationService();
                $recommendations = $recommendationService->getRecommendations($result);
            } else {
                $relatedEducations = [];
                $skillTree = [];
                $recommendations = [];
            }
            
            // Check if formation is favorited
            $isFavorite = false;
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Use authenticated user ID or fallback to temp for guests
            $user_id = null;
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            } elseif (isset($_SESSION['temp_user_id']) && $_SESSION['temp_user_id'] <= 2147483647) {
                $user_id = $_SESSION['temp_user_id'];
            } else {
                // Generate temp ID if not exists or invalid (for guest users)
                $_SESSION['temp_user_id'] = crc32(session_id()) & 0x7FFFFFFF;
                $user_id = $_SESSION['temp_user_id'];
            }

            if ($user_id) {
                file_put_contents('debug_favorites.log', date('Y-m-d H:i:s') . " - FormationController Detail: ID=$id User=$user_id SessionID=" . session_id() . "\n", FILE_APPEND);
                $favorite = new Favorite();
                // We don't need getOrCreateUser anymore as we're using direct ID
                $isFavorite = $favorite->isFormationFavorite($user_id, $id);
            }
            
            include "views/front/formation_details.php";
        } catch (Exception $e) {
            error_log("Error fetching formation: " . $e->getMessage());
            $result = null;
            $relatedEducations = [];
            $recommendations = [];
            $isFavorite = false;
            include "views/front/formation_details.php";
        }
    }

    // User Dashboard
    public function userDashboard() {
        // Check for medal notification on dashboard load as fallback
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id']) && !isset($_GET['medal_checked'])) {
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            
            try {
                // Check if medal_notification_seen column exists
                $columnCheck = "SHOW COLUMNS FROM users LIKE 'medal_notification_seen'";
                $columnStmt = $conn->prepare($columnCheck);
                $columnStmt->execute();
                $hasColumn = $columnStmt->rowCount() > 0;
                
                if ($hasColumn) {
                    $checkQuery = "SELECT medal, medal_notification_seen FROM users WHERE id = ?";
                    $checkStmt = $conn->prepare($checkQuery);
                    $checkStmt->execute([$_SESSION['user_id']]);
                    $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($userData) {
                        $medal = $userData['medal'] ?? 'none';
                        $notificationSeen = $userData['medal_notification_seen'] ?? 1;
                        
                        // If user has a medal and hasn't seen the notification, redirect to show it
                        if ($medal !== 'none' && $notificationSeen == 0) {
                            header("Location: ?controller=test&action=showMedalNotification&medal=" . urlencode($medal));
                            exit;
                        }
                    }
                }
            } catch (PDOException $e) {
                error_log("Error checking medal notification on dashboard: " . $e->getMessage());
            }
        }
        
        include "views/front/dashboard.php";
    }

    // Dashboard (Admin) - Redirect to unified admin dashboard
    public function dashboard() {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?controller=formation&action=list");
            exit;
        }
        // Redirect to unified admin dashboard
        header("Location: ?action=admin_dashboard");
        exit;
    }

    // Admin list
    public function adminList() {
        $formation = new Formation();
        $results = $formation->getAll();
        include "views/admin/formations_list.php";
    }

    // Add form & insert
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formation = new Formation();
            $formation->create(
                $_POST['title'],
                $_POST['description'],
                $_POST['competences'] ?? '',
                $_POST['difficulte'] ?? 'Débutant',
                $_POST['duree'] ?? 0,
                $_POST['categorie'] ?? '',
                $_POST['lien_ressources'] ?? '',
                $_POST['impact_social'] ?? ''
            );
            header("Location: ?controller=formation&action=adminList");
        } else {
            include "views/admin/formations_add.php";
        }
    }

    // Edit form & update
    public function edit($id) {
        $formation = new Formation();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formation->update(
                $id,
                $_POST['title'],
                $_POST['description'],
                $_POST['competences'] ?? '',
                $_POST['difficulte'] ?? 'Débutant',
                $_POST['duree'] ?? 0,
                $_POST['categorie'] ?? '',
                $_POST['lien_ressources'] ?? '',
                $_POST['impact_social'] ?? ''
            );
            header("Location: ?controller=formation&action=adminList");
        } else {
            $result = $formation->getById($id);
            include "views/admin/formations_edit.php";
        }
    }

    // Delete
    public function delete($id) {
        $formation = new Formation();
        $formation->delete($id);
        header("Location: ?controller=formation&action=adminList");
    }
    // Manage Tree (Hierarchy)
    public function manageTree($id) {
        $formation = new Formation();
        $education = new Education();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['parents']) && is_array($_POST['parents'])) {
                foreach ($_POST['parents'] as $edu_id => $parent_id) {
                    // Prevent self-parenting
                    if ($edu_id != $parent_id) {
                        $education->updateParent($edu_id, $parent_id);
                    }
                }
            }
            header("Location: ?controller=formation&action=manageTree&id=" . $id . "&success=1");
            exit;
        }
        
        $result = $formation->getById($id);
        $educations = $education->getByFormationId($id);
        
        include "views/admin/formation_tree_manager.php";
    }

    // In both controllers, add this method
    public function search() {
        include "views/front/search_content.php";
    }
}
?>
