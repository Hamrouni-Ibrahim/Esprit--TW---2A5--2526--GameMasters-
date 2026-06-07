<?php
require_once "models/Favorite.php";

class FavoriteController {
    
    /**
     * Get user ID from authenticated session
     * Uses real authentication system
     */
    private function getUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Use real authenticated user ID if available
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        // Fallback to temp user ID for guests (if you want to allow guest favorites)
        // Otherwise, return null to require login
        if (!isset($_SESSION['temp_user_id']) || $_SESSION['temp_user_id'] > 2147483647) {
            $_SESSION['temp_user_id'] = crc32(session_id()) & 0x7FFFFFFF;
        }
        
        file_put_contents('debug_favorites.log', date('Y-m-d H:i:s') . " - FavoriteController UserID: " . ($_SESSION['user_id'] ?? $_SESSION['temp_user_id']) . " SessionID: " . session_id() . "\n", FILE_APPEND);
        
        // Return null if not logged in (you can change this to allow guest favorites)
        return $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
    }

    /**
     * Toggle formation favorite
     */
    /**
     * Toggle formation favorite
     */
    public function toggleFormation($formation_id) {
        $user_id = $this->getUserId();
        $favorite = new Favorite();
        
        $success = false;
        $message = "";
        $is_favorite = false;

        file_put_contents('debug_favorites.log', date('Y-m-d H:i:s') . " - ToggleFormation: ID=$formation_id User=$user_id\n", FILE_APPEND);

        if ($favorite->isFormationFavorite($user_id, $formation_id)) {
            if ($favorite->removeFormation($user_id, $formation_id)) {
                $message = "Formation retirée des favoris";
                $is_favorite = false;
                $success = true;
            } else {
                $message = "Erreur lors du retrait des favoris";
                $success = false;
            }
        } else {
            if ($favorite->addFormation($user_id, $formation_id)) {
                $message = "Formation ajoutée aux favoris";
                $is_favorite = true;
                $success = true;
            } else {
                $message = "Erreur lors de l'ajout aux favoris";
                $success = false;
            }
        }
        
        // Return JSON response for AJAX
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'is_favorite' => $is_favorite
        ]);
        exit;
    }

    /**
     * Toggle education favorite
     */
    /**
     * Toggle education favorite
     */
    public function toggleEducation($education_id) {
        $user_id = $this->getUserId();
        $favorite = new Favorite();
        
        $success = false;
        $message = "";
        $is_favorite = false;

        if ($favorite->isEducationFavorite($user_id, $education_id)) {
            if ($favorite->removeEducation($user_id, $education_id)) {
                $message = "Éducation retirée des favoris";
                $is_favorite = false;
                $success = true;
            } else {
                $message = "Erreur lors du retrait des favoris";
                $success = false;
            }
        } else {
            if ($favorite->addEducation($user_id, $education_id)) {
                $message = "Éducation ajoutée aux favoris";
                $is_favorite = true;
                $success = true;
            } else {
                $message = "Erreur lors de l'ajout aux favoris";
                $success = false;
            }
        }
        
        // Return JSON response for AJAX
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'is_favorite' => $is_favorite
        ]);
        exit;
    }

    /**
     * Display favorites page
     */
    public function list() {
        $user_id = $this->getUserId();
        $favorite = new Favorite();
        
        $favoriteFormations = $favorite->getUserFavoriteFormations($user_id);
        $favoriteEducations = $favorite->getUserFavoriteEducations($user_id);
        
        // Ensure arrays are initialized
        if (!is_array($favoriteFormations)) {
            $favoriteFormations = [];
        }
        if (!is_array($favoriteEducations)) {
            $favoriteEducations = [];
        }
        
        include "views/front/favorites.php";
    }
}

