<?php

require_once __DIR__ . '/../models/FaceAuth.php';
require_once __DIR__ . '/../models/User.php';

class FaceAuthController {
    private $db;
    private $faceAuth;
    
    public function __construct($db) {
        $this->db = $db;
        $this->faceAuth = new FaceAuth($db);
    }
    
    /**
     * Enregistrer le descripteur facial d'un utilisateur
     * Endpoint: POST action=save_face
     */
    public function registerFace() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Non authentifié'
            ]);
            return;
        }
        
        // Récupérer les données JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['descriptor']) || !is_array($input['descriptor'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Descripteur facial invalide'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $descriptor = $input['descriptor'];
        
        // Vérifier que le descripteur a la bonne taille (128 pour face-api.js)
        if (count($descriptor) !== 128) {
            echo json_encode([
                'success' => false,
                'error' => 'Descripteur facial de taille incorrecte'
            ]);
            return;
        }
        
        // Vérifier si ce visage est déjà enregistré par un AUTRE utilisateur
        $existingUser = $this->faceAuth->findUserByFace($descriptor, 0.4); // Seuil plus strict pour l'unicité
        
        if ($existingUser && $existingUser['id'] != $userId) {
            error_log("FaceAuth: Tentative d'enregistrement d'un visage déjà existant (User ID: " . $existingUser['id'] . ") par User ID: " . $userId);
            echo json_encode([
                'success' => false,
                'error' => 'Ce visage est déjà associé à un autre compte.'
            ]);
            return;
        }

        // Sauvegarder le descripteur
        error_log("FaceAuth: Tentative d'enregistrement pour User ID: " . $userId);
        
        if ($this->faceAuth->saveFaceDescriptor($userId, $descriptor)) {
            error_log("FaceAuth: Succès pour User ID: " . $userId);
            echo json_encode([
                'success' => true,
                'message' => 'Visage enregistré avec succès'
            ]);
        } else {
            error_log("FaceAuth: Échec de l'enregistrement pour User ID: " . $userId);
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de l\'enregistrement du visage'
            ]);
        }
    }
    
    /**
     * Vérifier un visage pour la connexion
     * Endpoint: POST action=verify_face
     */
    public function verifyFace() {
        // Définir le Content-Type JSON
        header('Content-Type: application/json');
        
        // Récupérer les données JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['descriptor']) || !is_array($input['descriptor'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Descripteur facial invalide'
            ]);
            return;
        }
        
        $descriptor = $input['descriptor'];
        
        // Vérifier la taille du descripteur
        if (count($descriptor) !== 128) {
            echo json_encode([
                'success' => false,
                'error' => 'Descripteur facial de taille incorrecte'
            ]);
            return;
        }
        
        // Rechercher un utilisateur correspondant
        // Seuil abaissé à 0.45 pour plus de sécurité (réduit les faux positifs)
        $user = $this->faceAuth->findUserByFace($descriptor, 0.45);
        
        if ($user) {
            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Récupérer l'avatar si disponible
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($this->db);
            $userModel->id = $user['id'];
            if ($userModel->readOne()) {
                $_SESSION['avatar'] = $userModel->avatar ?? null;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ],
                'redirect' => 'index.php?controller=formation&action=userDashboard'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Visage non reconnu. Veuillez utiliser votre mot de passe.'
            ]);
        }
    }
    
    /**
     * Supprimer les données faciales d'un utilisateur
     * Endpoint: POST action=remove_face
     */
    public function removeFace() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Non authentifié'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Supprimer le descripteur
        if ($this->faceAuth->deleteFaceDescriptor($userId)) {
            echo json_encode([
                'success' => true,
                'message' => 'Données faciales supprimées avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la suppression des données faciales'
            ]);
        }
    }
    
    /**
     * Activer/désactiver la reconnaissance faciale
     * Endpoint: POST action=toggle_face
     */
    public function toggleFaceAuth() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Non authentifié'
            ]);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['enabled'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Paramètre manquant'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $enabled = (bool)$input['enabled'];
        
        // Mettre à jour le statut
        if ($this->faceAuth->updateFaceStatus($userId, $enabled)) {
            echo json_encode([
                'success' => true,
                'message' => $enabled ? 'Reconnaissance faciale activée' : 'Reconnaissance faciale désactivée'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du statut'
            ]);
        }
    }
    
    /**
     * Obtenir les informations de reconnaissance faciale de l'utilisateur
     * Endpoint: GET action=face_info
     */
    public function getFaceInfo() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Non authentifié'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $info = $this->faceAuth->getFaceInfo($userId);
        
        if ($info !== null) {
            echo json_encode([
                'success' => true,
                'info' => $info
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la récupération des informations'
            ]);
        }
    }
    
    /**
     * Afficher la page d'enregistrement du visage
     */
    public function showRegistrationPage() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        require_once __DIR__ . '/../views/front/face_registration.php';
    }
    
    /**
     * Afficher la page de connexion par reconnaissance faciale
     */
    public function showFaceLoginPage() {
        if (file_exists(__DIR__ . '/../views/front/face_login.php')) {
            include __DIR__ . '/../views/front/face_login.php';
        } else {
            echo "Face login page not found";
        }
    }
}

?>
