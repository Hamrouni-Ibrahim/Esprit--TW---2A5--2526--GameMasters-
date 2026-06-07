<?php 
// Forcer l'affichage des erreurs pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Gestion des Jeux - Admin';
$currentPage = 'games';

// Vérifier que les variables existent
if (!isset($games)) $games = [];
if (!isset($pendingGames)) $pendingGames = [];
if (!isset($gameStats)) $gameStats = ['total' => 0, 'published' => 0, 'pending' => 0];
if (!isset($message)) $message = null;
if (!isset($success)) $success = true;

include "views/admin/includes/header.php"; 
?>
<style>
    .admin-game-card {
        position: relative;
        overflow: hidden;
    }
    
    .admin-game-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(232, 121, 249, 0.1) 0%, rgba(147, 51, 234, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 0;
    }
    
    .admin-game-card:hover::before {
        opacity: 1;
    }
    
    .admin-game-card > * {
        position: relative;
        z-index: 1;
    }
    
    /* Responsive search form */
    @media (max-width: 992px) {
        .admin-search-form {
            grid-template-columns: 1fr !important;
        }
        
        .admin-search-form .search-input-container {
            width: 100% !important;
        }
        
        .admin-search-form button,
        .admin-search-form a {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 768px) {
        .admin-search-form {
            gap: 10px !important;
        }
    }
</style>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
        <div class="admin-shape shape4"></div>
        <div class="admin-shape shape5"></div>
        <div class="admin-shape shape6"></div>
    </div>
    <div class="admin-particles" id="adminParticles"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>Gestion des Jeux</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gérez le catalogue de jeux Game Masters</p>
</div>

<?php if(isset($message)): ?>
            <div class="alert" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; <?php echo $success ? 'background: rgba(0, 255, 136, 0.2); color: #00ff88; border: 1px solid #00ff88;' : 'background: rgba(255, 68, 68, 0.2); color: #ff4444; border: 1px solid #ff4444;'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

        <!-- Jeux en attente d'approbation -->
        <?php if(!empty($pendingGames)): ?>
        <div class="admin-card" style="margin-bottom: 30px; border: 2px solid rgba(255, 170, 0, 0.3); padding: 20px; border-radius: 15px; background: rgba(255, 255, 255, 0.02);">
            <h3 style="color: #ffaa00; margin-bottom: 20px;">⏳ Jeux en Attente d'Approbation (<?php echo count($pendingGames); ?>)</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                <?php 
                require_once "models/User.php";
                // S'assurer que $db est disponible
                if (!isset($db)) {
                    require_once "config/database.php";
                    $database = new Database();
                    $db = $database->getConnection();
                }
                foreach($pendingGames as $game): 
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
                    
                    // Gérer l'image
                    $imageUrl = $game['image_url'] ?? '';
                    if (strpos($imageUrl, '/game-masters/') === 0) {
                        $finalImageUrl = $imageUrl;
                    } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $finalImageUrl = $imageUrl;
                    } else {
                        $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                    }
                ?>
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 170, 0, 0.5); border-radius: 12px; padding: 15px; position: relative;">
                        <!-- Image du jeu -->
                        <div style="width: 100%; height: 180px; border-radius: 8px; overflow: hidden; margin-bottom: 15px; background: rgba(26, 26, 26, 0.8);">
                            <img src="<?php echo $finalImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($game['name'] ?? ''); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg'">
                        </div>
                        
                        <h4 style="color: #fff; margin-bottom: 10px; font-size: 18px;"><?php echo htmlspecialchars($game['name'] ?? 'Sans nom'); ?></h4>
                        
                        <div style="display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;">
                            <span style="padding: 4px 10px; background: rgba(153, 69, 255, 0.2); color: #9333ea; border-radius: 12px; font-size: 12px; border: 1px solid #9333ea;">
                                <?php echo htmlspecialchars($game['impact_social'] ?? 'N/A'); ?>
                            </span>
                            <?php if(!empty($game['category_name'])): ?>
                                <span style="padding: 4px 10px; background: rgba(0, 200, 255, 0.2); color: #00c8ff; border-radius: 12px; font-size: 12px; border: 1px solid #00c8ff;">
                                    📁 <?php echo htmlspecialchars($game['category_name']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 15px; line-height: 1.5;">
                            <?php echo htmlspecialchars(substr($game['description'] ?? '', 0, 120)); ?>
                            <?php if(strlen($game['description'] ?? '') > 120): ?>...<?php endif; ?>
                        </p>
                        
                        <?php if($gameUser): ?>
                            <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(153, 69, 255, 0.1); border-radius: 8px; margin-bottom: 15px; border: 1px solid rgba(153, 69, 255, 0.2);">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #9333ea, #c084fc); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                    <?php echo strtoupper(substr($gameUser['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="color: #a0a0a0; font-size: 11px; text-transform: uppercase;">Soumis par</div>
                                    <div style="color: #9333ea; font-weight: 600; font-size: 13px;"><?php echo htmlspecialchars($gameUser['username'] ?? 'Utilisateur'); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; gap: 8px; flex-direction: column;">
                            <button onclick="showGameDetails(<?php echo htmlspecialchars(json_encode($game, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)); ?>, <?php echo $gameUser ? htmlspecialchars(json_encode($gameUser, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)) : 'null'; ?>)" 
                                    style="width: 100%; padding: 10px; background: linear-gradient(135deg, #00c8ff, #9333ea); color: white; text-align: center; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                                👁️ Voir Plus
                            </button>
                            <div style="display: flex; gap: 8px;">
                                <a href="?action=admin_games&approve_game=1&id=<?php echo $game['id']; ?>" 
                                   onclick="return confirm('✅ Approuver ce jeu ?\n\nLe jeu sera publié et visible par tous les utilisateurs.');"
                                   style="flex: 1; padding: 10px; background: #00ff88; color: #000; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                    ✅ Approuver
                                </a>
                                <a href="?action=admin_games&reject_game=1&id=<?php echo $game['id']; ?>" 
                                   onclick="return confirm('❌ Rejeter ce jeu ?\n\nLe jeu sera rejeté et ne sera pas publié.');"
                                   style="flex: 1; padding: 10px; background: #ff4444; color: #fff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                    ❌ Rejeter
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Séparer les jeux en catégories : Admin, Utilisateurs, et En attente
        $publishedAdminGames = [];
        $publishedUserGames = [];
        $waitingGames = [];
        
        foreach($games as $game) {
            $status = $game['status'] ?? 'development';
            $approvalStatus = $game['approval_status'] ?? 'pending';
            $userId = $game['user_id'] ?? null;
            
            // Jeux publiés : status = 'published' ET approval_status = 'approved'
            if ($status === 'published' && $approvalStatus === 'approved') {
                // Séparer les jeux admin (user_id IS NULL) des jeux utilisateurs
                if ($userId === null) {
                    $publishedAdminGames[] = $game;
                } else {
                    $publishedUserGames[] = $game;
                }
            } else {
                // Tous les autres jeux (en attente, en développement, etc.)
                $waitingGames[] = $game;
            }
        }
        ?>

        <!-- Barre de recherche -->
        <div class="admin-card" style="padding: 20px; border-radius: 15px; background: rgba(255, 255, 255, 0.02); margin-bottom: 30px; border: 2px solid rgba(232, 121, 249, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 15px;">
                <h3 style="color: #e879f9; margin: 0;">
                    <i class="fas fa-search" style="margin-right: 10px;"></i>
                    Recherche de Jeux
                </h3>
            </div>
            <form method="GET" action="?action=admin_games" class="admin-search-form" style="display: grid; grid-template-columns: 1fr 280px auto auto; gap: 15px; align-items: end; flex-wrap: wrap;">
                <input type="hidden" name="action" value="admin_games">
                
                <!-- Recherche par nom -->
                <div class="search-input-container" style="position: relative;">
                    <input type="text" name="search_term" id="searchGameName" 
                           placeholder="Rechercher un jeu par nom..." 
                           value="<?php echo isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : ''; ?>"
                           class="form-control" 
                           style="width: 100%; padding: 12px 15px 12px 45px;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(232, 121, 249, 0.6); pointer-events: none; z-index: 1;"></i>
                </div>
                
                <!-- Recherche par catégorie -->
                <div class="search-input-container" style="position: relative; width: 100%;">
                    <select name="category_id" id="searchCategory" class="form-control" 
                            style="width: 100%; padding: 12px 45px 12px 15px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%); border: 1.5px solid rgba(232, 121, 249, 0.3); border-radius: 12px; color: #ffffff; font-size: 14px; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23e879f9\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 15px center; background-size: 16px;">
                        <option value="">Toutes les catégories</option>
                        <?php if(isset($categories) && !empty($categories)): ?>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                                        <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <i class="fas fa-filter" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(232, 121, 249, 0.6); pointer-events: none; z-index: 1;"></i>
                </div>
                
                <!-- Boutons -->
                <button type="submit" class="btn-admin btn-edit" style="padding: 12px 20px; white-space: nowrap;">
                    <i class="fas fa-search" style="margin-right: 5px;"></i> Rechercher
                </button>
                <?php if((isset($_GET['category_id']) && $_GET['category_id'] != '') || (isset($_GET['search_term']) && $_GET['search_term'] != '')): ?>
                    <a href="?action=admin_games" class="btn-admin btn-delete" style="padding: 12px 20px; text-decoration: none; white-space: nowrap;">
                        <i class="fas fa-times" style="margin-right: 5px;"></i> Réinitialiser
                    </a>
                <?php else: ?>
                    <div style="width: 0; visibility: hidden;"></div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Catalogue des Jeux Publiés par l'Admin -->
        <div class="admin-card" style="padding: 20px; border-radius: 15px; background: rgba(255, 255, 255, 0.02); margin-bottom: 30px; border: 2px solid rgba(147, 51, 234, 0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h3 style="color: #9333ea;">
                    <i class="fas fa-crown" style="margin-right: 10px;"></i>
                    Jeux Publiés (Admin) (<?php echo count($publishedAdminGames); ?>)
                </h3>
                <div style="display: flex; gap: 15px; align-items: center;">
                <a href="?action=add_game" style="padding: 10px 20px; background: linear-gradient(135deg, #9333ea, #c084fc); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-plus" style="margin-right: 5px;"></i> Ajouter un Jeu
                </a>
                </div>
            </div>

            <?php if(empty($publishedAdminGames)): ?>
                <div style="text-align: center; padding: 60px; color: #a0a0a0;">
                    <div style="font-size: 48px; margin-bottom: 20px;">👑</div>
                    <h3 style="color: #e0e0e0; margin-bottom: 10px;">Aucun jeu publié par l'admin</h3>
                    <p>Les jeux que vous ajoutez apparaîtront ici.</p>
        </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($publishedAdminGames as $game):
                        // Gérer l'image
                        $imageUrl = $game['image_url'] ?? '';
                        if (strpos($imageUrl, '/game-masters/') === 0) {
                            $finalImageUrl = $imageUrl;
                        } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $finalImageUrl = $imageUrl;
                        } else {
                            $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                        }
                        
                        // Préparer les données pour la modal
                        $gameData = [
                            'id' => $game['id'],
                            'name' => $game['name'] ?? 'Sans nom',
                            'description' => $game['description'] ?? '',
                            'impact_social' => $game['impact_social'] ?? 'N/A',
                            'status' => $game['status'] ?? 'development',
                            'category_name' => $game['category_name'] ?? 'Aucune catégorie',
                            'image_url' => $finalImageUrl,
                            'demo_url' => $game['demo_url'] ?? '',
                            'user_id' => $game['user_id'] ?? null
                        ];
                    ?>
                        <div class="admin-game-card" 
                             style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                             onclick="showAdminGameDetails(<?php echo htmlspecialchars(json_encode($gameData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)); ?>)"
                             onmouseover="this.style.borderColor='rgba(232, 121, 249, 0.5)'; this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(0)';">
                            <!-- Image du jeu -->
                            <div style="width: 100%; height: 150px; border-radius: 8px; overflow: hidden; margin-bottom: 15px; background: rgba(26, 26, 26, 0.8);">
                                <img src="<?php echo $finalImageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($game['name'] ?? ''); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg'">
                            </div>
                            
                            <h4 style="color: #9333ea; margin-bottom: 10px;"><?php echo htmlspecialchars($game['name'] ?? 'Sans nom'); ?></h4>
                            <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 10px;"><?php echo htmlspecialchars(substr($game['description'] ?? '', 0, 100)); ?>...</p>
                            <div style="display: flex; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
                                <span style="padding: 4px 10px; background: rgba(147, 51, 234, 0.3); color: #c084fc; border-radius: 12px; font-size: 12px; border: 1px solid rgba(147, 51, 234, 0.5);">
                                    👑 Admin
                                </span>
                                <span style="padding: 4px 10px; background: rgba(153, 69, 255, 0.2); color: #9333ea; border-radius: 12px; font-size: 12px;">
                                    <?php echo htmlspecialchars($game['impact_social'] ?? 'N/A'); ?>
                                </span>
                                <span style="padding: 4px 10px; background: rgba(0, 200, 255, 0.2); color: #00c8ff; border-radius: 12px; font-size: 12px;">
                                    <?php echo htmlspecialchars($game['status'] ?? 'development'); ?>
                            </span>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button onclick="event.stopPropagation(); window.location.href='?action=admin_edit_game&id=<?php echo $game['id']; ?>';" 
                                        style="flex: 1; min-width: 100px; padding: 8px; background: linear-gradient(135deg, #9333ea, #c084fc); color: white; text-align: center; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                                    ✏️ Modifier
                                </button>
                                <?php if(($game['status'] ?? '') !== 'published'): ?>
                                    <a href="?action=admin_games&publish_game=1&id=<?php echo $game['id']; ?>" 
                                       onclick="event.stopPropagation(); return confirm('Publier ce jeu ?');"
                                       style="flex: 1; min-width: 100px; padding: 8px; background: #00ff88; color: #000; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                        🚀 Publier
                                    </a>
                                <?php endif; ?>
                                <a href="?action=admin_games&delete_game=1&id=<?php echo $game['id']; ?>" 
                                   onclick="event.stopPropagation(); return confirm('Supprimer ce jeu ?')"
                                   style="flex: 1; min-width: 100px; padding: 8px; background: #ff4444; color: #fff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                    🗑️ Supprimer
                                </a>
                            </div>
                            <div style="text-align: center; margin-top: 10px; color: rgba(147, 51, 234, 0.7); font-size: 12px;">
                                👆 Cliquez pour voir les détails
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Catalogue des Jeux Publiés par les Utilisateurs -->
        <div class="admin-card" style="padding: 20px; border-radius: 15px; background: rgba(255, 255, 255, 0.02); margin-bottom: 30px; border: 2px solid rgba(0, 255, 136, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h3 style="color: #00ff88;">
                    <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                    Jeux Publiés (Utilisateurs) (<?php echo count($publishedUserGames); ?>)
                </h3>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="index.php?action=admin_export_games_pdf" class="btn-admin btn-edit" target="_blank" style="text-decoration: none;">
                        <i class="fas fa-file-pdf"></i> Liste
                    </a>
                </div>
            </div>

            <?php if(empty($publishedUserGames)): ?>
                <div style="text-align: center; padding: 60px; color: #a0a0a0;">
                    <div style="font-size: 48px; margin-bottom: 20px;">📭</div>
                    <h3 style="color: #e0e0e0; margin-bottom: 10px;">Aucun jeu publié par les utilisateurs</h3>
                    <p>Les jeux approuvés et publiés par les utilisateurs apparaîtront ici.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($publishedUserGames as $game):
                        // Gérer l'image
                        $imageUrl = $game['image_url'] ?? '';
                        if (strpos($imageUrl, '/game-masters/') === 0) {
                            $finalImageUrl = $imageUrl;
                        } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $finalImageUrl = $imageUrl;
                        } else {
                            $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                        }
                        
                        // Préparer les données pour la modal
                        $gameData = [
                            'id' => $game['id'],
                            'name' => $game['name'] ?? 'Sans nom',
                            'description' => $game['description'] ?? '',
                            'impact_social' => $game['impact_social'] ?? 'N/A',
                            'status' => $game['status'] ?? 'development',
                            'category_name' => $game['category_name'] ?? 'Aucune catégorie',
                            'image_url' => $finalImageUrl,
                            'demo_url' => $game['demo_url'] ?? '',
                            'user_id' => $game['user_id'] ?? null
                        ];
                    ?>
                        <div class="admin-game-card" 
                             style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                             onclick="showAdminGameDetails(<?php echo htmlspecialchars(json_encode($gameData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)); ?>)"
                             onmouseover="this.style.borderColor='rgba(0, 255, 136, 0.5)'; this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(0)';">
                            <!-- Image du jeu -->
                            <div style="width: 100%; height: 150px; border-radius: 8px; overflow: hidden; margin-bottom: 15px; background: rgba(26, 26, 26, 0.8);">
                                <img src="<?php echo $finalImageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($game['name'] ?? ''); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg'">
                            </div>
                            
                            <h4 style="color: #00c8ff; margin-bottom: 10px;"><?php echo htmlspecialchars($game['name'] ?? 'Sans nom'); ?></h4>
                            <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 10px;"><?php echo htmlspecialchars(substr($game['description'] ?? '', 0, 100)); ?>...</p>
                            <div style="display: flex; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
                                <span style="padding: 4px 10px; background: rgba(153, 69, 255, 0.2); color: #9333ea; border-radius: 12px; font-size: 12px;">
                                    <?php echo htmlspecialchars($game['impact_social'] ?? 'N/A'); ?>
                                </span>
                                <span style="padding: 4px 10px; background: rgba(0, 200, 255, 0.2); color: #00c8ff; border-radius: 12px; font-size: 12px;">
                                    <?php echo htmlspecialchars($game['status'] ?? 'development'); ?>
                            </span>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button onclick="event.stopPropagation(); window.location.href='?action=admin_edit_game&id=<?php echo $game['id']; ?>';" 
                                        style="flex: 1; min-width: 100px; padding: 8px; background: linear-gradient(135deg, #9333ea, #c084fc); color: white; text-align: center; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                                    ✏️ Modifier
                                </button>
                                <?php if(($game['status'] ?? '') !== 'published'): ?>
                                    <a href="?action=admin_games&publish_game=1&id=<?php echo $game['id']; ?>" 
                                       onclick="event.stopPropagation(); return confirm('Publier ce jeu ?');"
                                       style="flex: 1; min-width: 100px; padding: 8px; background: #00ff88; color: #000; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                        🚀 Publier
                                    </a>
                                <?php endif; ?>
                                <a href="?action=admin_games&delete_game=1&id=<?php echo $game['id']; ?>" 
                                   onclick="event.stopPropagation(); return confirm('Supprimer ce jeu ?')"
                                   style="flex: 1; min-width: 100px; padding: 8px; background: #ff4444; color: #fff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                    🗑️ Supprimer
                                </a>
                            </div>
                            <div style="text-align: center; margin-top: 10px; color: rgba(0, 255, 136, 0.7); font-size: 12px;">
                                👆 Cliquez pour voir les détails
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Catalogue des Jeux en Attente -->
        <div class="admin-card" style="padding: 20px; border-radius: 15px; background: rgba(255, 255, 255, 0.02); margin-bottom: 30px; border: 2px solid rgba(255, 170, 0, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #ffaa00;">
                    <i class="fas fa-clock" style="margin-right: 10px;"></i>
                    Jeux en Attente (<?php echo count($waitingGames); ?>)
                </h3>
            </div>

            <?php if(empty($waitingGames)): ?>
                <div style="text-align: center; padding: 60px; color: #a0a0a0;">
                    <div style="font-size: 48px; margin-bottom: 20px;">✅</div>
                    <h3 style="color: #e0e0e0; margin-bottom: 10px;">Aucun jeu en attente</h3>
                    <p>Tous les jeux sont publiés ou approuvés.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($waitingGames as $game):
                        // Gérer l'image
                        $imageUrl = $game['image_url'] ?? '';
                        if (strpos($imageUrl, '/game-masters/') === 0) {
                            $finalImageUrl = $imageUrl;
                        } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $finalImageUrl = $imageUrl;
                        } else {
                            $finalImageUrl = '/game-masters/public/assets/img/dev1.jpg';
                        }
                        
                        // Préparer les données pour la modal
                        $gameData = [
                            'id' => $game['id'],
                            'name' => $game['name'] ?? 'Sans nom',
                            'description' => $game['description'] ?? '',
                            'impact_social' => $game['impact_social'] ?? 'N/A',
                            'status' => $game['status'] ?? 'development',
                            'category_name' => $game['category_name'] ?? 'Aucune catégorie',
                            'image_url' => $finalImageUrl,
                            'demo_url' => $game['demo_url'] ?? '',
                            'user_id' => $game['user_id'] ?? null,
                            'approval_status' => $game['approval_status'] ?? 'pending'
                        ];
                    ?>
                        <div class="admin-game-card" 
                             style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 170, 0, 0.3); border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                             onclick="showAdminGameDetails(<?php echo htmlspecialchars(json_encode($gameData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)); ?>)"
                             onmouseover="this.style.borderColor='rgba(255, 170, 0, 0.5)'; this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.borderColor='rgba(255, 170, 0, 0.3)'; this.style.transform='translateY(0)';">
                            <!-- Image du jeu -->
                            <div style="width: 100%; height: 150px; border-radius: 8px; overflow: hidden; margin-bottom: 15px; background: rgba(26, 26, 26, 0.8);">
                                <img src="<?php echo $finalImageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($game['name'] ?? ''); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.onerror=null; this.src='/game-masters/public/assets/img/dev1.jpg'">
                            </div>
                            
                            <h4 style="color: #ffaa00; margin-bottom: 10px;"><?php echo htmlspecialchars($game['name'] ?? 'Sans nom'); ?></h4>
                            <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 10px;"><?php echo htmlspecialchars(substr($game['description'] ?? '', 0, 100)); ?>...</p>
                            <div style="display: flex; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
                                <span style="padding: 4px 10px; background: rgba(153, 69, 255, 0.2); color: #9333ea; border-radius: 12px; font-size: 12px;">
                                    <?php echo htmlspecialchars($game['impact_social'] ?? 'N/A'); ?>
                                </span>
                                <span style="padding: 4px 10px; background: rgba(255, 170, 0, 0.2); color: #ffaa00; border-radius: 12px; font-size: 12px;">
                                    <?php 
                                    $statusText = $game['status'] ?? 'development';
                                    if ($statusText === 'development') echo 'En développement';
                                    else if ($statusText === 'pending') echo 'En attente';
                                    else echo htmlspecialchars($statusText);
                                    ?>
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button onclick="event.stopPropagation(); window.location.href='?action=admin_edit_game&id=<?php echo $game['id']; ?>';" 
                                        style="flex: 1; min-width: 100px; padding: 8px; background: linear-gradient(135deg, #9333ea, #c084fc); color: white; text-align: center; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                                    ✏️ Modifier
                                </button>
                                <?php if(($game['status'] ?? '') !== 'published'): ?>
                                    <a href="?action=admin_games&publish_game=1&id=<?php echo $game['id']; ?>" 
                                       onclick="event.stopPropagation(); return confirm('Publier ce jeu ?');"
                                       style="flex: 1; min-width: 100px; padding: 8px; background: #00ff88; color: #000; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                        🚀 Publier
                                    </a>
                                <?php endif; ?>
                                <a href="?action=admin_games&delete_game=1&id=<?php echo $game['id']; ?>" 
                                   onclick="event.stopPropagation(); return confirm('Supprimer ce jeu ?')"
                                   style="flex: 1; min-width: 100px; padding: 8px; background: #ff4444; color: #fff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                                    🗑️ Supprimer
                                </a>
                            </div>
                            <div style="text-align: center; margin-top: 10px; color: rgba(255, 170, 0, 0.7); font-size: 12px;">
                                👆 Cliquez pour voir les détails
                            </div>
                        </div>
                    <?php endforeach; ?>
    </div>
    <?php endif; ?>
        </div>
    </div>
    
        <!-- Statistiques -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 30px;">
            <div style="background: linear-gradient(135deg, rgba(42, 42, 42, 0.5), rgba(26, 26, 26, 0.8)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; text-align: center;">
                <div style="font-size: 36px; font-weight: 900; color: #fff; margin-bottom: 8px;"><?php echo $gameStats['total'] ?? '0'; ?></div>
                <div style="font-size: 12px; color: #a0a0a0; text-transform: uppercase;">Total Jeux</div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(42, 42, 42, 0.5), rgba(26, 26, 26, 0.8)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; text-align: center;">
                <div style="font-size: 36px; font-weight: 900; color: #fff; margin-bottom: 8px;"><?php echo $gameStats['published'] ?? '0'; ?></div>
                <div style="font-size: 12px; color: #a0a0a0; text-transform: uppercase;">Jeux Publiés</div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(42, 42, 42, 0.5), rgba(26, 26, 26, 0.8)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; text-align: center;">
                <div style="font-size: 36px; font-weight: 900; color: #fff; margin-bottom: 8px;"><?php echo $gameStats['pending'] ?? '0'; ?></div>
                <div style="font-size: 12px; color: #a0a0a0; text-transform: uppercase;">En Attente</div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Détails du Jeu -->
