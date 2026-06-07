<?php 
$pageTitle = 'Recherche de Jeux - Admin';
$currentPage = 'admin_search_games';

// Vérifier que les variables existent
if (!isset($games)) $games = [];
if (!isset($selectedCategory)) $selectedCategory = null;
if (!isset($searchTerm)) $searchTerm = null;
if (!isset($errors)) $errors = [];
if (!isset($success)) $success = false;
if (!isset($categories)) $categories = [];

include "views/admin/includes/header.php"; 
?>
<style>
    /* Styles pour le select de catégorie */
    #category_id {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23e879f9\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(232, 121, 249, 0.3) !important;
    }
    
    #category_id option {
        background: rgba(26, 10, 46, 0.98) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        color: #ffffff !important;
        padding: 12px 15px !important;
        border: none !important;
    }
    
    #category_id option:checked {
        background: rgba(232, 121, 249, 0.3) !important;
        background-color: rgba(232, 121, 249, 0.3) !important;
    }
    
    #category_id option:hover,
    #category_id option:focus {
        background: rgba(232, 121, 249, 0.2) !important;
        background-color: rgba(232, 121, 249, 0.2) !important;
    }
    
    #category_id:focus {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23e879f9\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        border-color: rgba(232, 121, 249, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(232, 121, 249, 0.1) !important;
    }
    
    /* Styles pour le select de note */
    #rating {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23ffd700\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(255, 215, 0, 0.3) !important;
    }
    
    #rating option {
        background: rgba(26, 10, 46, 0.98) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        color: #ffffff !important;
        padding: 12px 15px !important;
        border: none !important;
    }
    
    #rating option:checked {
        background: rgba(255, 215, 0, 0.3) !important;
        background-color: rgba(255, 215, 0, 0.3) !important;
    }
    
    #rating option:hover,
    #rating option:focus {
        background: rgba(255, 215, 0, 0.2) !important;
        background-color: rgba(255, 215, 0, 0.2) !important;
    }
    
    #rating:focus {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23ffd700\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        border-color: rgba(255, 215, 0, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1) !important;
    }
    
    /* Correction des images */
    .admin-game-card img {
        display: block !important;
        max-width: 100% !important;
        height: auto !important;
    }
    
    /* Amélioration de la visibilité du select sur tous les navigateurs */
    select.form-control {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
    }
