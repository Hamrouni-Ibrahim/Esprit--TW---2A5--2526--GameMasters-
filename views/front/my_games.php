<?php
// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Jeux | Game Masters</title>
    <link rel="stylesheet" href="/game-masters/public/assets/css/style.css">
    <style>
        .my-games-container {
            max-width: 1400px;
            margin: 100px auto 60px;
            padding: 40px;
        }
        
        .page-header {
            margin-bottom: 40px;
        }
        
        .page-title {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }
        
        .page-subtitle {
            color: var(--text-secondary);
            font-size: 16px;
        }
        
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .game-card {
            background: linear-gradient(135deg, rgba(42, 42, 42, 0.3), rgba(26, 26, 26, 0.5));
            border: 1px solid var(--metal-dark);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .game-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-purple);
            box-shadow: 0 15px 35px rgba(153, 69, 255, 0.2);
        }
        
        .game-image-container {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: var(--carbon-dark);
        }
        
        .game-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .game-card:hover .game-image {
            transform: scale(1.05);
        }
        
        .game-overlay {
            position: absolute;
            top: 15px;
            right: 15px;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
            border: 1px solid #ffaa00;
        }
        
        .status-approved {
            background: rgba(0, 255, 136, 0.2);
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }
        
        .status-published {
            background: rgba(0, 255, 136, 0.2);
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }
        
        .status-development {
            background: rgba(0, 209, 255, 0.2);
            color: var(--accent-blue);
            border: 1px solid var(--accent-blue);
        }
        
        .status-rejected {
            background: rgba(255, 68, 68, 0.2);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
        }
        
        .game-content {
            padding: 25px;
        }
        
        .game-title {
            color: var(--accent-cyan);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .game-impact {
            display: inline-block;
            background: rgba(153, 69, 255, 0.2);
            color: var(--accent-purple);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--accent-purple);
        }
        
        .game-description {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .game-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 12px;
            color: var(--text-dim);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            border: 2px dashed var(--metal-dark);
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .empty-state p {
            color: var(--text-dim);
            margin-bottom: 30px;
        }
        
        .btn-primary {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(153, 69, 255, 0.3);
        }
        
        .info-box {
            background: rgba(0, 209, 255, 0.1);
            border: 1px solid var(--accent-blue);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .info-box h4 {
            color: var(--accent-cyan);
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .info-box p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Header simplifié -->
    <header style="background: rgba(18, 18, 18, 0.98); padding: 20px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); position: fixed; top: 0; left: 0; right: 0; z-index: 1000;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php?action=home" style="text-decoration: none; color: var(--text-primary); font-weight: 700; font-size: 20px;">
                🎮 Game Masters
            </a>
            <nav>
                <a href="index.php?action=home" style="color: var(--text-secondary); text-decoration: none; margin-left: 20px;">Accueil</a>
                <a href="index.php?action=games" style="color: var(--text-secondary); text-decoration: none; margin-left: 20px;">Jeux</a>
                <a href="index.php?action=add_game" style="color: var(--accent-cyan); text-decoration: none; margin-left: 20px;">Ajouter un Jeu</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="index.php?action=logout" style="color: var(--text-secondary); text-decoration: none; margin-left: 20px;">Déconnexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <div class="my-games-container">
        <div class="page-header">
            <h1 class="page-title">🎮 Mes Jeux Soumis</h1>
            <p class="page-subtitle">Suivez l'état de vos propositions de jeux</p>
        </div>
        
        <div class="info-box">
            <h4>📋 Statuts des jeux</h4>
            <p>
                <strong style="color: #ffaa00;">⏳ En Attente</strong> : Votre jeu est en cours d'examen par un administrateur<br>
                <strong style="color: var(--accent-green);">✅ Publié</strong> : Votre jeu a été approuvé et est visible sur le site<br>
                <strong style="color: var(--accent-blue);">🛠️ En Développement</strong> : Votre jeu est en cours de développement<br>
                <strong style="color: var(--accent-red);">❌ Rejeté</strong> : Votre jeu n'a pas été approuvé
            </p>
        </div>
        
        <?php if(isset($userGames) && !empty($userGames)): ?>
            <div class="games-grid">
                <?php foreach($userGames as $game): ?>
                    <div class="game-card">
                        <div class="game-image-container">
                            <?php
                            $imageUrl = $game['image_url'];
                            if (strpos($imageUrl, '/game-masters/') === 0) {
                                $finalImageUrl = $imageUrl;
                            } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                $finalImageUrl = $imageUrl;
                            } else {
                                $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                            }
                            ?>
                            <img src="<?php echo $finalImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($game['name']); ?>" 
                                 class="game-image"
                                 onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg'">
                            <div class="game-overlay">
                                <?php
                                $approvalStatus = $game['approval_status'] ?? 'approved';
                                $status = $game['status'] ?? 'development';
                                
                                if($approvalStatus === 'pending') {
                                    echo '<span class="status-badge status-pending">⏳ En Attente</span>';
                                } elseif($approvalStatus === 'rejected') {
                                    echo '<span class="status-badge status-rejected">❌ Rejeté</span>';
                                } elseif($status === 'published') {
                                    echo '<span class="status-badge status-published">✅ Publié</span>';
                                } else {
                                    echo '<span class="status-badge status-development">🛠️ En Développement</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="game-content">
                            <h3 class="game-title"><?php echo htmlspecialchars($game['name']); ?></h3>
                            <div class="game-impact"><?php echo htmlspecialchars($game['impact_social']); ?></div>
                            <p class="game-description"><?php echo htmlspecialchars($game['description']); ?></p>
                            <div class="game-meta">
                                <span>Soumis le <?php echo date('d/m/Y', strtotime($game['created_at'])); ?></span>
                                <?php if($status === 'published'): ?>
                                    <a href="index.php?action=games" style="color: var(--accent-cyan); text-decoration: none;">
                                        Voir sur le site →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🎮</div>
                <h3>Aucun jeu soumis</h3>
                <p>Vous n'avez pas encore proposé de jeu. Partagez vos idées avec la communauté !</p>
                <a href="index.php?action=add_game" class="btn-primary">
                    🚀 Proposer un Jeu
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer simplifié -->
    <footer style="background: var(--primary-black); padding: 40px 0; text-align: center; border-top: 1px solid var(--metal-dark); margin-top: 60px;">
        <p style="color: var(--text-dim);">© 2024 Game Masters. Tous droits réservés.</p>
    </footer>
</body>
</html>