<div id="gameDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 10000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
    <div style="background: rgba(26, 26, 26, 0.98); border: 2px solid rgba(232, 121, 249, 0.3); border-radius: 20px; padding: 40px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="hideGameDetails()" style="position: absolute; top: 20px; right: 20px; background: rgba(255, 68, 68, 0.2); border: 1px solid #ff4444; color: #ff4444; width: 40px; height: 40px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
        
        <h2 id="gameDetailsTitle" style="color: #e879f9; margin-bottom: 30px; font-size: 28px; padding-right: 50px;"></h2>
        
        <div style="width: 100%; height: 300px; border-radius: 12px; overflow: hidden; margin-bottom: 25px; background: rgba(26, 26, 26, 0.8);">
            <img id="gameDetailsImg" src="" alt="Game Image" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(153, 69, 255, 0.1); border: 1px solid rgba(153, 69, 255, 0.3); border-radius: 10px; padding: 15px;">
                <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Impact Social</div>
                <div id="gameDetailsImpact" style="color: #9333ea; font-weight: 600; font-size: 16px;"></div>
            </div>
            <div style="background: rgba(0, 200, 255, 0.1); border: 1px solid rgba(0, 200, 255, 0.3); border-radius: 10px; padding: 15px;">
                <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Statut</div>
                <div id="gameDetailsStatus" style="color: #00c8ff; font-weight: 600; font-size: 16px;"></div>
            </div>
            <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.3); border-radius: 10px; padding: 15px;">
                <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Catégorie</div>
                <div id="gameDetailsCategory" style="color: #00ff88; font-weight: 600; font-size: 16px;"></div>
            </div>
        </div>
        
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <h3 style="color: #e879f9; margin-bottom: 15px; font-size: 18px;">📝 Description</h3>
            <p id="gameDetailsDescription" style="color: #e0e0e0; font-size: 15px; line-height: 1.8; white-space: pre-wrap;"></p>
        </div>
        
        <div id="gameDetailsUser" style="display: none; background: rgba(153, 69, 255, 0.1); border: 1px solid rgba(153, 69, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <h3 style="color: #9333ea; margin-bottom: 15px; font-size: 18px;">👤 Informations du Contributeur</h3>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div id="gameDetailsUserAvatar" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #9333ea, #c084fc); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px; border: 3px solid #9333ea;"></div>
                <div style="flex: 1;">
                    <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Nom d'utilisateur</div>
                    <div id="gameDetailsUsername" style="color: #9333ea; font-weight: 600; font-size: 18px;"></div>
                    <div style="color: #a0a0a0; font-size: 13px; margin-top: 5px;">
                        Email: <span id="gameDetailsEmail" style="color: #e0e0e0;"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="gameDetailsDemo" style="display: none; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <h3 style="color: #e879f9; margin-bottom: 15px; font-size: 18px;">🎥 Vidéo de Démonstration</h3>
            <div id="gameDetailsDemoContent"></div>
        </div>
        
        <!-- Section des notes des utilisateurs -->
        <div id="gameDetailsRatings" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <h3 style="color: #ffd700; margin-bottom: 15px; font-size: 18px;">⭐ Notes des Utilisateurs</h3>
            <div id="gameDetailsRatingsContent" style="max-height: 300px; overflow-y: auto;">
                <div style="text-align: center; padding: 20px; color: #a0a0a0;">
                    <div class="spinner" style="display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-top-color: #ffd700; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 10px;">Chargement des notes...</p>
                </div>
            </div>
        </div>
        
        <style>
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            .rating-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px;
                margin-bottom: 10px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            .rating-item:hover {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 215, 0, 0.3);
            }
            .rating-user-info {
                display: flex;
                align-items: center;
                gap: 12px;
                flex: 1;
            }
            .rating-stars {
                color: #ffd700;
                font-size: 18px;
                margin-right: 15px;
            }
            .rating-date {
                color: #a0a0a0;
                font-size: 12px;
            }
        </style>
        
        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a id="gameDetailsApproveBtn" href="#" 
               onclick="return confirm('✅ Approuver ce jeu ?\n\nLe jeu sera publié et visible par tous les utilisateurs.');"
               style="flex: 1; padding: 15px; background: linear-gradient(135deg, #00ff88, #00cc88); color: #000; text-align: center; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 15px rgba(0, 255, 136, 0.3);">
                ✅ Approuver le Jeu
            </a>
            <a id="gameDetailsRejectBtn" href="#" 
               onclick="return confirm('❌ Rejeter ce jeu ?\n\nLe jeu sera rejeté et ne sera pas publié.');"
               style="flex: 1; padding: 15px; background: linear-gradient(135deg, #ff4444, #cc0000); color: #fff; text-align: center; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);">
                ❌ Rejeter le Jeu
            </a>
        </div>
    </div>
