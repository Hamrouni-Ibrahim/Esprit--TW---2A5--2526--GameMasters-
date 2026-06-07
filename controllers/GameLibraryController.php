<?php
require_once "models/GameLibrary.php";

class GameLibraryController {
    
    // List all games
    public function list() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour accéder à la bibliothèque de jeux.";
            header("Location: ?action=login");
            exit;
        }
        
        $gameLibrary = new GameLibrary();
        
        // Get filter parameters
        $category = $_GET['category'] ?? null;
        $difficulty = $_GET['difficulty'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Get games based on filters
        if ($search) {
            $games = $gameLibrary->search($search);
        } else {
            $games = $gameLibrary->getAll($category, $difficulty);
        }
        
        // Get categories for filter
        $categories = $gameLibrary->getCategories();
        
        include "views/front/games_library.php";
    }
    
    // Play a specific game
    public function play() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour jouer aux jeux.";
            header("Location: ?action=login");
            exit;
        }
        
        $gameId = $_GET['id'] ?? null;
        
        if (!$gameId) {
            $_SESSION['error_message'] = "Jeu non spécifié.";
            header("Location: ?controller=gameLibrary&action=list");
            exit;
        }
        
        $gameLibrary = new GameLibrary();
        $game = $gameLibrary->getById($gameId);
        
        if (!$game) {
            $_SESSION['error_message'] = "Jeu non trouvé ou non disponible.";
            header("Location: ?controller=gameLibrary&action=list");
            exit;
        }
        
        include "views/front/game_play.php";
    }
}
?>





