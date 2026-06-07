<?php
// Note: Variables are already initialized in index.php
// This file is included by index.php which handles the logic
// Variables available: $games, $selectedCategory, $searchTerm, $errors, $success, $categories

// Get category stats if method exists
$categoryStats = [];
if (method_exists($categoryController, 'getCategoryStats')) {
    $categoryStats = $categoryController->getCategoryStats();
}

// Gestion du cas où categoryStats est vide ou a une structure différente
if (empty($categoryStats)) {
    $categoryStats = [];
    foreach ($categories as $category) {
        // Vérifier la structure réelle des données
        $categoryId = $category['idCategory'] ?? $category['id'] ?? $category['ID'] ?? 0;
        $categoryName = $category['name'] ?? $category['Name'] ?? 'Catégorie inconnue';
        
        $categoryStats[] = [
            'idCategory' => $categoryId,
            'name' => $categoryName,
            'game_count' => 0
        ];
    }
} else {
    // Normaliser la structure des stats
    $normalizedStats = [];
    foreach ($categoryStats as $stat) {
        $categoryId = $stat['idCategory'] ?? $stat['id'] ?? $stat['ID'] ?? 0;
        $categoryName = $stat['name'] ?? $stat['Name'] ?? 'Catégorie inconnue';
        $gameCount = $stat['game_count'] ?? $stat['gameCount'] ?? $stat['count'] ?? 0;
        
        $normalizedStats[] = [
            'idCategory' => $categoryId,
            'name' => $categoryName,
            'game_count' => $gameCount
        ];
    }
    $categoryStats = $normalizedStats;
}

// SUPPRIMER LES DOUBLONS DE CATÉGORIES
$uniqueCategories = [];
$seenCategories = [];

foreach ($categories as $category) {
    $categoryId = $category['idCategory'] ?? $category['id'] ?? $category['ID'] ?? 0;
    $categoryName = $category['name'] ?? $category['Name'] ?? 'Catégorie inconnue';
    
    // Normaliser le nom pour la comparaison (minuscules, sans espaces superflus)
    $normalizedName = strtolower(trim($categoryName));
    
    if (!in_array($normalizedName, $seenCategories)) {
        $seenCategories[] = $normalizedName;
        $uniqueCategories[] = $category;
    }
}

$categories = $uniqueCategories;

// Mettre à jour les stats pour correspondre aux catégories uniques
$uniqueStats = [];
foreach ($categoryStats as $stat) {
    $categoryId = $stat['idCategory'] ?? $stat['id'] ?? $stat['ID'] ?? 0;
    $categoryName = $stat['name'] ?? $stat['Name'] ?? 'Catégorie inconnue';
    $normalizedName = strtolower(trim($categoryName));
    
    if (!in_array($normalizedName, $seenCategories)) {
        $seenCategories[] = $normalizedName;
        $uniqueStats[] = $stat;
    }
}

$categoryStats = $uniqueStats;
?>