</div>

<script>
// Cacher le loader immédiatement
(function() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
})();

// Fonction pour afficher les détails du jeu
function showGameDetails(game, user) {
    const modal = document.getElementById('gameDetailsModal');
    if (!modal) return;
    
    // Remplir les informations
    document.getElementById('gameDetailsTitle').textContent = game.name || 'Sans nom';
    document.getElementById('gameDetailsDescription').textContent = game.description || 'Aucune description';
    document.getElementById('gameDetailsImpact').textContent = game.impact_social || 'N/A';
    
    let statusText = 'En attente';
    if (game.status === 'published') statusText = 'Publié';
    else if (game.status === 'development') statusText = 'En développement';
    else if (game.status === 'archived') statusText = 'Archivé';
    document.getElementById('gameDetailsStatus').textContent = statusText;
    document.getElementById('gameDetailsCategory').textContent = game.category_name || 'Aucune catégorie';
    
    // Image
    const img = document.getElementById('gameDetailsImg');
    let imageUrl = game.image_url || '';
    if (imageUrl.indexOf('/game-masters/') === 0) {
        img.src = imageUrl;
    } else if (imageUrl && (imageUrl.indexOf('http://') === 0 || imageUrl.indexOf('https://') === 0)) {
        img.src = imageUrl;
    } else {
        img.src = '/game-masters/public/assets/img/dev1.jpg';
    }
    img.onerror = function() {
        this.src = '/game-masters/public/assets/img/dev1.jpg';
    };
    
    // Informations utilisateur
    const userDiv = document.getElementById('gameDetailsUser');
    if (user && game.user_id && game.user_id > 0) {
        userDiv.style.display = 'block';
        document.getElementById('gameDetailsUsername').textContent = user.username || 'Utilisateur';
        document.getElementById('gameDetailsEmail').textContent = user.email || 'N/A';
        const avatar = document.getElementById('gameDetailsUserAvatar');
        avatar.textContent = (user.username || 'U').charAt(0).toUpperCase();
    } else {
        userDiv.style.display = 'none';
    }
    
    // Vidéo de démonstration
    const demoDiv = document.getElementById('gameDetailsDemo');
    const demoContent = document.getElementById('gameDetailsDemoContent');
    if (game.demo_url) {
        demoDiv.style.display = 'block';
        if (game.demo_url.indexOf('youtube.com') !== -1 || game.demo_url.indexOf('youtu.be') !== -1) {
            // Convertir URL YouTube en embed
            let embedUrl = game.demo_url;
            if (game.demo_url.indexOf('youtu.be/') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('youtu.be/')[1].split('?')[0];
            } else if (game.demo_url.indexOf('youtube.com/watch?v=') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('v=')[1].split('&')[0];
            }
            demoContent.innerHTML = '<iframe width="100%" height="400" src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 8px;"></iframe>';
        } else {
            demoContent.innerHTML = '<video width="100%" height="400" controls style="border-radius: 8px;"><source src="' + game.demo_url + '" type="video/mp4">Votre navigateur ne supporte pas la lecture de vidéos.</video>';
        }
    } else {
        demoDiv.style.display = 'none';
    }
    
    // Boutons d'action
    document.getElementById('gameDetailsApproveBtn').href = '?action=admin_games&approve_game=1&id=' + game.id;
    document.getElementById('gameDetailsRejectBtn').href = '?action=admin_games&reject_game=1&id=' + game.id;
    
    // Charger les notes des utilisateurs
    loadGameRatings(game.id);
    
    // Afficher la modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideGameDetails() {
    const modal = document.getElementById('gameDetailsModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Fermer la modal en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const modal = document.getElementById('gameDetailsModal');
    if (event.target === modal) {
        hideGameDetails();
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideGameDetails();
    }
});

