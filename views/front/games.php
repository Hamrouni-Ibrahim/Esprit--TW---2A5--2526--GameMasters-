<?php
// Initialize GameController if not already set
if (!isset($gameController)) {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Game.php';
    require_once __DIR__ . '/../../controllers/GameController.php';
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        $gameController = new GameController($db);
    } catch (Exception $e) {
        die("Erreur de connexion: " . $e->getMessage());
    }
}

// Set page variables for header
$pageTitle = 'Jeux - Game Master';
$currentPage = 'games';

// Include main site header
include "views/front/includes/header.php";
?>
    <style>
        /* === CORRECTIONS SPÉCIFIQUES POUR LA PAGE JEUX === */
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .logo-gaming {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            max-width: 200px;
            flex-shrink: 0;
        }

        .logo-image {
            width: 45px !important;
            height: 45px !important;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(0, 255, 204, 0.3);
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);
            flex-shrink: 0;
        }

        .logo-text-gaming {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #ff6b6b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
            position: relative;
            white-space: nowrap;
        }

        .admin-btn {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            padding: 8px 20px;
            border-radius: 8px;
            color: white !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(147, 51, 234, 0.4);
        }

        .logout-btn {
            color: #ff6b6b !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            color: #ff4757 !important;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
        }

        .games-section {
            padding: 80px 50px;
            background: linear-gradient(180deg, #0f1329 0%, #1a1f3a 100%);
        }

        .games-hero {
            text-align: center;
            padding: 100px 20px 60px;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
            position: relative;
            overflow: hidden;
        }

        .games-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 204, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
        }

        .games-hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .games-hero p {
            font-size: 1.3em;
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .game-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .game-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #00ffcc, #ff6b6b, #00ccff);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .game-card:hover {
            transform: translateY(-10px);
            border-color: rgba(0, 255, 204, 0.3);
            box-shadow: 0 15px 40px rgba(0, 255, 204, 0.15);
        }

        .game-card:hover::before {
            opacity: 1;
        }

        .game-image-container {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .game-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            display: block;
        }

        .game-card:hover .game-image {
            transform: scale(1.05);
        }

        .game-content {
            padding: 25px;
        }

        .game-title {
            color: #00ffcc;
            font-size: 1.5em;
            margin-bottom: 12px;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(0, 255, 204, 0.8), 0 0 40px rgba(0, 255, 204, 0.5), 0 0 60px rgba(0, 255, 204, 0.3);
            filter: blur(0.5px);
            letter-spacing: 1px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .game-title:hover {
            text-shadow: 0 0 30px rgba(0, 255, 204, 1), 0 0 60px rgba(0, 255, 204, 0.7), 0 0 90px rgba(0, 255, 204, 0.4);
            filter: blur(0.3px);
            transform: scale(1.02);
        }
        
        .game-title a {
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #00ffcc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }
        
        @keyframes gradient-shift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .game-description {
            color: #a0a0a0;
            font-size: 0.95em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .game-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .game-feature {
            background: rgba(0, 255, 204, 0.1);
            color: #00ffcc;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            border: 1px solid rgba(0, 255, 204, 0.2);
        }

        /* CORRECTIONS VIDÉOS */
        .video-container {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 15px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: #000;
        }

        .video-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(0, 255, 204, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .video-container:hover::before {
            opacity: 1;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
            border-radius: 12px;
            background: #000;
        }

        .video-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
        }

        .video-container:hover video,
        .video-container:hover iframe {
            transform: scale(1.02);
        }

        .video-placeholder {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            margin-top: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px dashed rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        .games-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 60px 0 40px;
        }

        .games-stat {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .games-stat:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 255, 204, 0.3);
        }

        .games-stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #00ffcc;
            margin-bottom: 8px;
        }

        .games-stat-label {
            color: #a0a0a0;
            font-size: 0.9em;
        }

        .footer-gaming {
            background: #0a0e27;
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .footer-logo img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }

        .footer-logo span {
            font-size: 1.3em;
            font-weight: 700;
            background: linear-gradient(135deg, #00ffcc, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 968px) {
            .nav-links {
                gap: 25px;
            }
            
            .logo-text-gaming {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .logo-image {
                width: 35px !important;
                height: 35px !important;
            }
            
            .logo-text-gaming {
                font-size: 18px;
            }
            
            .nav-links {
                display: none;
            }
            
            .hamburger {
                display: flex;
            }

            .games-hero h1 {
                font-size: 2.5em;
            }
            
            .games-hero p {
                font-size: 1.1em;
            }
            
            .games-grid {
                grid-template-columns: 1fr;
            }
            
            .games-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .game-image-container {
                height: 200px;
            }
        }

        @media (max-width: 480px) {
            .games-section {
                padding: 40px 20px;
            }
            
            .games-stats {
                grid-template-columns: 1fr;
            }

            .game-image-container {
                height: 180px;
            }

            .logo-text-gaming {
                font-size: 16px;
            }
        }

        /* Modal Styles */
        .game-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
        }

        .game-modal.active {
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .game-modal-content {
            background: linear-gradient(135deg, #1a1f3a 0%, #0f1329 100%);
            border: 2px solid rgba(0, 255, 204, 0.3);
            border-radius: 20px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 255, 204, 0.3);
            animation: slideUp 0.4s ease;
        }

        .game-modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .game-modal-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .game-modal-content::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 204, 0.5);
            border-radius: 10px;
        }

        .game-modal-content::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 255, 204, 0.7);
        }

        .game-modal-header {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #1a1f3a 0%, #0f1329 100%);
            padding: 25px 30px;
            border-bottom: 1px solid rgba(0, 255, 204, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .game-modal-title {
            color: #00ffcc;
            font-size: 2em;
            margin: 0;
            font-weight: 700;
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .game-modal-close {
            color: #aaa;
            font-size: 2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .game-modal-close:hover,
        .game-modal-close:focus {
            color: #00ffcc;
            transform: rotate(90deg);
            background: rgba(0, 255, 204, 0.1);
        }

        .game-modal-body {
            padding: 30px;
        }

        .game-modal-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 2px solid rgba(0, 255, 204, 0.2);
        }

        .game-modal-category {
            color: #00ffcc;
            font-size: 1.2em;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .game-modal-description {
            color: #cccccc;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .game-modal-impact {
            background: rgba(0, 255, 204, 0.1);
            border-left: 4px solid #00ffcc;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .game-modal-impact strong {
            color: #00ffcc;
            display: block;
            margin-bottom: 8px;
        }

        .game-modal-impact p {
            color: #cccccc;
            margin: 0;
        }

        .game-modal-video {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
            border: 2px solid rgba(0, 255, 204, 0.2);
        }

        .game-modal-video iframe,
        .game-modal-video video {
            width: 100%;
            height: 100%;
            border: none;
        }

        .game-modal-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .game-modal-feature {
            background: rgba(0, 255, 204, 0.15);
            color: #00ffcc;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            border: 1px solid rgba(0, 255, 204, 0.3);
        }

        .game-modal-rating {
            margin-bottom: 25px;
        }

        .game-card-clickable {
            cursor: pointer;
        }

        .game-card-clickable:hover {
            transform: translateY(-10px) !important;
        }

        @media (max-width: 768px) {
            .game-modal-content {
                max-width: 95%;
                max-height: 95vh;
            }

            .game-modal-title {
                font-size: 1.5em;
            }

            .game-modal-body {
                padding: 20px;
            }
        }

        /* Fix for select options readability */
        select option {
            color: #000000 !important;
            background-color: #ffffff !important;
        }
    </style>

<!-- Content Section for Games -->
<section class="content-section">
    <div class="content-bg"></div>
    <div class="content-shapes">
        <div class="content-shape shape1"></div>
        <div class="content-shape shape2"></div>
        <div class="content-shape shape3"></div>
        <div class="content-shape shape4"></div>
        <div class="content-shape shape5"></div>
        <div class="content-shape shape6"></div>
    </div>
    <div class="content-particles" id="contentParticles"></div>
    <div class="content-container">
        <!-- Bouton Ajouter un Jeu (utilisateurs connectés sauf admin) -->
        <?php 
        if(isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')): 
        ?>
            <div style="text-align: center; padding: 30px 20px; background: linear-gradient(135deg, rgba(0, 255, 204, 0.1), rgba(0, 204, 255, 0.05)); border: 2px solid rgba(0, 255, 204, 0.2); border-radius: 15px; margin-bottom: 40px;">
                <a href="?action=add_game" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0e27; text-decoration: none; border-radius: 25px; font-weight: 700; font-size: 1.1em; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(0, 255, 204, 0.3);">
                    ➕ Ajouter un Jeu
                </a>
            </div>
        <?php 
        endif;
        ?>

        <!-- Hero Section Jeux -->
        <div style="text-align: center; padding: 60px 20px 40px; margin-bottom: 40px;">
            <h1 style="color: #00ffcc; font-size: 2.5em; margin-bottom: 20px;">Jeux à Impact Social</h1>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 1.2em; max-width: 800px; margin: 0 auto 30px;">Découvrez nos jeux qui allient divertissement et changement positif. Chaque aventure est une opportunité d'apprendre et d'agir.</p>
            
            <?php
            // Fetch categories for the filter
            $categories = [];
            try {
                $catQuery = "SELECT id, name FROM game_categories ORDER BY name";
                $catStmt = $db->prepare($catQuery);
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                // Ignore error, empty categories
            }

            // Handle search parameters
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $categoryId = isset($_GET['category_id']) && !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $rating = isset($_GET['rating']) && !empty($_GET['rating']) ? (int)$_GET['rating'] : null;

            // Perform search if any filter is active
            if (!empty($searchQuery) || !empty($categoryId) || !empty($rating)) {
                $publishedGames = $gameController->searchGames($searchQuery, $categoryId, $rating);
            } else {
                $publishedGames = $gameController->getPublishedGames();
            }
            ?>
            
            <!-- Search Form for Games -->
            <div style="max-width: 900px; margin: 0 auto; background: rgba(0, 255, 204, 0.05); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 15px; padding: 25px;">
                <form method="GET" action="" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: center;">
                    <input type="hidden" name="action" value="games">
                    
                    <!-- Search Input -->
                    <div style="flex: 1 1 300px;">
                        <input type="text" name="search" 
                               value="<?php echo htmlspecialchars($searchQuery); ?>" 
                               placeholder="Rechercher un jeu..." 
                               style="width: 100%; padding: 12px 20px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 10px; color: #fff; font-size: 14px; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#00ffcc'; this.style.background='rgba(255, 255, 255, 0.08)';"
                               onblur="this.style.borderColor='rgba(0, 255, 204, 0.3)'; this.style.background='rgba(255, 255, 255, 0.05)';">
                    </div>

                    <!-- Category Filter -->
                    <div style="flex: 1 1 200px;">
                        <select name="category_id" style="width: 100%; padding: 12px 20px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 10px; color: #fff; font-size: 14px; box-sizing: border-box; cursor: pointer;">
                            <option value="">Toutes les catégories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Rating Filter -->
                    <div style="flex: 1 1 150px;">
                        <select name="rating" style="width: 100%; padding: 12px 20px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 10px; color: #fff; font-size: 14px; box-sizing: border-box; cursor: pointer;">
                            <option value="">Toutes les notes</option>
                            <option value="5" <?php echo ($rating == 5) ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4" <?php echo ($rating == 4) ? 'selected' : ''; ?>>⭐⭐⭐⭐ (4)</option>
                            <option value="3" <?php echo ($rating == 3) ? 'selected' : ''; ?>>⭐⭐⭐ (3)</option>
                            <option value="2" <?php echo ($rating == 2) ? 'selected' : ''; ?>>⭐⭐ (2)</option>
                            <option value="1" <?php echo ($rating == 1) ? 'selected' : ''; ?>>⭐ (1)</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" style="padding: 12px 25px; background: linear-gradient(135deg, #00ffcc, #00ccff); border: none; border-radius: 10px; color: #0a0e27; font-weight: 600; cursor: pointer; transition: all 0.3s; white-space: nowrap;" 
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 255, 204, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        🔍 Rechercher
                    </button>
                    
                    <?php if (!empty($searchQuery) || !empty($categoryId) || !empty($rating)): ?>
                        <a href="?action=games" style="padding: 12px 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; white-space: nowrap;">
                            ✕ Effacer
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <?php if (!empty($searchQuery) || !empty($categoryId) || !empty($rating)): ?>
                <div style="text-align: center; color: #00ffcc; margin-top: 20px; font-size: 1.1em; background: rgba(0, 255, 204, 0.1); display: inline-block; padding: 10px 20px; border-radius: 20px;">
                    Filtres actifs: 
                    <?php 
                    $filters = [];
                    if (!empty($searchQuery)) $filters[] = "Recherche: <strong>" . htmlspecialchars($searchQuery) . "</strong>";
                    if (!empty($categoryId)) {
                        foreach($categories as $cat) {
                            if ($cat['id'] == $categoryId) {
                                $filters[] = "Catégorie: <strong>" . htmlspecialchars($cat['name']) . "</strong>";
                                break;
                            }
                        }
                    }
                    if (!empty($rating)) $filters[] = "Note: <strong>" . $rating . " étoiles</strong>";
                    echo implode(' | ', $filters);
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistiques -->
        <div class="games-section" style="padding: 40px 0;">
        <div class="dashboard-container">
            <?php

            $allGames = $gameController->index();
            
            $publishedCount = count($publishedGames);
            $developmentCount = 0;
            $totalCount = count($allGames);
            
            foreach ($allGames as $game) {
                if ($game['status'] === 'development') {
                    $developmentCount++;
                }
            }
            ?>
            
            <div class="games-stats">
                <div class="games-stat">
                    <div class="games-stat-value"><?php echo $totalCount; ?>+</div>
                    <div class="games-stat-label">Jeux Engagés</div>
                </div>
                <div class="games-stat">
                    <div class="games-stat-value"><?php echo $publishedCount; ?></div>
                    <div class="games-stat-label">Jeux Publiés</div>
                </div>
                <div class="games-stat">
                    <div class="games-stat-value"><?php echo $developmentCount; ?></div>
                    <div class="games-stat-label">En Développement</div>
                </div>
                <div class="games-stat">
                    <div class="games-stat-value">4.8★</div>
                    <div class="games-stat-label">Note Communauté</div>
                </div>
            </div>

            <!-- Jeux UNIQUEMENT de la base de données -->
            <div class="games-grid">
                <?php foreach ($publishedGames as $game): ?>
                            <?php 
                    // Prepare game data for modal
                            $imageUrl = $game['image_url'];
                            
                            if (strpos($imageUrl, '/game-masters/') === 0) {
                                $finalImageUrl = $imageUrl;
                            } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                $finalImageUrl = $imageUrl;
                            } else {
                                $gameName = strtolower($game['name']);
                                
                                if (strpos($gameName, 'kind') !== false || strpos($gameName, 'words') !== false) {
                                    $finalImageUrl = '/game-masters/public/assets/img/Kind Words.jpg';
                                } elseif (strpos($gameName, 'terra') !== false || strpos($gameName, 'nil') !== false) {
                                    $finalImageUrl = '/game-masters/public/assets/img/Terra Nil.jpg';
                                } elseif (strpos($gameName, 'hacknet') !== false || strpos($gameName, 'hack') !== false) {
                                    $finalImageUrl = '/game-masters/public/assets/img/Hacknet.jpg';
                                } elseif (strpos($gameName, 'tell me why') !== false || strpos($gameName, 'tell me') !== false || strpos($gameName, 'why') !== false) {
                                    $finalImageUrl = '/game-masters/public/assets/img/Tell Me Why.jpg';
                                } elseif (strpos($gameName, 'sky') !== false || strpos($gameName, 'children') !== false || strpos($gameName, 'light') !== false) {
                                    $finalImageUrl = '/game-masters/public/assets/img/Sky Children of the Light.jpg';
                                } else {
                                    $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                                }
                            }

                    // Prepare video URL
                    $videoUrl = $game['demo_url'];
                    $embedVideoUrl = '';
                    
                    if (!empty($videoUrl)) {
                        if (strpos($videoUrl, 'youtube.com/embed') !== false) {
                            $embedVideoUrl = $videoUrl;
                        } elseif (strpos($videoUrl, 'youtube.com/watch') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                            $videoId = '';
                            if (preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $videoUrl, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('/youtu\\.be\\/([^\\?\\&]+)/', $videoUrl, $matches)) {
                                $videoId = $matches[1];
                            }
                            if (!empty($videoId)) {
                                $embedVideoUrl = 'https://www.youtube.com/embed/' . $videoId;
                            }
                        } elseif (strpos($videoUrl, '/game-masters/public/assets/videos/') === 0 || filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                            $embedVideoUrl = $videoUrl; // Local video
                        }
                    } else {
                        // Default videos
                        $gameName = strtolower($game['name']);
                        if (strpos($gameName, 'kind') !== false || strpos($gameName, 'words') !== false) {
                            $embedVideoUrl = 'https://www.youtube.com/embed/gC1JnfWvsLM';
                        } elseif (strpos($gameName, 'terra') !== false || strpos($gameName, 'nil') !== false) {
                            $embedVideoUrl = 'https://www.youtube.com/embed/F8eYqNNxICE';
                        } elseif (strpos($gameName, 'hacknet') !== false || strpos($gameName, 'hack') !== false) {
                            $embedVideoUrl = 'https://www.youtube.com/embed/FgDmU7Pb96Y';
                        } elseif (strpos($gameName, 'tell me why') !== false || strpos($gameName, 'tell me') !== false || strpos($gameName, 'why') !== false) {
                            $embedVideoUrl = 'https://www.youtube.com/embed/xa_SN5ZOUXc';
                        } elseif (strpos($gameName, 'sky') !== false || strpos($gameName, 'children') !== false || strpos($gameName, 'light') !== false) {
                            $embedVideoUrl = 'https://www.youtube.com/embed/g3r1KbzSiT8';
                        }
                    }

                    // Prepare features
                    $impact = strtolower($game['impact_social'] ?? '');
                    $features = [];
                    
                    if (strpos($impact, 'santé') !== false || strpos($impact, 'mental') !== false || strpos($impact, 'émotion') !== false) {
                        $features[] = 'Santé Mentale';
                    }
                    if (strpos($impact, 'écolog') !== false || strpos($impact, 'environnement') !== false || strpos($impact, 'climat') !== false) {
                        $features[] = 'Écologie';
                    }
                    if (strpos($impact, 'inclus') !== false || strpos($impact, 'divers') !== false || strpos($impact, 'égalité') !== false) {
                        $features[] = 'Inclusion';
                    }
                    if (strpos($impact, 'cyber') !== false || strpos($impact, 'sécurité') !== false || strpos($impact, 'hack') !== false) {
                        $features[] = 'Cybersécurité';
                    }
                    if (strpos($impact, 'bien-être') !== false || strpos($impact, 'social') !== false || strpos($impact, 'communauté') !== false) {
                        $features[] = 'Bien-être';
                    }
                    if (strpos($impact, 'éduc') !== false || strpos($impact, 'apprentissage') !== false) {
                        $features[] = 'Éducation';
                    }
                    if (strpos($impact, 'stratégie') !== false) {
                        $features[] = 'Stratégie';
                    }
                    if (strpos($impact, 'aventure') !== false) {
                        $features[] = 'Aventure';
                    }
                    if (strpos($impact, 'narratif') !== false) {
                        $features[] = 'Narratif';
                    }
                    
                    if (empty($features)) {
                        $features = ['Impact Social', 'Engagement'];
                    }
                    ?>
                    <div class="game-card game-card-clickable" 
                         data-game-id="<?php echo $game['id']; ?>"
                         data-game-name="<?php echo htmlspecialchars($game['name'], ENT_QUOTES); ?>"
                         data-game-description="<?php echo htmlspecialchars($game['description'], ENT_QUOTES); ?>"
                         data-game-impact="<?php echo htmlspecialchars($game['impact_social'] ?? '', ENT_QUOTES); ?>"
                         data-game-category="<?php echo htmlspecialchars($game['category_name'] ?? '', ENT_QUOTES); ?>"
                         data-game-image="<?php echo htmlspecialchars($finalImageUrl, ENT_QUOTES); ?>"
                         data-game-video="<?php echo htmlspecialchars($embedVideoUrl, ENT_QUOTES); ?>"
                         data-game-features="<?php echo htmlspecialchars(json_encode($features), ENT_QUOTES); ?>"
                         data-game-rating-avg="<?php echo $game['rating_average'] ?? 0; ?>"
                         data-game-rating-count="<?php echo $game['rating_count'] ?? 0; ?>"
                         data-game-user-rating="<?php echo $game['user_rating'] ?? 0; ?>">
                        <div class="game-image-container">
                                <img src="<?php echo $finalImageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($game['name']); ?>" 
                                     class="game-image"
                                     onerror="this.src='/game-masters/public/assets/img/dev1.jpg'">
                        </div>
                        
                        <div class="game-content">
                            <h3 class="game-title">
                                    <?php echo htmlspecialchars($game['name']); ?>
                            </h3>
                            
                            <?php if (!empty($game['category_name'])): ?>
                                <div style="color: #00ffcc; font-size: 1.5em; margin-bottom: 12px; font-weight: 700;">
                                    📁 <?php echo htmlspecialchars($game['category_name']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Features dynamiques -->
                            <div class="game-features">
                                <?php
                                $impact = strtolower($game['impact_social']);
                                $features = [];
                                
                                if (strpos($impact, 'santé') !== false || strpos($impact, 'mental') !== false || strpos($impact, 'émotion') !== false) {
                                    $features[] = 'Santé Mentale';
                                }
                                if (strpos($impact, 'écolog') !== false || strpos($impact, 'environnement') !== false || strpos($impact, 'climat') !== false) {
                                    $features[] = 'Écologie';
                                }
                                if (strpos($impact, 'inclus') !== false || strpos($impact, 'divers') !== false || strpos($impact, 'égalité') !== false) {
                                    $features[] = 'Inclusion';
                                }
                                if (strpos($impact, 'cyber') !== false || strpos($impact, 'sécurité') !== false || strpos($impact, 'hack') !== false) {
                                    $features[] = 'Cybersécurité';
                                }
                                if (strpos($impact, 'bien-être') !== false || strpos($impact, 'social') !== false || strpos($impact, 'communauté') !== false) {
                                    $features[] = 'Bien-être';
                                }
                                if (strpos($impact, 'éduc') !== false || strpos($impact, 'apprentissage') !== false) {
                                    $features[] = 'Éducation';
                                }
                                if (strpos($impact, 'stratégie') !== false) {
                                    $features[] = 'Stratégie';
                                }
                                if (strpos($impact, 'aventure') !== false) {
                                    $features[] = 'Aventure';
                                }
                                if (strpos($impact, 'narratif') !== false) {
                                    $features[] = 'Narratif';
                                }
                                
                                if (empty($features)) {
                                    $features = ['Impact Social', 'Engagement'];
                                }
                                
                                foreach ($features as $feature): ?>
                                    <span class="game-feature"><?php echo $feature; ?></span>
                                <?php endforeach; ?>
                            </div>

                            <!-- Système de notation -->
                            <div class="rating-container" data-game-id="<?php echo $game['id']; ?>" data-user-rating="<?php echo $game['user_rating'] ?? 0; ?>" data-is-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>" style="margin-bottom: 15px;">
                                <div class="stars" style="color: #ffd700; font-size: 1.4em; user-select: none; display: inline-block;">
                                    <?php 
                                    $userRating = $game['user_rating'] ?? 0;
                                    $averageRating = $game['rating_average'] ?? 0;
                                    $ratingCount = $game['rating_count'] ?? 0;
                                    $isLoggedIn = isset($_SESSION['user_id']);
                                    
                                    for ($i = 1; $i <= 5; $i++) {
                                        $cursorStyle = $isLoggedIn ? 'cursor: pointer;' : 'cursor: not-allowed; opacity: 0.6;';
                                        echo '<span class="star" data-value="' . $i . '" style="' . $cursorStyle . ' display: inline-block; transition: transform 0.2s; margin: 0 1px;">' . ($i <= $userRating ? '★' : '☆') . '</span>';
                                    }
                                    ?>
                                </div>
                                <?php if (!$isLoggedIn): ?>
                                    <div style="font-size: 0.75em; color: #ffaa00; margin-top: 5px;">
                                        <a href="?action=login" style="color: #00ffcc; text-decoration: underline;">Connectez-vous</a> pour noter ce jeu
                                    </div>
                                <?php else: ?>
                                    <button class="rate-btn" style="display: none; margin-top: 8px; background: linear-gradient(135deg, #00ffcc, #00b8e6); color: #000; border: none; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 0.85em; box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3); transition: all 0.3s;">Noter</button>
                                <?php endif; ?>
                                <div class="rating-info" style="font-size: 0.85em; color: #aaa; margin-top: 8px;">
                                    <span class="average"><?php echo $averageRating > 0 ? $averageRating : '-'; ?></span>/5 
                                    (<span class="count"><?php echo $ratingCount; ?></span> avis)
                                </div>
                            </div>
                            
                            <p class="game-description" style="max-height: 100px; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($game['description']); ?>
                            </p>
                            
                            <div style="color: #00ffcc; font-size: 0.9em; margin-top: 15px; font-weight: 600;">
                                👆 Cliquez pour voir plus
                                </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($publishedGames) === 0): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <h3 style="color: #00ffcc; margin-bottom: 20px; font-size: 1.8em;">
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            Aucun jeu trouvé pour "<?php echo htmlspecialchars($_GET['search']); ?>"
                        <?php else: ?>
                            Aucun jeu publié pour le moment
                        <?php endif; ?>
                    </h3>
                    <p style="color: #cccccc; font-size: 1.1em; margin-bottom: 30px;">
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            Essayez avec d'autres mots-clés ou <a href="?action=games" style="color: #00ffcc;">affichez tous les jeux</a>.
                        <?php else: ?>
                        Les jeux ajoutés via l'administration apparaîtront ici une fois publiés.
                        <?php endif; ?>
                    </p>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="?action=add_game" class="cta-button" style="font-size: 1.1em; padding: 12px 30px;">
                            Ajouter des Jeux
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Bouton pour ajouter un jeu (tous les utilisateurs connectés sauf admin) -->
            <?php 
            if(isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')): 
            ?>
                <div style="text-align: center; margin: 40px 0; padding: 30px; background: rgba(0, 255, 204, 0.05); border: 2px dashed rgba(0, 255, 204, 0.3); border-radius: 15px;">
                    <h3 style="color: #00ffcc; margin-bottom: 15px; font-size: 1.5em;">🎮 Vous avez une idée de jeu ?</h3>
                    <p style="color: #cccccc; font-size: 1em; margin-bottom: 25px;">
                        Partagez votre jeu avec la communauté ! Votre soumission sera examinée par un administrateur.
                    </p>
                    <a href="?action=add_game" class="cta-button" style="font-size: 1.1em; padding: 12px 30px; background: linear-gradient(135deg, #00ffcc, #00ccff);">
                        ➕ Ajouter un Jeu
                    </a>
                </div>
            <?php 
            endif;
            ?>

            <div style="text-align: center; margin-top: 60px; padding: 40px; background: rgba(255, 255, 255, 0.03); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1);">
                <h2 style="color: #00ffcc; margin-bottom: 20px; font-size: 2em;">Prêt à Jouer Utile ?</h2>
                <p style="color: #cccccc; font-size: 1.1em; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Rejoignez notre communauté de joueurs engagés et découvrez comment le gaming peut avoir un impact positif sur le monde.
                </p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="?action=register" class="cta-button" style="font-size: 1.1em; padding: 15px 40px;">Rejoindre l'Aventure</a>
                <?php else: ?>
                    <a href="?controller=formation&action=userDashboard" class="cta-button" style="font-size: 1.1em; padding: 15px 40px;">Retour à l'Accueil</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Game Modal -->
<div id="gameModal" class="game-modal">
    <div class="game-modal-content">
        <div class="game-modal-header">
            <h2 class="game-modal-title" id="modalGameTitle"></h2>
            <span class="game-modal-close" id="modalClose">&times;</span>
        </div>
        <div class="game-modal-body">
            <img id="modalGameImage" class="game-modal-image" src="" alt="">
            
            <div id="modalGameCategory" class="game-modal-category"></div>
            
            <div id="modalGameFeatures" class="game-modal-features"></div>
            
            <div id="modalGameRating" class="game-modal-rating"></div>
            
            <div id="modalGameDescription" class="game-modal-description"></div>
            
            <div id="modalGameImpact" class="game-modal-impact"></div>
            
            <div id="modalGameVideo" class="game-modal-video"></div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gameCards = document.querySelectorAll('.game-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            gameCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Modal functionality
            const modal = document.getElementById('gameModal');
            const modalClose = document.getElementById('modalClose');
            
            console.log('Modal element:', modal);
            console.log('Modal close element:', modalClose);
            
            if (!modal) {
                console.error('Game modal not found! Make sure the modal HTML is in the page.');
            } else {
                const modalTitle = document.getElementById('modalGameTitle');
                const modalImage = document.getElementById('modalGameImage');
                const modalCategory = document.getElementById('modalGameCategory');
                const modalFeatures = document.getElementById('modalGameFeatures');
                const modalRating = document.getElementById('modalGameRating');
                const modalDescription = document.getElementById('modalGameDescription');
                const modalImpact = document.getElementById('modalGameImpact');
                const modalVideo = document.getElementById('modalGameVideo');

                // Function to convert YouTube URL to embed
                function convertToEmbedUrl(url) {
                    if (!url) return '';
                    
                    // Already an embed URL
                    if (url.includes('youtube.com/embed')) {
                        return url;
                    }
                    
                    // Extract video ID from various YouTube URL formats
                    let videoId = '';
                    if (url.includes('youtube.com/watch?v=')) {
                        videoId = url.split('v=')[1].split('&')[0];
                    } else if (url.includes('youtu.be/')) {
                        videoId = url.split('youtu.be/')[1].split('?')[0];
                    }
                    
                    if (videoId) {
                        return 'https://www.youtube.com/embed/' + videoId;
                    }
                    
                    return url;
                }

                // Function to open modal with game data
                function openGameModal(card) {
                    if (!modal) return;
                    
                    const gameId = card.dataset.gameId;
                    const gameName = card.dataset.gameName;
                    const gameDescription = card.dataset.gameDescription;
                    const gameImpact = card.dataset.gameImpact;
                    const gameCategory = card.dataset.gameCategory;
                    const gameImage = card.dataset.gameImage;
                    const gameVideo = card.dataset.gameVideo;
                    const gameFeatures = JSON.parse(card.dataset.gameFeatures || '[]');
                    const ratingAvg = parseFloat(card.dataset.gameRatingAvg || 0);
                    const ratingCount = parseInt(card.dataset.gameRatingCount || 0);
                    const userRating = parseInt(card.dataset.gameUserRating || 0);

                    // Populate modal
                    if (modalTitle) modalTitle.textContent = gameName;
                    if (modalImage) {
                        modalImage.src = gameImage;
                        modalImage.alt = gameName;
                    }
                    
                    if (modalCategory) {
                        if (gameCategory) {
                            modalCategory.innerHTML = '📁 ' + gameCategory;
                            modalCategory.style.display = 'block';
                        } else {
                            modalCategory.style.display = 'none';
                        }
                    }

                    // Features
                    if (modalFeatures) {
                        modalFeatures.innerHTML = '';
                        gameFeatures.forEach(feature => {
                            const featureSpan = document.createElement('span');
                            featureSpan.className = 'game-modal-feature';
                            featureSpan.textContent = feature;
                            modalFeatures.appendChild(featureSpan);
                        });
                    }

                    // Rating
                    if (modalRating) {
                        modalRating.innerHTML = '';
                        if (ratingCount > 0) {
                            const ratingDiv = document.createElement('div');
                            ratingDiv.style.cssText = 'color: #ffd700; font-size: 1.2em; margin-bottom: 10px;';
                            ratingDiv.innerHTML = '⭐ ' + ratingAvg.toFixed(1) + '/5 (' + ratingCount + ' avis)';
                            modalRating.appendChild(ratingDiv);
                        }
                    }

                    // Description
                    if (modalDescription) modalDescription.textContent = gameDescription;

                    // Impact
                    if (modalImpact) {
                        if (gameImpact) {
                            modalImpact.innerHTML = '<strong>Impact Social :</strong><p>' + gameImpact + '</p>';
                            modalImpact.style.display = 'block';
                        } else {
                            modalImpact.style.display = 'none';
                        }
                    }

                    // Video
                    if (modalVideo) {
                        modalVideo.innerHTML = '';
                        if (gameVideo) {
                            const embedUrl = convertToEmbedUrl(gameVideo);
                            
                            if (embedUrl.includes('youtube.com/embed')) {
                                // YouTube video
                                const iframe = document.createElement('iframe');
                                iframe.src = embedUrl;
                                iframe.setAttribute('frameborder', '0');
                                iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
                                iframe.setAttribute('allowfullscreen', '');
                                modalVideo.appendChild(iframe);
                            } else if (embedUrl.includes('/game-masters/public/assets/videos/') || embedUrl.startsWith('http')) {
                                // Local or external video
                                const video = document.createElement('video');
                                video.controls = true;
                                video.style.width = '100%';
                                video.style.height = '100%';
                                
                                const source = document.createElement('source');
                                source.src = embedUrl;
                                source.type = 'video/mp4';
                                video.appendChild(source);
                                
                                const source2 = document.createElement('source');
                                source2.src = embedUrl;
                                source2.type = 'video/webm';
                                video.appendChild(source2);
                                
                                video.appendChild(document.createTextNode('Votre navigateur ne supporte pas la lecture de vidéos.'));
                                modalVideo.appendChild(video);
                            }
                        } else {
                            modalVideo.innerHTML = '<div style="text-align: center; padding: 60px; color: #666;"><div style="font-size: 48px; margin-bottom: 10px;">🎮</div><p>Aucune vidéo disponible</p></div>';
                        }
                    }

                    // Show modal
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                // Close modal
                function closeGameModal() {
                    if (!modal) return;
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    // Clear video to stop playback
                    if (modalVideo) modalVideo.innerHTML = '';
                }

                // Event listeners for game cards - use event delegation for reliability
                const gamesGrid = document.querySelector('.games-grid');
                
                if (gamesGrid) {
                    gamesGrid.addEventListener('click', function(e) {
                        // Find the closest game card
                        const card = e.target.closest('.game-card-clickable');
                        
                        if (!card) return;
                        
                        console.log('Card clicked!', card.dataset.gameName);
                        
                        // Don't open modal if clicking on rating stars or buttons
                        if (e.target.closest('.rating-container') || 
                            e.target.closest('.rate-btn') ||
                            e.target.closest('.stars') ||
                            e.target.tagName === 'BUTTON' ||
                            e.target.classList.contains('star')) {
                            console.log('Click ignored - rating area');
                            return;
                        }
                        
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Opening modal for:', card.dataset.gameName);
                        openGameModal(card);
                    });
                }
                
                // Also add direct listeners as backup
                const clickableCards = document.querySelectorAll('.game-card-clickable');
                console.log('Found clickable cards:', clickableCards.length);
                
                clickableCards.forEach(card => {
                    card.style.cursor = 'pointer';
                });

                if (modalClose) {
                    modalClose.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeGameModal();
                    });
                }

                // Close modal when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeGameModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.classList.contains('active')) {
                        closeGameModal();
                    }
                });
            }

            // Gestion des notes
            const ratingContainers = document.querySelectorAll('.rating-container');
            
            ratingContainers.forEach(container => {
                const stars = container.querySelectorAll('.star');
                const gameId = container.dataset.gameId;
                const infoDiv = container.querySelector('.rating-info');
                const rateBtn = container.querySelector('.rate-btn');
                const isLoggedIn = container.dataset.isLoggedIn === 'true';
                
                // Si l'utilisateur n'est pas connecté, désactiver toutes les interactions
                if (!isLoggedIn) {
                    stars.forEach(star => {
                        star.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            alert('Vous devez être connecté pour noter un jeu. Veuillez vous connecter.');
                            window.location.href = '?action=login';
                        });
                        // Désactiver le hover pour les utilisateurs non connectés
                        star.addEventListener('mouseenter', function() {
                            this.style.cursor = 'not-allowed';
                        });
                    });
                    return; // Ne pas continuer avec les autres événements
                }
                
                stars.forEach(star => {
                    // Hover effect with scale
                    star.addEventListener('mouseenter', function() {
                        const value = this.dataset.value;
                        this.style.transform = 'scale(1.2)';
                        updateStarsVisual(stars, value);
                    });
                    
                    star.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                    
                    // Click to select
                    star.addEventListener('click', function() {
                        const value = this.dataset.value;
                        container.dataset.selectedRating = value;
                        updateStarsVisual(stars, value);
                        // Show button with animation
                        if(rateBtn) {
                            rateBtn.style.display = 'inline-block';
                            rateBtn.style.animation = 'fadeIn 0.3s';
                        }
                    });
                });

                // Button click to submit
                if(rateBtn) {
                    console.log('Rate button found for game:', gameId);
                    rateBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const value = container.dataset.selectedRating;
                        console.log('Button clicked! Selected rating:', value, 'for game:', gameId);
                        
                        if(!value) {
                            alert('Veuillez d\'abord sélectionner une note en cliquant sur les étoiles');
                            return;
                        }

                        // Vérifier à nouveau si l'utilisateur est connecté avant d'envoyer
                        if (!isLoggedIn) {
                            alert('Vous devez être connecté pour noter un jeu. Veuillez vous connecter.');
                            window.location.href = '?action=login';
                            return;
                        }
                        
                        fetch('?action=rate_game', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                game_id: gameId,
                                rating: value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Response:', data);
                            if (data.success) {
                                // Update visual state permanently
                                container.dataset.userRating = value;
                                updateStarsVisual(stars, value, true);
                                
                                // Update average and count
                                container.querySelector('.average').textContent = data.new_average;
                                container.querySelector('.count').textContent = data.new_count;
                                
                                // Hide button and show success
                                rateBtn.style.display = 'none';
                                const originalText = infoDiv.innerHTML;
                                infoDiv.innerHTML = '<span style="color: #00ffcc;">Merci !</span>';
                                setTimeout(() => {
                                    infoDiv.innerHTML = originalText;
                                    // Update displayed values again just in case
                                    container.querySelector('.average').textContent = data.new_average;
                                    container.querySelector('.count').textContent = data.new_count;
                                }, 2000);
                            } else {
                                alert(data.error || 'Erreur lors de la notation');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Une erreur est survenue');
                        });
                    });
                }
                
                // Reset on mouse leave if not clicked/selected
                container.querySelector('.stars').addEventListener('mouseleave', function() {
                    const currentRating = container.dataset.userRating || 0;
                    const selectedRating = container.dataset.selectedRating;
                    // If a new rating is selected but not submitted, keep showing it?
                    // Or revert to userRating if not hovering?
                    // Let's revert to selectedRating if exists, else userRating
                    const valueToShow = selectedRating || currentRating;
                    updateStarsVisual(stars, valueToShow);
                });
            });

            function updateStarsVisual(stars, value, permanent = false) {
                stars.forEach(s => {
                    if (s.dataset.value <= value) {
                        s.textContent = '★';
                    } else {
                        s.textContent = '☆';
                    }
                });
            }
        });
    </script>

<?php include "views/front/includes/footer.php"; ?>