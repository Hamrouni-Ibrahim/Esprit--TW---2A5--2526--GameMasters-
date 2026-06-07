<?php
require_once "views/front/includes/header.php";
?>

<style>
    .games-library-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        min-height: calc(100vh - 200px);
    }

    .page-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title h1 {
        font-size: 2.5em;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .page-title p {
        color: #a0a0a0;
        font-size: 1.1em;
    }

    .filters-section {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .filters-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        color: #00ffcc;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #00ffcc;
        box-shadow: 0 0 10px rgba(0, 255, 204, 0.3);
    }

    .filter-group select option {
        background: #1a1a2e;
        color: #fff;
        padding: 10px;
    }

    .filter-group select option:hover {
        background: #2a2a3e;
    }

    .filter-group select option:checked {
        background: #00ffcc;
        color: #0a0a0a;
        font-weight: 600;
    }

    .filter-btn {
        padding: 12px 30px;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        border: none;
        border-radius: 8px;
        color: #0a0a0a;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 255, 204, 0.4);
    }

    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .game-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .game-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 204, 0.1), transparent);
        transition: left 0.5s;
    }

    .game-card:hover::before {
        left: 100%;
    }

    .game-card:hover {
        transform: translateY(-5px);
        border-color: #00ffcc;
        box-shadow: 0 10px 30px rgba(0, 255, 204, 0.2);
    }

    .game-thumbnail {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 3em;
        border: 2px solid rgba(0, 255, 204, 0.3);
        overflow: hidden;
        position: relative;
    }

    .game-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .game-card:hover .game-thumbnail img {
        transform: scale(1.1);
    }

    .game-title {
        color: #00ffcc;
        font-size: 1.3em;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .game-description {
        color: #a0a0a0;
        font-size: 0.9em;
        line-height: 1.6;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .game-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .game-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: 600;
    }

    .badge-category {
        background: rgba(0, 255, 204, 0.2);
        color: #00ffcc;
        border: 1px solid rgba(0, 255, 204, 0.3);
    }

    .badge-difficulty {
        background: rgba(255, 200, 0, 0.2);
        color: #ffc800;
        border: 1px solid rgba(255, 200, 0, 0.3);
    }

    .play-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        border: none;
        border-radius: 8px;
        color: #0a0a0a;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .play-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 255, 204, 0.4);
    }

    .no-games {
        text-align: center;
        padding: 60px 20px;
        color: #a0a0a0;
    }

    .no-games h3 {
        color: #00ffcc;
        margin-bottom: 10px;
    }
</style>

<div class="games-library-container">
    <div class="page-title">
        <h1>🎮 Bibliothèque de Jeux</h1>
        <p>Découvrez des jeux éducatifs et à impact social</p>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form method="GET" action="">
            <input type="hidden" name="controller" value="gameLibrary">
            <input type="hidden" name="action" value="list">
            
            <div class="filters-row">
                <div class="filter-group">
                    <label>🔍 Recherche</label>
                    <input type="text" name="search" placeholder="Rechercher un jeu..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                
                <div class="filter-group">
                    <label>📁 Catégorie</label>
                    <select name="category">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === $cat) ? 'selected' : ''; ?>>
                                <?php 
                                $catNames = [
                                    'educational' => 'Éducatif',
                                    'social_impact' => 'Impact Social'
                                ];
                                echo htmlspecialchars($catNames[$cat] ?? ucfirst($cat)); 
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>⚡ Difficulté</label>
                    <select name="difficulty">
                        <option value="">Tous les niveaux</option>
                        <option value="easy" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'easy') ? 'selected' : ''; ?>>Facile</option>
                        <option value="medium" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'medium') ? 'selected' : ''; ?>>Moyen</option>
                        <option value="hard" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'hard') ? 'selected' : ''; ?>>Difficile</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="filter-btn">Filtrer</button>
                    <?php if (isset($_GET['category']) || isset($_GET['difficulty']) || isset($_GET['search'])): ?>
                        <a href="?controller=gameLibrary&action=list" style="display: block; margin-top: 10px; color: #00ffcc; text-decoration: none; text-align: center; font-size: 0.9em;">Réinitialiser</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Games Grid -->
    <?php if (empty($games)): ?>
        <div class="no-games">
            <h3>Aucun jeu trouvé</h3>
            <p>Essayez de modifier vos critères de recherche.</p>
        </div>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($games as $game): ?>
                <div class="game-card">
                    <div class="game-thumbnail">
                        <?php if (!empty($game['thumbnail_url'])): ?>
                            <img src="<?php echo htmlspecialchars($game['thumbnail_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($game['title']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='🎮';">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 3em; background: linear-gradient(135deg, #1a1a2e, #16213e);">
                                🎮
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="game-title"><?php echo htmlspecialchars($game['title']); ?></div>
                    
                    <div class="game-description">
                        <?php echo htmlspecialchars($game['description']); ?>
                    </div>
                    
                    <div class="game-meta">
                        <span class="game-badge badge-category">
                            <?php 
                            $catNames = [
                                'educational' => '📚 Éducatif',
                                'social_impact' => '🌍 Impact Social'
                            ];
                            echo $catNames[$game['category']] ?? ucfirst($game['category']); 
                            ?>
                        </span>
                        <span class="game-badge badge-difficulty">
                            <?php 
                            $diffNames = ['easy' => '⭐ Facile', 'medium' => '⭐⭐ Moyen', 'hard' => '⭐⭐⭐ Difficile'];
                            echo $diffNames[$game['difficulty']] ?? ucfirst($game['difficulty']); 
                            ?>
                        </span>
                    </div>
                    
                    <a href="?controller=gameLibrary&action=play&id=<?php echo $game['id']; ?>" class="play-btn">
                        ▶️ Jouer maintenant
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once "views/front/includes/footer.php"; ?>