// Fonction pour afficher les détails du jeu (pour tous les jeux de la liste principale)
function showAdminGameDetails(game) {
    const modal = document.getElementById('gameDetailsModal');
    if (!modal) return;
    
    // Remplir les informations
    document.getElementById('gameDetailsTitle').textContent = game.name || 'Sans nom';
    document.getElementById('gameDetailsDescription').textContent = game.description || 'Aucune description';
    document.getElementById('gameDetailsImpact').textContent = game.impact_social || 'N/A';
    
    let statusText = 'En attente';
    if (game.status === 'published') statusText = 'Publié';
    else if (game.status === 'development') statusText = 'En développement';
    else if (game.status === 'archived') statusText = 'Archivé';
    document.getElementById('gameDetailsStatus').textContent = statusText;
    document.getElementById('gameDetailsCategory').textContent = game.category_name || 'Aucune catégorie';
    
    // Image
    const img = document.getElementById('gameDetailsImg');
    let imageUrl = game.image_url || '';
    if (imageUrl.indexOf('/game-masters/') === 0) {
        img.src = imageUrl;
    } else if (imageUrl && (imageUrl.indexOf('http://') === 0 || imageUrl.indexOf('https://') === 0)) {
        img.src = imageUrl;
    } else {
        img.src = '/game-masters/public/assets/img/dev1.jpg';
    }
    img.onerror = function() {
        this.src = '/game-masters/public/assets/img/dev1.jpg';
    };
    
    // Informations utilisateur - masquer pour les jeux admin
    const userDiv = document.getElementById('gameDetailsUser');
    if (game.user_id && game.user_id > 0) {
        // Si le jeu a un user_id, on pourrait afficher les infos, mais pour l'instant on masque
        userDiv.style.display = 'none';
    } else {
        userDiv.style.display = 'none';
    }
    
    // Vidéo de démonstration
    const demoDiv = document.getElementById('gameDetailsDemo');
    const demoContent = document.getElementById('gameDetailsDemoContent');
    if (game.demo_url) {
        demoDiv.style.display = 'block';
        if (game.demo_url.indexOf('youtube.com') !== -1 || game.demo_url.indexOf('youtu.be') !== -1) {
            // Convertir URL YouTube en embed
            let embedUrl = game.demo_url;
            if (game.demo_url.indexOf('youtu.be/') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('youtu.be/')[1].split('?')[0];
            } else if (game.demo_url.indexOf('youtube.com/watch?v=') !== -1) {
                embedUrl = 'https://www.youtube.com/embed/' + game.demo_url.split('v=')[1].split('&')[0];
            } else if (game.demo_url.indexOf('youtube.com/embed/') !== -1) {
                embedUrl = game.demo_url;
            }
            demoContent.innerHTML = '<iframe width="100%" height="400" src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 8px;"></iframe>';
        } else {
            demoContent.innerHTML = '<video width="100%" height="400" controls style="border-radius: 8px;"><source src="' + game.demo_url + '" type="video/mp4">Votre navigateur ne supporte pas la lecture de vidéos.</video>';
        }
    } else {
        demoDiv.style.display = 'none';
    }
    
    // Boutons d'action - masquer pour les jeux déjà publiés
    const approveBtn = document.getElementById('gameDetailsApproveBtn');
    const rejectBtn = document.getElementById('gameDetailsRejectBtn');
    
    if (game.status === 'published') {
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'none';
    } else {
        approveBtn.style.display = 'block';
        rejectBtn.style.display = 'block';
        approveBtn.href = '?action=admin_games&approve_game=1&id=' + game.id;
        rejectBtn.href = '?action=admin_games&reject_game=1&id=' + game.id;
    }
    
    // Charger les notes des utilisateurs
    loadGameRatings(game.id);
    
    // Afficher la modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Fonction pour charger les notes des utilisateurs
function loadGameRatings(gameId) {
    const ratingsContent = document.getElementById('gameDetailsRatingsContent');
    if (!ratingsContent) return;
    
    // Afficher le loader
    ratingsContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #a0a0a0;"><div class="spinner" style="display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-top-color: #ffd700; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Chargement des notes...</p></div>';
    
    fetch('?action=admin_get_game_ratings&game_id=' + gameId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.ratings && data.ratings.length > 0) {
                let html = '';
                data.ratings.forEach(rating => {
                    const stars = '★'.repeat(rating.rating) + '☆'.repeat(5 - rating.rating);
                    const date = new Date(rating.created_at).toLocaleDateString('fr-FR', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    html += `
                        <div class="rating-item">
                            <div class="rating-user-info">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #9333ea, #c084fc); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; border: 2px solid #9333ea;">
                                    ${(rating.username || 'U').charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div style="color: #e0e0e0; font-weight: 600; font-size: 14px;">${rating.username || 'Utilisateur'}</div>
                                    <div style="color: #a0a0a0; font-size: 12px;">${rating.email || ''}</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="rating-stars" title="${rating.rating} étoile${rating.rating > 1 ? 's' : ''}">
                                    ${stars}
                                </div>
                                <div class="rating-date">${date}</div>
                            </div>
                        </div>
                    `;
                });
                ratingsContent.innerHTML = html;
            } else {
                ratingsContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #a0a0a0;">Aucun utilisateur n\'a encore noté ce jeu.</div>';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des notes:', error);
            ratingsContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff4444;">Erreur lors du chargement des notes.</div>';
        });
}

// Generate particles for admin background - RESTORED
document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.getElementById('adminParticles');
    if (particlesContainer) {
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'admin-particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }
    }
});
</script>

<?php include "views/admin/includes/footer.php"; ?>
