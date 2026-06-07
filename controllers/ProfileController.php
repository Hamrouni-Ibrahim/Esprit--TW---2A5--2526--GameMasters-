<?php
class ProfileController {
    private $profileModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        require_once __DIR__ . '/../models/Profile.php';
        $this->profileModel = new Profile($db);
    }

    // Afficher le profil de l'utilisateur
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $this->profileModel->user_id = $userId;
        
        // Get profile using readByUserId method
        $profile = null;
        if ($this->profileModel->readByUserId()) {
            $profile = [
                'first_name' => $this->profileModel->first_name,
                'last_name' => $this->profileModel->last_name,
                'discord' => $this->profileModel->discord,
                'country' => $this->profileModel->country,
                'nationality' => $this->profileModel->nationality,
                'gender' => $this->profileModel->gender,
                'birth_date' => $this->profileModel->birth_date,
                'career_level' => $this->profileModel->career_level,
                'expertise' => $this->profileModel->expertise,
                'tech_stack' => $this->profileModel->tech_stack,
                'timezone' => $this->profileModel->timezone
            ];
        }
        
        // Get user info
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User($this->conn);
        $userModel->id = $userId;
        $user = null;
        if ($userModel->readOne()) {
            // Vérifier si la colonne medal existe
            $checkMedalQuery = "SHOW COLUMNS FROM users LIKE 'medal'";
            $checkMedalStmt = $this->conn->prepare($checkMedalQuery);
            $checkMedalStmt->execute();
            $hasMedalColumn = $checkMedalStmt->rowCount() > 0;
            
            $medal = 'none';
            if ($hasMedalColumn) {
                $medalQuery = "SELECT medal FROM users WHERE id = ?";
                $medalStmt = $this->conn->prepare($medalQuery);
                $medalStmt->execute([$userId]);
                $medalRow = $medalStmt->fetch(PDO::FETCH_ASSOC);
                $medal = $medalRow['medal'] ?? 'none';
            }
            
            $user = [
                'id' => $userModel->id,
                'username' => $userModel->username,
                'email' => $userModel->email,
                'role' => $userModel->role,
                'avatar' => $userModel->avatar ?? null,
                'created_at' => $userModel->created_at ?? null,
                'medal' => $medal
            ];
            error_log("ProfileController - User ID: " . $userId);
            error_log("ProfileController - User avatar from DB: " . ($user['avatar'] ?? 'NULL'));
            error_log("ProfileController - User avatar property: " . ($userModel->avatar ?? 'NULL'));
        } else {
            error_log("ProfileController - Failed to read user with ID: " . $userId);
        }
        
        // Get face authentication info
        $faceInfo = null;
        require_once __DIR__ . '/../models/FaceAuth.php';
        $faceAuth = new FaceAuth($this->conn);
        $faceInfo = $faceAuth->getFaceInfo($userId);

        $pageTitle = 'Mon Profil - Game Master';
        $currentPage = 'profile';
        include "views/front/includes/header.php";
        include "views/front/profile_view.php";
        include "views/front/includes/footer.php";
    }

    // Modifier le profil
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $result = $this->save($userId, $_POST);
            
            if ($result['success']) {
                header('Location: ?action=profile&success=1');
                exit;
            } else {
                $errors = $result['errors'];
            }
        }

        $userId = $_SESSION['user_id'];
        $this->profileModel->user_id = $userId;
        
        // Get profile using readByUserId method
        $profile = null;
        if ($this->profileModel->readByUserId()) {
            $profile = [
                'first_name' => $this->profileModel->first_name,
                'last_name' => $this->profileModel->last_name,
                'discord' => $this->profileModel->discord,
                'country' => $this->profileModel->country,
                'nationality' => $this->profileModel->nationality,
                'gender' => $this->profileModel->gender,
                'birth_date' => $this->profileModel->birth_date,
                'career_level' => $this->profileModel->career_level,
                'expertise' => $this->profileModel->expertise,
                'tech_stack' => $this->profileModel->tech_stack,
                'timezone' => $this->profileModel->timezone
            ];
        }
        
        // Get user info
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User($this->conn);
        $userModel->id = $userId;
        $user = null;
        if ($userModel->readOne()) {
            $user = [
                'id' => $userModel->id,
                'username' => $userModel->username,
                'email' => $userModel->email,
                'role' => $userModel->role,
                'avatar' => $userModel->avatar ?? null
            ];
        }

        $pageTitle = 'Modifier mon Profil - Game Master';
        $currentPage = 'profile';
        include "views/front/includes/header.php";
        include "views/front/edit_profile.php";
        include "views/front/includes/footer.php";
    }

    // Sauvegarder le profil
    public function save($user_id, $data) {
        $errors = $this->validateProfileData($data);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->profileModel->user_id = $user_id;
        $this->profileModel->first_name = $data['first_name'] ?? '';
        $this->profileModel->last_name = $data['last_name'] ?? '';
        $this->profileModel->discord = $data['discord'] ?? '';
        $this->profileModel->country = $data['country'] ?? '';
        $this->profileModel->nationality = $data['nationality'] ?? '';
        $this->profileModel->gender = $data['gender'] ?? '';
        $this->profileModel->birth_date = $data['birth_date'] ?? '';
        $this->profileModel->career_level = $data['career_level'] ?? '';
        $this->profileModel->expertise = $data['expertise'] ?? '';
        $this->profileModel->tech_stack = $data['tech_stack'] ?? '';
        $this->profileModel->timezone = $data['timezone'] ?? 'Europe/Paris';

        if($this->profileModel->save()) {
            $_SESSION['profile_completed'] = true;
            return ['success' => true, 'message' => 'Profil sauvegardé avec succès!'];
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de la sauvegarde du profil']];
    }

    // Valider les données du profil
    private function validateProfileData($data) {
        $errors = [];
        
        if(empty($data['first_name'])) {
            $errors[] = 'Le prénom est requis';
        }
        
        if(empty($data['last_name'])) {
            $errors[] = 'Le nom est requis';
        }
        
        return $errors;
    }

    // Récupérer le profil par user_id
    public function getByUserId($user_id) {
        try {
            $this->profileModel->user_id = $user_id;
            
            if ($this->profileModel->readByUserId()) {
                return [
                    'first_name' => $this->profileModel->first_name ?? '',
                    'last_name' => $this->profileModel->last_name ?? '',
                    'discord' => $this->profileModel->discord ?? '',
                    'country' => $this->profileModel->country ?? '',
                    'nationality' => $this->profileModel->nationality ?? '',
                    'gender' => $this->profileModel->gender ?? '',
                    'birth_date' => $this->profileModel->birth_date ?? '',
                    'career_level' => $this->profileModel->career_level ?? '',
                    'expertise' => $this->profileModel->expertise ?? '',
                    'tech_stack' => $this->profileModel->tech_stack ?? '',
                    'timezone' => $this->profileModel->timezone ?? 'Europe/Paris',
                    'bio' => '' // Profile model doesn't have bio/description field
                ];
            }
            return null;
        } catch (Exception $e) {
            error_log("ProfileController::getByUserId error: " . $e->getMessage());
            return null;
        }
    }
}
?>

