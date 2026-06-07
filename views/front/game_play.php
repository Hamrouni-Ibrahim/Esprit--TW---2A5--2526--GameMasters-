<?php
require_once "views/front/includes/header.php";
?>

<style>
    .game-play-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        min-height: calc(100vh - 200px);
    }

    .game-header {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .game-header-image {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(0, 255, 204, 0.3);
        margin-right: 20px;
    }

    .game-info h1 {
        color: #00ffcc;
        font-size: 1.8em;
        margin-bottom: 10px;
    }

    .game-info p {
        color: #a0a0a0;
        margin: 5px 0;
    }

    .game-meta-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9em;
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

    .back-btn {
        padding: 12px 25px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-block;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: #00ffcc;
        color: #00ffcc;
    }

    .game-frame-container {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .game-frame {
        width: 100%;
        height: 80vh;
        min-height: 600px;
        border: none;
        border-radius: 10px;
        background: #000;
    }

    .game-link-container {
        text-align: center;
        padding: 40px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
    }

    .game-link-container h3 {
        color: #00ffcc;
        margin-bottom: 20px;
    }

    .game-link-container p {
        color: #a0a0a0;
        margin-bottom: 25px;
    }

    .external-link-btn {
        display: inline-block;
        padding: 15px 40px;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        border: none;
        border-radius: 8px;
        color: #0a0a0a;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }

    .external-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 255, 204, 0.4);
    }
</style>

<div class="game-play-container">
    <div class="game-header">
        <div class="game-info" style="display: flex; align-items: center; flex: 1;">
            <?php if (!empty($game['thumbnail_url'])): ?>
                <img src="<?php echo htmlspecialchars($game['thumbnail_url']); ?>" 
                     alt="<?php echo htmlspecialchars($game['title']); ?>" 
                     class="game-header-image"
                     onerror="this.style.display='none';">
            <?php endif; ?>
            <div>
                <h1><?php echo htmlspecialchars($game['title']); ?></h1>
                <p><?php echo htmlspecialchars($game['description']); ?></p>
                <div class="game-meta-badges">
                <span class="badge badge-category">
                    <?php 
                    $catNames = [
                        'educational' => '📚 Éducatif',
                        'social_impact' => '🌍 Impact Social'
                    ];
                    echo $catNames[$game['category']] ?? ucfirst($game['category']); 
                    ?>
                </span>
                <span class="badge badge-difficulty">
                    <?php 
                    $diffNames = ['easy' => '⭐ Facile', 'medium' => '⭐⭐ Moyen', 'hard' => '⭐⭐⭐ Difficile'];
                    echo $diffNames[$game['difficulty']] ?? ucfirst($game['difficulty']); 
                    ?>
                </span>
                </div>
            </div>
        </div>
        <a href="?controller=gameLibrary&action=list" class="back-btn">← Retour à la bibliothèque</a>
    </div>

    <?php if ($game['game_type'] === 'iframe'): ?>
        <div class="game-frame-container">
            <iframe 
                src="<?php echo htmlspecialchars($game['game_url']); ?>" 
                class="game-frame"
                allowfullscreen
                allow="gamepad; fullscreen">
            </iframe>
        </div>
    <?php else: ?>
        <div class="game-link-container">
            <h3>🎮 Jouer au jeu</h3>
            <p>Ce jeu s'ouvrira dans un nouvel onglet pour une meilleure expérience de jeu.</p>
            <a href="<?php echo htmlspecialchars($game['game_url']); ?>" target="_blank" class="external-link-btn">
                Ouvrir le jeu dans un nouvel onglet →
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once "views/front/includes/footer.php"; ?>