<!-- Note: Header is already included by index.php -->
<style>
        /* === CORRECTIONS SPÉCIFIQUES POUR LA PAGE RECHERCHE === */
        
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

        /* Styles pour le contenu de recherche */
        .search-section {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
        }

        .search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00ffcc, #00ccff, #ff6b6b);
        }

        .search-form-container {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: end;
        }

        .form-group-enhanced {
            position: relative;
        }

        .form-label-enhanced {
            display: block;
            color: #00ffcc;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .form-select-enhanced {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0,0,0,0.3);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #ffffff;
            font-size: 16px;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2300ffcc' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .form-select-enhanced:focus {
            outline: none;
            border-color: #00ffcc;
            box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.2);
        }

        .form-select-enhanced.invalid {
            border-color: #ff4444;
        }

        .btn-search {
            background: linear-gradient(135deg, #00ffcc, #00ccff);
            color: #0e0e16;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 255, 204, 0.3);
        }

        .btn-search:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .validation-feedback {
            font-size: 14px;
            margin-top: 8px;
            min-height: 20px;
        }

        .validation-feedback.error {
            color: #ff4444;
        }

        .validation-feedback.success {
            color: #00ff88;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #00ffcc;
            display: block;
        }

        .stat-label {
            font-size: 12px;
            color: #cccccc;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .results-section {
            margin-top: 40px;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .results-count {
            color: #cccccc;
            font-size: 14px;
        }

        .games-grid-enhanced {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .game-card-enhanced {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
        }

        .game-card-enhanced:hover {
            transform: translateY(-8px);
            border-color: #00ffcc;
            box-shadow: 0 20px 40px rgba(0, 255, 204, 0.25);
        }

        .game-image-enhanced {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .game-image-enhanced img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .game-card-enhanced:hover .game-image-enhanced img {
            transform: scale(1.05);
        }

        .game-category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 255, 204, 0.9);
            color: #0e0e16;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .game-content-enhanced {
            padding: 25px;
        }

        .game-title-enhanced {
            color: #00ffcc;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .game-description-enhanced {
            color: #cccccc;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .game-impact-enhanced {
            background: rgba(0, 209, 255, 0.1);
            border: 1px solid #00ccff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .game-impact-label {
            color: #00ccff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .game-impact-text {
            color: #ffffff;
            font-size: 13px;
            line-height: 1.4;
        }

        .game-actions-enhanced {
            display: flex;
            gap: 10px;
        }

        .btn-details {
            background: transparent;
            border: 2px solid #00ff88;
            color: #00ff88;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
        }

        .btn-details:hover {
            background: #00ff88;
            color: #0e0e16;
        }

        .no-results-enhanced {
            text-align: center;
            padding: 80px 20px;
            color: #666666;
        }

        .no-results-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .alert-message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-error {
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
            border-color: rgba(255, 68, 68, 0.3);
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            border-color: rgba(0, 255, 136, 0.3);
        }

        /* Footer amélioré */
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

        /* Responsive */
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

            .search-form-container {
                grid-template-columns: 1fr;
            }
            
            .search-section {
                padding: 25px 20px;
            }
            
            .games-grid-enhanced {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .logo-text-gaming {
                font-size: 16px;
            }

            .search-section {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Note: Navigation is already included in header.php -->
    
    <main class="main-content">
        <div class="container">
            <!-- En-tête de page -->
            <div class="page-header" style="text-align: center; margin-bottom: 50px;">
                <h1 style="font-size: 3em; margin-bottom: 20px;">🎮</h1>
                <h1>Recherche de Jeux</h1>
                <p style="font-size: 18px; color: #cccccc; max-width: 600px; margin: 0 auto;">
                    Trouvez facilement vos jeux préférés par nom ou par catégorie
                </p>
            </div>

            <!-- Messages d'alerte -->
            <?php if (!empty($errors)): ?>
                <div class="alert-message alert-error">
                    <strong>❌ Erreur :</strong>
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success && empty($games)): ?>
                <div class="alert-message alert-info">
                    <strong>ℹ️ Information :</strong> Aucun jeu trouvé correspondant à vos critères. Essayez d'autres mots-clés ou une autre catégorie.
                </div>
            <?php endif; ?>

            <!-- Section de recherche -->
            <section class="search-section">
                <form id="searchGamesForm" method="POST" action="?action=search_games" novalidate>
                    <input type="hidden" name="search_games" value="1">
                    
                    <div class="search-form-container">
                        <!-- Recherche par nom -->
                        <div class="form-group-enhanced">
                            <label for="search_term" class="form-label-enhanced">
                                🔍 Nom du jeu
                            </label>
                            <input type="text" name="search_term" id="search_term" 
                                   class="form-select-enhanced" 
                                   placeholder="Ex: Terra Nil, Kind Words..."
                                   value="<?= isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : '' ?>"
                                   style="background-image: none;">
                        </div>

                        <!-- Recherche par catégorie -->
                        <div class="form-group-enhanced">
                            <label for="category_id" class="form-label-enhanced">
                                📁 Catégorie
                            </label>
                            <select name="category_id" id="category_id" class="form-select-enhanced">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <?php
                                    // Récupérer l'ID et le nom de manière sécurisée
                                    $categoryId = $category['idCategory'] ?? $category['id'] ?? $category['ID'] ?? 0;
                                    $categoryName = $category['name'] ?? $category['Name'] ?? 'Catégorie inconnue';
                                    
                                    // Compter les jeux pour cette catégorie
                                    $gameCount = 0;
                                    foreach ($categoryStats as $stat) {
                                        $statId = $stat['idCategory'] ?? $stat['id'] ?? $stat['ID'] ?? 0;
                                        if ($statId == $categoryId) {
                                            $gameCount = $stat['game_count'] ?? $stat['gameCount'] ?? $stat['count'] ?? 0;
                                            break;
                                        }
                                    }
                                    ?>
                                    <option value="<?= $categoryId ?>" 
                                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $categoryId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoryName) ?>
                                        <?php if ($gameCount > 0): ?>
                                            (<?= $gameCount ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="validation-feedback" id="categoryFeedback"></div>
                        </div>
                        
                        <button type="submit" class="btn-search" id="submitBtn">
                            🎯 Rechercher
                        </button>
                    </div>
                </form>

                <!-- Statistiques des catégories -->
                <div class="stats-grid">
                    <?php 
                    $totalGames = 0;
                    $maxGames = 0;
                    
                    if (!empty($categoryStats)) {
                        foreach ($categoryStats as $stat) {
                            $gameCount = $stat['game_count'] ?? $stat['gameCount'] ?? $stat['count'] ?? 0;
                            $totalGames += $gameCount;
                            if ($gameCount > $maxGames) {
                                $maxGames = $gameCount;
                            }
                        }
                    }
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= count($categories) ?></span>
                        <span class="stat-label">Catégories</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $totalGames ?></span>
                        <span class="stat-label">Jeux classés</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $maxGames ?></span>
                        <span class="stat-label">Max par catégorie</span>

                    </div>
                </div>
            </section>

            <!-- Section des résultats -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_games']) && empty($errors)): ?>
                <section class="results-section">
                    <div class="results-header">
                        <div>
                            <h2>
                                <?php 
                                    $titleParts = [];
                                    if (!empty($searchTerm)) {
                                        $titleParts[] = 'Recherche "' . htmlspecialchars($searchTerm) . '"';
                                    }
                                    if ($selectedCategory) {
                                        $catName = $selectedCategory['name'] ?? $selectedCategory['Name'] ?? 'Catégorie inconnue';
                                        $titleParts[] = 'Catégorie "' . htmlspecialchars($catName) . '"';
                                    }
                                    
                                    if (empty($titleParts)) {
                                        echo "🎮 Tous les jeux";
                                    } else {
                                        echo "🎮 Résultats pour : " . implode(' + ', $titleParts);
                                    }
                                ?>
                            </h2>
                            <div class="results-count">
                                <?= count($games) ?> jeu<?= count($games) > 1 ? 'x' : '' ?> trouvé<?= count($games) > 1 ? 's' : '' ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($games)): ?>
                        <div class="games-grid-enhanced">
                            <?php foreach ($games as $game): ?>
                                <div class="game-card-enhanced">
                                    <div class="game-image-enhanced">
                                        <a href="/game-masters/public/index.php?action=game_details&id=<?= $game['id'] ?>" style="display: block; width: 100%; height: 100%;">
                                            <img src="<?= htmlspecialchars($game['image_url'] ?: '/game-masters/public/assets/img/dev1.jpg') ?>" 
                                                 alt="<?= htmlspecialchars($game['name']) ?>"
                                                 onerror="this.src='/game-masters/public/assets/img/dev1.jpg'">
                                        </a>
                                        <?php if (!empty($game['category_name'])): ?>
                                            <div class="game-category-badge">
                                                <?= htmlspecialchars($game['category_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="game-content-enhanced">
                                        <h3 class="game-title-enhanced"><?= htmlspecialchars($game['name']) ?></h3>
                                        <p class="game-description-enhanced">
                                            <?= htmlspecialchars(substr($game['description'] ?? 'Description non disponible', 0, 120)) ?>...
                                        </p>
                                        <?php if (!empty($game['impact_social'])): ?>
                                            <div class="game-impact-enhanced">
                                                <div class="game-impact-label">🌟 Impact Social</div>
                                                <div class="game-impact-text"><?= htmlspecialchars($game['impact_social']) ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="game-actions-enhanced">
                                            <a href="/game-masters/public/index.php?action=game_details&id=<?= $game['id'] ?>" 
                                               class="btn-details">Voir les détails</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results-enhanced">
                            <div class="no-results-icon">🔍</div>
                            <h3 style="color: #cccccc; margin-bottom: 15px;">Aucun jeu trouvé</h3>
                            <p style="margin-bottom: 25px;">Aucun jeu ne correspond à vos critères de recherche.</p>
                            <button onclick="document.getElementById('search_term').focus()" class="btn-search">
                                🔄 Nouvelle recherche
                            </button>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer Amélioré -->
    <footer class="footer-gaming">
        <div class="footer-logo">
            <img src="/game-masters/public/assets/img/logo.png" alt="Game Masters">
            <span>Game Masters</span>
        </div>
        <p class="copyright">© 2024 Game Masters — Jouez, apprenez et changez le monde</p>
        <p style="color: #666; margin-top: 10px; font-size: 0.9em;">
            Des jeux qui font la différence
        </p>
    </footer>

    <script src="/game-masters/templates/front/templatefront.js"></script>
    <script>
        // Preserve search values between page navigations
        (function() {
            const searchTermInput = document.getElementById('search_term');
            const categorySelect = document.getElementById('category_id');
            
            // Save search values to sessionStorage when they change
            function saveSearchValues() {
                const searchData = {
                    search_term: searchTermInput ? searchTermInput.value : '',
                    category_id: categorySelect ? categorySelect.value : ''
                };
                sessionStorage.setItem('gameSearchValues', JSON.stringify(searchData));
            }
            
            // Restore search values from sessionStorage or URL parameters
            function restoreSearchValues() {
                // First, check URL parameters (highest priority)
                const urlParams = new URLSearchParams(window.location.search);
                const urlSearchTerm = urlParams.get('search_term');
                const urlCategoryId = urlParams.get('category_id');
                
                if (urlSearchTerm !== null && searchTermInput) {
                    searchTermInput.value = urlSearchTerm;
                } else {
                    // Try to restore from sessionStorage
                    const savedData = sessionStorage.getItem('gameSearchValues');
                    if (savedData) {
                        try {
                            const searchData = JSON.parse(savedData);
                            if (searchTermInput && searchData.search_term) {
                                searchTermInput.value = searchData.search_term;
                            }
                        } catch(e) {
                            console.error('Error parsing saved search data:', e);
                        }
                    }
                }
                
                if (urlCategoryId !== null && categorySelect) {
                    categorySelect.value = urlCategoryId;
                } else {
                    // Try to restore from sessionStorage
                    const savedData = sessionStorage.getItem('gameSearchValues');
                    if (savedData) {
                        try {
                            const searchData = JSON.parse(savedData);
                            if (categorySelect && searchData.category_id) {
                                categorySelect.value = searchData.category_id;
                            }
                        } catch(e) {
                            console.error('Error parsing saved search data:', e);
                        }
                    }
                }
            }
            
            // Restore on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', restoreSearchValues);
            } else {
                restoreSearchValues();
            }
            
            // Save on input change
            if (searchTermInput) {
                searchTermInput.addEventListener('input', saveSearchValues);
                searchTermInput.addEventListener('change', saveSearchValues);
            }
            if (categorySelect) {
                categorySelect.addEventListener('change', saveSearchValues);
            }
            
            // Save before form submission
            const form = document.getElementById('searchGamesForm');
            if (form) {
                form.addEventListener('submit', function() {
                    saveSearchValues();
                });
            }
        })();
        
        // Validation JavaScript en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('searchGamesForm');
            const categorySelect = document.getElementById('category_id');
            const categoryFeedback = document.getElementById('categoryFeedback');
            const submitBtn = document.getElementById('submitBtn');

            function validateCategory() {
                const value = categorySelect.value.trim();
                categorySelect.classList.remove('invalid');
                categoryFeedback.textContent = '';
                categoryFeedback.className = 'validation-feedback';

                if (!value) {
                    categorySelect.classList.add('invalid');
                    categoryFeedback.textContent = '❌ Veuillez sélectionner une catégorie';
                    categoryFeedback.classList.add('error');
                    return false;
                }

                categoryFeedback.textContent = '✅ Catégorie valide';
                categoryFeedback.classList.add('success');
                return true;
            }

            function validateForm() {
                const isCategoryValid = validateCategory();
                submitBtn.disabled = !isCategoryValid;
                return isCategoryValid;
            }

            // Événements de validation
            categorySelect.addEventListener('change', validateForm);
            categorySelect.addEventListener('blur', validateForm);

            // Validation initiale
            validateForm();

            // Empêcher la soumission si invalide
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    // Animation de shake sur le champ invalide
                    categorySelect.style.animation = 'none';
                    setTimeout(() => {
                        categorySelect.style.animation = 'shake 0.5s ease-in-out';
                    }, 10);
                }
            });

            // Auto-focus sur le select
            categorySelect.focus();
        });

        // Animation CSS pour le shake
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);
    </script>
<!-- Footer is handled by the main footer -->