</style>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>🎮 Recherche de Jeux</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Recherchez des jeux par nom ou catégorie</p>
        </div>

        <!-- Messages d'erreur -->
        <?php if (!empty($errors)): ?>
            <div class="admin-card" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: rgba(255, 68, 68, 0.2); color: #ff4444; border: 1px solid #ff4444;">
                <strong>❌ Erreur :</strong>
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Section de recherche -->
        <div class="admin-card" style="padding: 30px; margin-bottom: 30px; border: 2px solid rgba(232, 121, 249, 0.3);">
            <h3 style="color: #e879f9; margin-bottom: 20px;">
                <i class="fas fa-search" style="margin-right: 10px;"></i>
                Formulaire de Recherche
            </h3>
            
            <form method="POST" action="?action=admin_search_games" style="display: grid; grid-template-columns: 1fr 200px 200px auto; gap: 15px; align-items: end;">
                <input type="hidden" name="search_games" value="1">
                
                <!-- Recherche par nom -->
                <div class="search-input-container" style="position: relative;">
                    <input type="text" name="search_term" id="search_term" 
                           placeholder="Rechercher un jeu par nom..." 
                           value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : (isset($searchTerm) ? htmlspecialchars($searchTerm) : ''); ?>"
                           class="form-control" 
                           style="width: 100%; padding: 12px 15px 12px 45px;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(232, 121, 249, 0.6); pointer-events: none; z-index: 1;"></i>
                </div>
                
                <!-- Recherche par catégorie -->
                <div class="search-input-container" style="position: relative; width: 100%;">
                    <select name="category_id" id="category_id" class="form-control" 
                            style="width: 100%; padding: 12px 45px 12px 15px; border: 1.5px solid rgba(232, 121, 249, 0.3); border-radius: 12px; font-size: 14px; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                        <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Toutes les catégories</option>
                        <?php if(!empty($categories)): ?>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                                        style="background: rgba(26, 10, 46, 0.98); color: #ffffff;"
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) || (isset($selectedCategory) && $selectedCategory && $selectedCategory['id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <i class="fas fa-filter" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(232, 121, 249, 0.6); pointer-events: none; z-index: 1;"></i>
                </div>
                
                <!-- Recherche par note (étoiles) -->
                <div class="search-input-container" style="position: relative; width: 100%;">
                    <select name="rating" id="rating" class="form-control" 
                            style="width: 100%; padding: 12px 45px 12px 15px; border: 1.5px solid rgba(255, 215, 0, 0.3); border-radius: 12px; font-size: 14px; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important; color: #ffffff !important;">
                        <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Toutes les notes</option>
                        <option value="5" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;" <?php echo (isset($_POST['rating']) && $_POST['rating'] == '5') ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ 5 étoiles</option>
                        <option value="4" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;" <?php echo (isset($_POST['rating']) && $_POST['rating'] == '4') ? 'selected' : ''; ?>>⭐⭐⭐⭐ 4 étoiles</option>
                        <option value="3" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;" <?php echo (isset($_POST['rating']) && $_POST['rating'] == '3') ? 'selected' : ''; ?>>⭐⭐⭐ 3 étoiles</option>
                        <option value="2" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;" <?php echo (isset($_POST['rating']) && $_POST['rating'] == '2') ? 'selected' : ''; ?>>⭐⭐ 2 étoiles</option>
                        <option value="1" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;" <?php echo (isset($_POST['rating']) && $_POST['rating'] == '1') ? 'selected' : ''; ?>>⭐ 1 étoile</option>
                    </select>
                    <i class="fas fa-star" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255, 215, 0, 0.6); pointer-events: none; z-index: 1;"></i>
                </div>
                
                <!-- Bouton Rechercher -->
                <button type="submit" class="btn-admin btn-edit" style="padding: 12px 25px; white-space: nowrap;">
                    <i class="fas fa-search" style="margin-right: 5px;"></i> Rechercher
                </button>
            </form>
        </div>

        <!-- Résultats de recherche -->
        <?php if ($success && !empty($games)): ?>
            <div class="admin-card" style="padding: 30px; border: 2px solid rgba(0, 255, 136, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
                    <h3 style="color: #00ff88; margin: 0;">
                        <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                        Résultats de la Recherche
                    </h3>
                    <div style="color: #a0a0a0; font-size: 14px;">
                        <?php echo count($games); ?> jeu(x) trouvé(s)
                        <?php if($selectedCategory): ?>
                            dans la catégorie "<?php echo htmlspecialchars($selectedCategory['name']); ?>"
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                    <?php 
                    require_once "models/User.php";
                    if (!isset($db)) {
                        require_once "config/database.php";
                        $database = new Database();
                        $db = $database->getConnection();
                    }
                    foreach($games as $game): 
                        // Récupérer les infos de l'utilisateur
                        $gameUser = null;
                        if(isset($game['user_id']) && $game['user_id'] > 0) {
                            try {
                                $userQuery = "SELECT id, username, email FROM users WHERE id = ? LIMIT 1";
                                $userStmt = $db->prepare($userQuery);
                                $userStmt->execute([$game['user_id']]);
                                $gameUser = $userStmt->fetch(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                $gameUser = null;
                            }
                        }
                        
                        // Gérer l'image (même logique que games.php)
                        $imageUrl = $game['image_url'] ?? '';
                        if (strpos($imageUrl, '/game-masters/') === 0) {
                            $finalImageUrl = $imageUrl;
                        } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $finalImageUrl = $imageUrl;
                        } else {
                            $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                        }
                    ?>
                        <div class="admin-game-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%); border: 1px solid rgba(232, 121, 249, 0.2); border-radius: 15px; padding: 20px; transition: all 0.3s ease;">
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <img src="<?php echo htmlspecialchars($finalImageUrl); ?>" 
                                     alt="<?php echo htmlspecialchars($game['name'] ?? 'Jeu'); ?>" 
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; border: 2px solid rgba(232, 121, 249, 0.3); display: block;"
                                     onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg';">
                                <div style="flex: 1;">
                                    <h4 style="color: #e879f9; margin: 0 0 8px 0; font-size: 18px;">
                                        <?php echo htmlspecialchars($game['name'] ?? 'Sans nom'); ?>
                                    </h4>
                                    <?php if(!empty($game['category_name'])): ?>
                                        <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px;">
                                            📁 <?php echo htmlspecialchars($game['category_name']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="color: #888; font-size: 11px; margin-bottom: 5px;">
                                        <?php 
                                        $status = $game['status'] ?? 'development';
                                        $approvalStatus = $game['approval_status'] ?? 'pending';
                                        if ($status === 'published' && $approvalStatus === 'approved') {
                                            echo '<span style="color: #00ff88;">✅ Publié</span>';
                                        } else {
                                            echo '<span style="color: #ffaa00;">⏳ En attente</span>';
                                        }
                                        ?>
                                    </div>
                                    <?php 
                                    $ratingAvg = $game['rating_average'] ?? ($game['average_rating'] ?? 0);
                                    $ratingCount = $game['rating_count'] ?? 0;
                                    if ($ratingAvg > 0): 
                                        $fullStars = floor($ratingAvg);
                                        $hasHalfStar = ($ratingAvg - $fullStars) >= 0.5;
                                    ?>
                                        <div style="color: #ffd700; font-size: 12px; margin-top: 5px;">
                                            <?php 
                                            echo str_repeat('★', $fullStars);
                                            if ($hasHalfStar) echo '½';
                                            echo str_repeat('☆', 5 - $fullStars - ($hasHalfStar ? 1 : 0));
                                            ?>
                                            <span style="color: #a0a0a0; margin-left: 5px;">(<?php echo number_format($ratingAvg, 1); ?>) - <?php echo $ratingCount; ?> avis</span>
                                        </div>
                                    <?php else: ?>
                                        <div style="color: #a0a0a0; font-size: 11px; margin-top: 5px;">
                                            ⭐ Aucune note
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if(!empty($game['description'])): ?>
                                <p style="color: #cccccc; font-size: 13px; line-height: 1.5; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars(substr($game['description'], 0, 150)); ?>...
                                </p>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 10px;">
                                <button onclick="showGameDetails(<?php echo htmlspecialchars(json_encode($game, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)); ?>, <?php echo $gameUser ? htmlspecialchars(json_encode($gameUser, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)) : 'null'; ?>)" 
                                        style="flex: 1; padding: 10px; background: linear-gradient(135deg, #00c8ff, #9333ea); color: white; text-align: center; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                                    👁️ Voir Plus
                                </button>
                                <a href="?action=admin_games" 
                                   style="flex: 1; padding: 10px; background: rgba(232, 121, 249, 0.2); color: #e879f9; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; border: 1px solid rgba(232, 121, 249, 0.3);">
                                    📋 Gérer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($success && empty($games)): ?>
            <div class="admin-card" style="padding: 30px; border: 2px solid rgba(255, 170, 0, 0.3); text-align: center;">
                <div style="font-size: 48px; margin-bottom: 20px;">🔍</div>
                <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucun jeu trouvé</h3>
                <p style="color: #a0a0a0;">Aucun jeu ne correspond à vos critères de recherche. Essayez d'autres mots-clés ou une autre catégorie.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal pour les détails du jeu (réutiliser celle de games.php) -->
<div id="gameDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: rgba(26, 26, 26, 0.98); border: 2px solid rgba(232, 121, 249, 0.3); border-radius: 20px; padding: 40px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="hideGameDetails()" style="position: absolute; top: 20px; right: 20px; background: rgba(255, 68, 68, 0.2); border: 1px solid #ff4444; color: #ff4444; width: 40px; height: 40px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
        
        <h2 id="gameDetailsTitle" style="color: #e879f9; margin-bottom: 30px; font-size: 28px; padding-right: 50px;"></h2>
        
        <div id="gameDetailsContent"></div>
    </div>
</div>

<script>
function showGameDetails(game, gameUser) {
    const modal = document.getElementById('gameDetailsModal');
    const title = document.getElementById('gameDetailsTitle');
    const content = document.getElementById('gameDetailsContent');
    
    if (!modal || !title || !content) return;
    
    title.textContent = game.name || 'Jeu sans nom';
    
    let html = '<div style="display: flex; flex-direction: column; gap: 20px;">';
    
    // Image (même logique que games.php)
    if (game.image_url) {
        let imageUrl = game.image_url || '';
        let finalImageUrl = '';
        if (imageUrl.indexOf('/game-masters/') === 0) {
            finalImageUrl = imageUrl;
        } else if (imageUrl && (imageUrl.indexOf('http://') === 0 || imageUrl.indexOf('https://') === 0)) {
            finalImageUrl = imageUrl;
        } else {
            finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
        }
        html += '<img src="' + finalImageUrl + '" alt="' + (game.name || 'Jeu') + '" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; border: 2px solid rgba(232, 121, 249, 0.3); display: block;" onerror="this.onerror=null; this.src=\'/game-masters/public/assets/img/dev1.jpg\';">';
    }
    
    // Description
    if (game.description) {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Description</div><div style="color: #e0e0e0; font-size: 14px; line-height: 1.6;">' + game.description + '</div></div>';
    }
    
    // Catégorie
    if (game.category_name) {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Catégorie</div><div style="color: #00ff88; font-weight: 600; font-size: 16px;">' + game.category_name + '</div></div>';
    }
    
    // Statut
    const status = game.status || 'development';
    const approvalStatus = game.approval_status || 'pending';
    if (status === 'published' && approvalStatus === 'approved') {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Statut</div><div style="color: #00ff88; font-weight: 600; font-size: 16px;">✅ Publié et Approuvé</div></div>';
    } else {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Statut</div><div style="color: #ffaa00; font-weight: 600; font-size: 16px;">⏳ En attente d\'approbation</div></div>';
    }
    
    // Vidéo
    if (game.demo_url) {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Vidéo de démonstration</div><div>';
        if (game.demo_url.indexOf('youtube.com') !== -1 || game.demo_url.indexOf('youtu.be') !== -1) {
            let embedUrl = game.demo_url;
            if (game.demo_url.indexOf('youtu.be/') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('youtu.be/')[1].split('?')[0];
            } else if (game.demo_url.indexOf('youtube.com/watch?v=') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('v=')[1].split('&')[0];
            }
            html += '<iframe width="100%" height="400" src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 8px;"></iframe>';
        } else {
            html += '<video width="100%" height="400" controls style="border-radius: 8px;"><source src="' + game.demo_url + '" type="video/mp4">Votre navigateur ne supporte pas la lecture de vidéos.</video>';
        }
        html += '</div></div>';
    }
    
    // Utilisateur
    if (gameUser && gameUser.username) {
        html += '<div><div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Soumis par</div><div style="color: #9333ea; font-weight: 600; font-size: 16px;">' + gameUser.username + '</div></div>';
    }
    
    html += '</div>';
    content.innerHTML = html;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideGameDetails() {
    const modal = document.getElementById('gameDetailsModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Fermer la modal en cliquant en dehors
document.addEventListener('click', function(e) {
    const modal = document.getElementById('gameDetailsModal');
    if (modal && e.target === modal) {
        hideGameDetails();
    }
});
</script>

<?php include "views/admin/includes/footer.php"; ?>

