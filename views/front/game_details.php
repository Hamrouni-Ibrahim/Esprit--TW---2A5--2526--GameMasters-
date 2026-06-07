<?php
// Set page variables for header
$pageTitle = (isset($game['name']) ? htmlspecialchars($game['name']) : 'Jeu') . ' - Game Master';
$currentPage = 'games';

// Include main site header
include "views/front/includes/header.php";
?>
    <style>
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

        .detail-section {
            padding: 120px 20px 80px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f1329 0%, #1a1f3a 100%);
        }

        .detail-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .detail-header {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        .detail-banner {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }

        .detail-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 40px;
            background: linear-gradient(to top, #0f1329 0%, transparent 100%);
        }

        .detail-title {
            font-size: 3.5em;
            color: #fff;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .detail-meta {
            display: flex;
            gap: 20px;
            color: #ccc;
            font-size: 1.1em;
            align-items: center;
        }

        .detail-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            padding: 40px;
        }

        .description__text {
            color: #d1d5db;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .sidebar-card {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-title {
            color: #00ffcc;
            font-size: 1.2em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #00ffcc;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        .back-btn:hover {
            transform: translateX(-5px);
        }

        .video-wrapper {
            margin-top: 30px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-tag {
            display: inline-block;
            background: rgba(0, 255, 204, 0.1);
            color: #00ffcc;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            border: 1px solid rgba(0, 255, 204, 0.2);
            margin-right: 10px;
        }

        @media (max-width: 900px) {
            .detail-content {
                grid-template-columns: 1fr;
            }
            .detail-header {
                height: 300px;
            }
            .detail-title {
                font-size: 2.5em;
            }
        }
    <style>
        .detail-section {
            padding: 120px 20px 80px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f1329 0%, #1a1f3a 100%);
        }
        <div class="detail-container">
            <div style="padding: 20px 40px 0;">
                <a href="?action=games" class="back-btn">← Retour aux jeux</a>
            </div>

            <?php
            // Gestion de l'image
            $imageUrl = $game['image_url'];
            if (strpos($imageUrl, '/game-masters/') === 0) {
                $finalImageUrl = $imageUrl;
            } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $finalImageUrl = $imageUrl;
            } else {
                // Image par défaut (logique simplifiée pour l'affichage)
                $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
            }
            ?>

            <div class="detail-header">
                <img src="<?php echo htmlspecialchars($finalImageUrl); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>" class="detail-banner" onerror="this.src='/game-masters/public/assets/img/dev1.jpg'">
                <div class="detail-overlay">
                    <h1 class="detail-title"><?php echo htmlspecialchars($game['name']); ?></h1>
                    <div class="detail-meta">
                        <span class="feature-tag">
                            <?php echo !empty($game['impact_social']) ? htmlspecialchars($game['impact_social']) : 'Impact Social'; ?>
                        </span>
                        <?php if(isset($game['category_name'])): ?>
                            <span class="feature-tag" style="border-color: #00c8ff; color: #00c8ff; background: rgba(0, 200, 255, 0.1);">
                                📁 <?php echo htmlspecialchars($game['category_name']); ?>
                            </span>
                        <?php endif; ?>
                        <span>📅 <?php echo date('d/m/Y', strtotime($game['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-content">
                <div class="main-info">
                    <h2 class="sidebar-title">À propos du jeu</h2>
                    <div class="description__text">
                        <?php echo nl2br(htmlspecialchars($game['description'])); ?>
                    </div>

                    <!-- Vidéo -->
                    <?php 
                    $videoUrl = $game['demo_url'] ?? '';
                    if (!empty($videoUrl)): 
                        // Function to convert YouTube URL to embed URL
                        function convertYouTubeToEmbed($url) {
                            // Remove any whitespace
                            $url = trim($url);
                            
                            // Extract video ID from different YouTube URL formats
                            $videoId = '';
                            
                            // Format: https://www.youtube.com/watch?v=VIDEO_ID
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                                $videoId = $matches[1];
                            }
                            // Format: youtube.com/watch?v=VIDEO_ID (without https)
                            elseif (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                                $videoId = $matches[1];
                            }
                            // Format: youtu.be/VIDEO_ID
                            elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                                $videoId = $matches[1];
                            }
                            
                            if (!empty($videoId)) {
                                return 'https://www.youtube.com/embed/' . $videoId . '?rel=0&modestbranding=1';
                            }
                            
                            return null;
                        }
                        
                        $embedUrl = convertYouTubeToEmbed($videoUrl);
                    ?>
                        <h2 class="sidebar-title" style="margin-top: 40px;">Démonstration</h2>
                        <div class="video-wrapper">
                            <?php
                            if ($embedUrl) {
                                // YouTube video
                                echo '<iframe width="100%" height="500" src="' . htmlspecialchars($embedUrl) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 15px;"></iframe>';
                            } elseif (filter_var($videoUrl, FILTER_VALIDATE_URL) || strpos($videoUrl, '/') === 0) {
                                // Other video URL or local video
                                echo '<video width="100%" height="500" controls style="border-radius: 15px;">';
                                echo '<source src="' . htmlspecialchars($videoUrl) . '" type="video/mp4">';
                                echo '<source src="' . htmlspecialchars($videoUrl) . '" type="video/webm">';
                                echo '<source src="' . htmlspecialchars($videoUrl) . '" type="video/ogg">';
                                echo 'Votre navigateur ne supporte pas la lecture de vidéos.';
                                echo '</video>';
                            } else {
                                // Invalid URL, show as link
                                echo '<div style="padding: 20px; background: rgba(255, 255, 255, 0.05); border-radius: 15px; text-align: center;">';
                                echo '<p style="color: #aaa; margin-bottom: 15px;">URL vidéo non valide</p>';
                                echo '<a href="' . htmlspecialchars($videoUrl) . '" target="_blank" style="color: #00ffcc; text-decoration: underline;">Ouvrir la vidéo</a>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="sidebar">
                    <!-- Note du jeu -->
                    <div class="sidebar-card" style="text-align: center;">
                        <div class="sidebar-title">Avis de la communauté</div>
                        <div style="font-size: 3em; color: #ffd700; font-weight: bold; margin-bottom: 10px;">
                            <?php echo isset($game['rating_average']) && $game['rating_average'] > 0 ? $game['rating_average'] : '-'; ?> <span style="font-size: 0.4em; color: #666;">/5</span>
                        </div>
                        <div style="color: #aaa; font-size: 0.9em;">
                            Basé sur <?php echo isset($game['rating_count']) ? $game['rating_count'] : 0; ?> avis
                        </div>
                    </div>

                    <!-- Contributeur -->
                    <?php if (isset($game['user_id']) && $game['user_id'] > 0): ?>
                        <div class="sidebar-card">
                            <div class="sidebar-title">Proposé par</div>
                            <?php
                            // Fetch user info if available (passed from controller ideally, but can be done here if needed or just display if available in game array)
                            // Assuming controller passes user info or joined in query
                            ?>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 50px; height: 50px; background: rgba(153, 69, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #9945ff; border: 1px solid rgba(153, 69, 255, 0.4);">
                                    👤
                                </div>
                                <div>
                                    <div style="color: #fff; font-weight: 600;">Membre de la communauté</div>
                                    <div style="color: #aaa; font-size: 0.8em; margin-top: 5px;">Rejoignez-nous !</div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="sidebar-card">
                            <div class="sidebar-title">Editeur</div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="/game-masters/public/assets/img/logo.png" style="width: 50px; height: 50px; border-radius: 10px;">
                                <div>
                                    <div style="color: #00ffcc; font-weight: 600;">Game Masters</div>
                                    <div style="color: #aaa; font-size: 0.8em; margin-top: 5px;">Officiel</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="sidebar-card" style="text-align: center;">
                        <a href="?action=search_games" class="admin-btn" style="display: block; width: 100%; text-align: center; margin-bottom: 10px;">
                            🔍 Nouvelle Recherche
                        </a>
                        <a href="?controller=formation&action=userDashboard" style="color: #aaa; text-decoration: none; font-size: 0.9em; display: block; margin-top: 15px;">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>
