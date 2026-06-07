<?php 
$pageTitle = 'Gestion des Utilisateurs - Admin';
$currentPage = 'users';
include "views/admin/includes/header.php"; 
?>
<style>
    /* Styles pour les selects dans les modals (role et status) */
    #addRole,
    #addStatus,
    #editRole,
    #editStatus {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important;
        background-color: rgba(26, 10, 46, 0.95) !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(232, 121, 249, 0.3) !important;
    }
    
    #addRole option,
    #addStatus option,
    #editRole option,
    #editStatus option {
        background: rgba(26, 10, 46, 0.98) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        color: #ffffff !important;
        padding: 12px 15px !important;
        border: none !important;
    }
    
    #addRole option:checked,
    #addStatus option:checked,
    #editRole option:checked,
    #editStatus option:checked {
        background: rgba(232, 121, 249, 0.3) !important;
        background-color: rgba(232, 121, 249, 0.3) !important;
    }
    
    #addRole option:hover,
    #addStatus option:hover,
    #editRole option:hover,
    #editStatus option:hover {
        background: rgba(232, 121, 249, 0.2) !important;
        background-color: rgba(232, 121, 249, 0.2) !important;
    }
    
    #addRole:focus,
    #addStatus:focus,
    #editRole:focus,
    #editStatus:focus {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        border-color: rgba(232, 121, 249, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(232, 121, 249, 0.1) !important;
    }
    
    /* Assurer que tous les selects dans les modals ont le bon style */
    .modal select.form-control {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important;
        background-color: rgba(26, 10, 46, 0.95) !important;
        color: #ffffff !important;
    }
    
    .modal select.form-control option {
        background: rgba(26, 10, 46, 0.98) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        color: #ffffff !important;
    }
    
    /* Styles pour le select de médaille */
    #filterMedal {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23ffd700\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(255, 215, 0, 0.3) !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
    }
    
    #filterMedal option {
        background: rgba(26, 10, 46, 0.98) !important;
        background-color: rgba(26, 10, 46, 0.98) !important;
        color: #ffffff !important;
        padding: 12px 15px !important;
        border: none !important;
    }
    
    #filterMedal option:checked {
        background: rgba(255, 215, 0, 0.3) !important;
        background-color: rgba(255, 215, 0, 0.3) !important;
    }
    
    #filterMedal option:hover,
    #filterMedal option:focus {
        background: rgba(255, 215, 0, 0.2) !important;
        background-color: rgba(255, 215, 0, 0.2) !important;
    }
    
    #filterMedal:focus {
        background: linear-gradient(135deg, rgba(26, 10, 46, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%) !important;
        background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%23ffd700\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z\'/%3E%3C/svg%3E') !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        border-color: rgba(255, 215, 0, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1) !important;
    }
</style>

<!-- Admin Content -->
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
            <h2>Gestion des Utilisateurs</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Administration complète des membres de la plateforme</p>
        </div>

<?php if(isset($message)): ?>
    <div class="alert <?php echo isset($success) && $success ? 'alert-success' : 'alert-error'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php
function stringToColor($string) {
    if (empty($string)) return '#cccccc';
    $hash = md5($string);
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= $hash[$i];
    }
    return $color;
}
?>

<!-- Statistiques utilisateurs -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 25px;">
    <div class="stat-card" style="padding: 15px 12px;">
        <div class="stat-icon" style="width: 50px; height: 50px; font-size: 22px; margin-bottom: 10px;">👥</div>
        <div class="stat-number" style="font-size: 28px; margin-bottom: 5px;"><?php echo $userStats['total_users'] ?? '42'; ?></div>
        <div class="stat-label" style="font-size: 11px; margin-bottom: 5px;">Total Utilisateurs</div>
        <p class="stat-description" style="font-size: 10px;">Membres inscrits</p>
    </div>
    
    <div class="stat-card" style="padding: 15px 12px;">
        <div class="stat-icon" style="width: 50px; height: 50px; font-size: 22px; margin-bottom: 10px;">✅</div>
        <div class="stat-number" style="font-size: 28px; margin-bottom: 5px;"><?php echo $userStats['active_users'] ?? '38'; ?></div>
        <div class="stat-label" style="font-size: 11px; margin-bottom: 5px;">Utilisateurs Actifs</div>
        <p class="stat-description" style="font-size: 10px;">Statut actif</p>
    </div>
    
    <div class="stat-card" style="padding: 15px 12px;">
        <div class="stat-icon" style="width: 50px; height: 50px; font-size: 22px; margin-bottom: 10px;">🆕</div>
        <div class="stat-number" style="font-size: 28px; margin-bottom: 5px;"><?php echo $userStats['new_users'] ?? '12'; ?></div>
        <div class="stat-label" style="font-size: 11px; margin-bottom: 5px;">Nouveaux (7j)</div>
        <p class="stat-description" style="font-size: 10px;">Cette semaine</p>
    </div>
    
    <div class="stat-card" style="padding: 15px 12px;">
        <div class="stat-icon" style="width: 50px; height: 50px; font-size: 22px; margin-bottom: 10px;">📊</div>
        <div class="stat-number" style="font-size: 28px; margin-bottom: 5px;"><?php echo $userStats['completion_rate'] ?? '85'; ?>%</div>
        <div class="stat-label" style="font-size: 11px; margin-bottom: 5px;">Profils Complétés</div>
        <p class="stat-description" style="font-size: 10px;">Taux de complétion</p>
    </div>
</div>

<!-- Actions principales -->
<div class="admin-card" style="padding: 20px 15px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; font-size: 18px;">📋 Liste des Utilisateurs</h3>
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-container" style="width: 280px;">
                <input type="text" id="searchUsers" placeholder="Rechercher un utilisateur..." class="form-control">
                <i class="fas fa-search"></i>
            </div>
            <div class="search-input-container" style="width: 200px; position: relative;">
                <select id="filterMedal" class="form-control" style="padding: 12px 40px 12px 15px; background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 215, 0, 0.3) !important; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                    <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Toutes les médailles</option>
                    <option value="bronze" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥉 Bronze</option>
                    <option value="silver" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥈 Argent</option>
                    <option value="gold" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥇 Or</option>
                    <option value="none" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">— Aucune</option>
                </select>
                <i class="fas fa-medal" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255, 215, 0, 0.6); pointer-events: none; z-index: 1;"></i>
            </div>
            <a href="index.php?action=admin_export_users_pdf" class="btn-admin btn-edit" target="_blank">
                <i class="fas fa-file-pdf"></i> Liste
            </a>
            <button class="btn-admin btn-add" id="btnAddUser" data-modal="addUserModal">
                <i class="fas fa-user-plus"></i> Nouvel Utilisateur
            </button>
        </div>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Avatar</th>
                    <th style="min-width: 150px;">Utilisateur</th>
                    <th style="min-width: 180px;">Email</th>
                    <th style="width: 90px;">Rôle</th>
                    <th style="width: 90px;">Statut</th>

                    <th style="width: 100px;">Profil</th>
                    <th style="width: 110px;">Inscription</th>
                    <th style="width: 280px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($users) && !empty($users)): ?>
                    <?php foreach($users as $user): ?>
                        <tr data-user-id="<?php echo $user['id']; ?>" data-medal="<?php echo htmlspecialchars($user['medal'] ?? 'none'); ?>">
                            <td>
                                <?php
                                // Check if user has avatar in users table
                                $avatarRaw = $user['avatar'] ?? '';
                                $avatarUrl = null;
                                
                                if (!empty($avatarRaw)) {
                                    // Nettoyer le chemin de l'avatar
                                    $avatarPath = trim($avatarRaw);
                                    $avatarPath = ltrim($avatarPath, '/');
                                    $avatarPath = str_replace('projet01/', '', $avatarPath);
                                    
                                    // Construire l'URL finale
                                    if (strpos($avatarPath, 'public/') === 0) {
                                        $avatarUrl = $avatarPath;
                                    } else {
                                        $avatarUrl = 'public/' . ltrim($avatarPath, '/');
                                    }
                                }
                                
                                if (!empty($avatarUrl)): 
                                ?>
                                    <img src="<?php echo htmlspecialchars($avatarUrl); ?>" 
                                         alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                         class="user-avatar-small"
                                         style="cursor: pointer; width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(0, 255, 204, 0.3);"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         onclick="viewUserProfile(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>', '<?php echo $user['status']; ?>', '<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>', <?php echo !empty($user['profile']) ? 'true' : 'false'; ?>)">
                                    <div class="user-avatar-small"
                                         style="display: none; cursor: pointer; width: 35px; height: 35px; border-radius: 50%; background-color: <?php echo stringToColor($user['username']); ?>; color: #fff; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; text-transform: uppercase; border: 2px solid rgba(0, 255, 204, 0.3);"
                                         onclick="viewUserProfile(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>', '<?php echo $user['status']; ?>', '<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>', <?php echo !empty($user['profile']) ? 'true' : 'false'; ?>)">
                                        <?php echo substr($user['username'], 0, 1); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="user-avatar-small"
                                         style="cursor: pointer; width: 35px; height: 35px; border-radius: 50%; background-color: <?php echo stringToColor($user['username']); ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; text-transform: uppercase; border: 2px solid rgba(0, 255, 204, 0.3);"
                                         onclick="viewUserProfile(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>', '<?php echo $user['status']; ?>', '<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>', <?php echo !empty($user['profile']) ? 'true' : 'false'; ?>)">
                                        <?php echo substr($user['username'], 0, 1); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <strong style="cursor: pointer; margin-bottom: 4px; font-size: 13px; color: #ffffff; font-weight: 700;" 
                                            onclick="viewUserProfile(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>', '<?php echo $user['status']; ?>', '<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>', <?php echo !empty($user['profile']) ? 'true' : 'false'; ?>)">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </strong>
                                    <?php if(!empty($user['profile']['first_name'])): ?>
                                        <small style="color: #b0b0b0; font-size: 11px;">
                                            <?php echo htmlspecialchars($user['profile']['first_name'] . ' ' . $user['profile']['last_name']); ?>
                                        </small>
                                    <?php else: ?>
                                        <small style="color: #888888; font-size: 11px;">Nom non renseigné</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span style="color: #d0d0d0; font-size: 12px; word-break: break-all;">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge 
                                    <?php echo $user['role'] === 'admin' ? 'status-active' : 
                                          ($user['role'] === 'moderator' ? 'status-pending' : 'status-inactive'); ?>"
                                    style="font-size: 10px; padding: 4px 8px;">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge 
                                    <?php echo $user['status'] === 'active' ? 'status-active' : 
                                          ($user['status'] === 'pending' ? 'status-pending' : 'status-banned'); ?>"
                                    style="font-size: 10px; padding: 4px 8px;">
                                    <?php echo $user['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $medal = $user['medal'] ?? 'none';
                                $medalIcons = [
                                    'bronze' => '🥉',
                                    'silver' => '🥈',
                                    'gold' => '🥇',
                                    'none' => '—'
                                ];
                                $medalNames = [
                                    'bronze' => 'Bronze',
                                    'silver' => 'Argent',
                                    'gold' => 'Or',
                                    'none' => 'Aucune'
                                ];
                                $medalColors = [
                                    'bronze' => '#cd7f32',
                                    'silver' => '#c0c0c0',
                                    'gold' => '#ffd700',
                                    'none' => '#888'
                                ];
                                ?>
                                <span style="font-size: 18px; color: <?php echo $medalColors[$medal]; ?>;" 
                                      title="<?php echo $medalNames[$medal]; ?>">
                                    <?php echo $medalIcons[$medal]; ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($user['profile']['first_name'])): ?>
                                    <span class="status-badge status-active" style="font-size: 10px; padding: 4px 8px;">Complété</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive" style="font-size: 10px; padding: 4px 8px;">Incomplet</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: #d0d0d0; font-size: 12px;">
                                    <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <button class="btn-admin btn-edit btn-view-user" 
                                            data-user-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-role="<?php echo $user['role']; ?>"
                                            data-status="<?php echo $user['status']; ?>"
                                            data-registration="<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>"
                                            data-has-profile="<?php echo !empty($user['profile']) ? 'true' : 'false'; ?>"
                                            style="min-width: 40px; height: 40px; padding: 0;"
                                            title="Voir le profil">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-admin btn-edit btn-edit-user" 
                                            data-user-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>"
                                            data-role="<?php echo $user['role']; ?>"
                                            data-status="<?php echo $user['status']; ?>"
                                            data-medal="<?php echo htmlspecialchars($user['medal'] ?? 'none', ENT_QUOTES); ?>"
                                            style="min-width: 40px; height: 40px; padding: 0; cursor: pointer;"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if($user['status'] === 'banned'): ?>
                                        <a href="?action=admin_users&unban_user=1&id=<?php echo $user['id']; ?>" 
                                           class="btn-admin btn-edit"
                                           onclick="return confirm('Débannir cet utilisateur ?')"
                                           style="white-space: nowrap; padding: 8px 14px;">
                                            <i class="fas fa-unlock"></i> Débannir
                                        </a>
                                        <?php if (!empty($user['banned_until'])): ?>
                                            <small style="display: block; color: #ffaa00; font-size: 10px; margin-top: 5px;">
                                                Jusqu'au <?php echo date('d/m/Y H:i', strtotime($user['banned_until'])); ?>
                                            </small>
                                        <?php else: ?>
                                            <small style="display: block; color: #ff3333; font-size: 10px; margin-top: 5px;">
                                                Permanent
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button type="button" 
                                                class="btn-admin btn-delete"
                                                onclick="showBanModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                style="white-space: nowrap; padding: 8px 14px;">
                                            <i class="fas fa-ban"></i> Bannir
                                        </button>
                                    <?php endif; ?>
                                    <a href="?action=admin_users&delete_user=1&id=<?php echo $user['id']; ?>" 
                                       class="btn-admin btn-delete"
                                       onclick="return confirmDeleteUser()"
                                       style="white-space: nowrap; padding: 8px 14px;">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px; color: var(--text-dim);">
                            <div style="font-size: 64px; margin-bottom: 20px;">👥</div>
                            <h3 style="color: var(--text-secondary); margin-bottom: 15px;">Aucun utilisateur trouvé</h3>
                            <p style="margin-bottom: 25px;">Commencez par ajouter votre premier utilisateur à la plateforme.</p>
                            <button class="btn-admin btn-add" id="btnAddUserEmpty" data-modal="addUserModal">
                                <i class="fas fa-user-plus"></i> Ajouter le premier utilisateur
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination (exemple) -->
    <?php if(isset($users) && !empty($users) && count($users) > 10): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--metal-dark);">
        <div style="color: var(--text-secondary); font-size: 14px;">
            Affichage de 1 à 10 sur <?php echo count($users); ?> utilisateurs
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-admin btn-edit" style="padding: 8px 15px;">← Précédent</button>
            <button class="btn-admin btn-add" style="padding: 8px 15px;">1</button>
            <button class="btn-admin btn-edit" style="padding: 8px 15px;">2</button>
            <button class="btn-admin btn-edit" style="padding: 8px 15px;">Suivant →</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Ajout Utilisateur -->
<div id="addUserModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus" style="margin-right: 10px;"></i> Ajouter un Utilisateur</h2>
            <button class="close-btn" onclick="hideModal('addUserModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="addUserForm" method="POST" action="?action=admin_users" novalidate>
            <input type="hidden" name="add_user" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo AuthController::generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label><i class="fas fa-user" style="margin-right: 8px;"></i> Nom d'utilisateur *</label>
                <input type="text" id="addUsername" name="username" class="form-control" 
                       required placeholder="Entrez le nom d'utilisateur"
                       minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+"
                       oninput="validateAddUsername(this)">
                <div class="validation-feedback" id="addUsernameFeedback"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email *</label>
                <input type="email" id="addEmail" name="email" class="form-control" 
                       required placeholder="entrez@email.com"
                       maxlength="100" oninput="validateAddEmail(this)">
                <div class="validation-feedback" id="addEmailFeedback"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock" style="margin-right: 8px;"></i> Mot de passe *</label>
                <input type="password" id="addPassword" name="password" class="form-control" 
                       required placeholder="Minimum 8 caractères"
                       minlength="8" maxlength="128" oninput="validateAddPassword(this)">
                <div class="password-strength" id="addPasswordStrength"></div>
                <div class="validation-feedback" id="addPasswordFeedback"></div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label><i class="fas fa-user-tag" style="margin-right: 8px;"></i> Rôle *</label>
                    <select id="addRole" name="role" class="form-control" required onchange="validateAddRole(this)">
                        <option value="">Sélectionner...</option>
                        <option value="player">🎮 Joueur</option>
                        <option value="moderator">🛡️ Modérateur</option>
                        <option value="admin">👑 Administrateur</option>
                    </select>
                    <div class="validation-feedback" id="addRoleFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-info-circle" style="margin-right: 8px;"></i> Statut *</label>
                    <select id="addStatus" name="status" class="form-control" required onchange="validateAddStatus(this)">
                        <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Sélectionner...</option>
                        <option value="active" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">✅ Actif</option>
                        <option value="inactive" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏸️ Inactif</option>
                        <option value="pending" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏳ En attente</option>
                        <option value="banned" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🚫 Banni</option>
                    </select>
                    <div class="validation-feedback" id="addStatusFeedback"></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-admin btn-add" id="addUserSubmitBtn" style="flex: 1;" disabled>
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Créer l'utilisateur
                </button>
                <button type="button" class="btn-admin btn-edit" onclick="hideModal('addUserModal')">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Détails du Profil Utilisateur -->
<div id="userProfileModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-user" style="margin-right: 10px;"></i> Profil Utilisateur</h2>
            <button class="close-btn" onclick="hideModal('userProfileModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="userProfileContent">
            <!-- Le contenu sera chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- Modal Édition Utilisateur -->
<div id="editUserModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2><i class="fas fa-edit" style="margin-right: 10px;"></i> Modifier l'Utilisateur</h2>
            <button class="close-btn" onclick="hideModal('editUserModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="editUserForm" method="POST" action="?action=admin_users" novalidate>
            <input type="hidden" name="edit_user" value="1">
            <input type="hidden" id="editUserId" name="id" value="">
            <input type="hidden" name="csrf_token" value="<?php echo AuthController::generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label><i class="fas fa-user" style="margin-right: 8px;"></i> Nom d'utilisateur *</label>
                <input type="text" id="editUsername" name="username" class="form-control" 
                       required minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+"
                       oninput="validateEditUsername(this)">
                <div class="validation-feedback" id="editUsernameFeedback"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email *</label>
                <input type="email" id="editEmail" name="email" class="form-control" 
                       required maxlength="100" oninput="validateEditEmail(this)">
                <div class="validation-feedback" id="editEmailFeedback"></div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label><i class="fas fa-user-tag" style="margin-right: 8px;"></i> Rôle *</label>
                    <select id="editRole" name="role" class="form-control" required onchange="validateEditRole(this)">
                        <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Sélectionner...</option>
                        <option value="player" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🎮 Joueur</option>
                        <option value="moderator" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🛡️ Modérateur</option>
                        <option value="admin" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">👑 Administrateur</option>
                    </select>
                    <div class="validation-feedback" id="editRoleFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-info-circle" style="margin-right: 8px;"></i> Statut *</label>
                    <select id="editStatus" name="status" class="form-control" required onchange="validateEditStatus(this)">
                        <option value="" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">Sélectionner...</option>
                        <option value="active" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">✅ Actif</option>
                        <option value="inactive" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏸️ Inactif</option>
                        <option value="pending" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏳ En attente</option>
                        <option value="banned" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🚫 Banni</option>
                    </select>
                    <div class="validation-feedback" id="editStatusFeedback"></div>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <label><i class="fas fa-medal" style="margin-right: 8px;"></i> Médaille</label>
                <select id="editMedal" name="medal" class="form-control">
                    <option value="none" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">❌ Aucune</option>
                    <option value="bronze" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥉 Bronze</option>
                    <option value="silver" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥈 Silver</option>
                    <option value="gold" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🥇 Gold</option>
                </select>
                <small style="color: #a0a0a0; font-size: 11px; margin-top: 5px; display: block;">
                    Attribuez manuellement une médaille à cet utilisateur. Les médailles sont normalement attribuées automatiquement via le test QCM, mais vous pouvez les modifier ici.
                </small>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-admin btn-add" id="editUserSubmitBtn" style="flex: 1;" disabled>
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Sauvegarder
                </button>
                <button type="button" class="btn-admin btn-edit" onclick="hideModal('editUserModal')">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.validation-feedback {
    font-size: 11px;
    margin-top: 5px;
    min-height: 16px;
}

.validation-feedback.valid {
    color: var(--accent-green);
}

.validation-feedback.invalid {
    color: var(--accent-red);
}

.password-strength {
    height: 4px;
    margin-top: 5px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.password-strength.weak {
    background: var(--accent-red);
    width: 25%;
}

.password-strength.medium {
    background: #ffaa00;
    width: 50%;
}

.password-strength.strong {
    background: var(--accent-green);
    width: 75%;
}

.password-strength.very-strong {
    background: var(--accent-green);
    width: 100%;
}

.form-control:invalid {
    border-color: var(--accent-red);
}

.form-control:valid {
    border-color: var(--accent-green);
}
</style>

<script>
// États de validation pour l'ajout
const addValidationState = {
    username: false,
    email: false,
    password: false,
    role: false,
    status: false
};

// États de validation pour l'édition
const editValidationState = {
    username: false,
    email: false,
    role: false,
    status: false
};

// ==================== VALIDATION AJOUT UTILISATEUR ====================

function validateAddUsername(input) {
    const value = input.value.trim();
    const feedback = document.getElementById('addUsernameFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        addValidationState.username = false;
        updateAddSubmitButton();
        return;
    }
    
    if (value.length < 3) {
        showValidationError(input, feedback, "Minimum 3 caractères");
        addValidationState.username = false;
    } else if (value.length > 20) {
        showValidationError(input, feedback, "Maximum 20 caractères");
        addValidationState.username = false;
    } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        showValidationError(input, feedback, "Lettres, chiffres et underscores uniquement");
        addValidationState.username = false;
    } else if (/^[0-9]+$/.test(value)) {
        showValidationError(input, feedback, "Doit contenir au moins une lettre");
        addValidationState.username = false;
    } else if (!/[a-zA-Z]/.test(value)) {
        showValidationError(input, feedback, "Doit contenir au moins une lettre");
        addValidationState.username = false;
    } else {
        showValidationSuccess(input, feedback, "Nom d'utilisateur valide");
        addValidationState.username = true;
    }
    
    updateAddSubmitButton();
}

function validateAddEmail(input) {
    const value = input.value.trim();
    const feedback = document.getElementById('addEmailFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        addValidationState.email = false;
        updateAddSubmitButton();
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(value)) {
        showValidationError(input, feedback, "Format d'email invalide");
        addValidationState.email = false;
    } else if (value.length > 100) {
        showValidationError(input, feedback, "Maximum 100 caractères");
        addValidationState.email = false;
    } else {
        showValidationSuccess(input, feedback, "Email valide");
        addValidationState.email = true;
    }
    
    updateAddSubmitButton();
}

function validateAddPassword(input) {
    const value = input.value;
    const feedback = document.getElementById('addPasswordFeedback');
    const strength = document.getElementById('addPasswordStrength');
    
    resetValidation(input, feedback);
    strength.className = 'password-strength';
    
    if (!value) {
        addValidationState.password = false;
        updateAddSubmitButton();
        return;
    }
    
    let score = 0;
    const messages = [];
    
    if (value.length >= 8) score += 1;
    else messages.push("Minimum 8 caractères");
    
    if (/[A-Z]/.test(value)) score += 1;
    else messages.push("Au moins une majuscule");
    
    if (/[a-z]/.test(value)) score += 1;
    else messages.push("Au moins une minuscule");
    
    if (/[0-9]/.test(value)) score += 1;
    else messages.push("Au moins un chiffre");
    
    if (/[^A-Za-z0-9]/.test(value)) score += 1;
    else messages.push("Au moins un caractère spécial");
    
    if (score <= 2) {
        strength.classList.add('weak');
        showValidationError(input, feedback, messages.join(', '));
        addValidationState.password = false;
    } else if (score === 3) {
        strength.classList.add('medium');
        showValidationSuccess(input, feedback, "Mot de passe moyen");
        addValidationState.password = true;
    } else if (score === 4) {
        strength.classList.add('strong');
        showValidationSuccess(input, feedback, "Mot de passe fort");
        addValidationState.password = true;
    } else {
        strength.classList.add('very-strong');
        showValidationSuccess(input, feedback, "Mot de passe très fort");
        addValidationState.password = true;
    }
    
    updateAddSubmitButton();
}

function validateAddRole(input) {
    const value = input.value;
    const feedback = document.getElementById('addRoleFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        showValidationError(input, feedback, "Veuillez sélectionner un rôle");
        addValidationState.role = false;
    } else {
        showValidationSuccess(input, feedback, "Rôle valide");
        addValidationState.role = true;
    }
    
    updateAddSubmitButton();
}

function validateAddStatus(input) {
    const value = input.value;
    const feedback = document.getElementById('addStatusFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        showValidationError(input, feedback, "Veuillez sélectionner un statut");
        addValidationState.status = false;
    } else {
        showValidationSuccess(input, feedback, "Statut valide");
        addValidationState.status = true;
    }
    
    updateAddSubmitButton();
}

// ==================== VALIDATION ÉDITION UTILISATEUR ====================

function validateEditUsername(input) {
    const value = input.value.trim();
    const feedback = document.getElementById('editUsernameFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        editValidationState.username = false;
        updateEditSubmitButton();
        return;
    }
    
    if (value.length < 3) {
        showValidationError(input, feedback, "Minimum 3 caractères");
        editValidationState.username = false;
    } else if (value.length > 20) {
        showValidationError(input, feedback, "Maximum 20 caractères");
        editValidationState.username = false;
    } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        showValidationError(input, feedback, "Lettres, chiffres et underscores uniquement");
        editValidationState.username = false;
    } else if (/^[0-9]+$/.test(value)) {
        showValidationError(input, feedback, "Doit contenir au moins une lettre");
        editValidationState.username = false;
    } else if (!/[a-zA-Z]/.test(value)) {
        showValidationError(input, feedback, "Doit contenir au moins une lettre");
        editValidationState.username = false;
    } else {
        showValidationSuccess(input, feedback, "Nom d'utilisateur valide");
        editValidationState.username = true;
    }
    
    updateEditSubmitButton();
}

function validateEditEmail(input) {
    const value = input.value.trim();
    const feedback = document.getElementById('editEmailFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        editValidationState.email = false;
        updateEditSubmitButton();
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(value)) {
        showValidationError(input, feedback, "Format d'email invalide");
        editValidationState.email = false;
    } else if (value.length > 100) {
        showValidationError(input, feedback, "Maximum 100 caractères");
        editValidationState.email = false;
    } else {
        showValidationSuccess(input, feedback, "Email valide");
        editValidationState.email = true;
    }
    
    updateEditSubmitButton();
}

function validateEditRole(input) {
    const value = input.value;
    const feedback = document.getElementById('editRoleFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        showValidationError(input, feedback, "Veuillez sélectionner un rôle");
        editValidationState.role = false;
    } else {
        showValidationSuccess(input, feedback, "Rôle valide");
        editValidationState.role = true;
    }
    
    updateEditSubmitButton();
}

function validateEditStatus(input) {
    const value = input.value;
    const feedback = document.getElementById('editStatusFeedback');
    
    resetValidation(input, feedback);
    
    if (!value) {
        showValidationError(input, feedback, "Veuillez sélectionner un statut");
        editValidationState.status = false;
    } else {
        showValidationSuccess(input, feedback, "Statut valide");
        editValidationState.status = true;
    }
    
    updateEditSubmitButton();
}

// ==================== FONCTIONS UTILITAIRES ====================

function resetValidation(input, feedback) {
    input.classList.remove('valid', 'invalid');
    feedback.textContent = '';
    feedback.className = 'validation-feedback';
}

function showValidationError(input, feedback, message) {
    input.classList.add('invalid');
    feedback.textContent = message;
    feedback.classList.add('invalid');
}

function showValidationSuccess(input, feedback, message) {
    input.classList.add('valid');
    feedback.textContent = message;
    feedback.classList.add('valid');
}

function updateAddSubmitButton() {
    const submitBtn = document.getElementById('addUserSubmitBtn');
    const isValid = Object.values(addValidationState).every(state => state);
    submitBtn.disabled = !isValid;
}

function updateEditSubmitButton() {
    const submitBtn = document.getElementById('editUserSubmitBtn');
    const isValid = Object.values(editValidationState).every(state => state);
    submitBtn.disabled = !isValid;
}

function confirmDeleteUser() {
    return confirm('⚠️ Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?\n\nCette action est irréversible et supprimera toutes les données associées.');
}

// ==================== GESTION DES MODALES ====================

// User data from PHP - Real database data
const userDataFromPHP = <?php echo json_encode($users ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;

// Simple modal functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important;';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        if (modalId === 'addUserModal' && typeof resetAddForm === 'function') {
            resetAddForm();
        }
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.cssText = 'display: none !important;';
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Make functions globally available
window.showModal = showModal;
window.hideModal = hideModal;

function resetAddForm() {
    // Réinitialiser l'état de validation
    Object.keys(addValidationState).forEach(key => {
        addValidationState[key] = false;
    });
    
    // Réinitialiser les champs
    document.getElementById('addUserForm').reset();
    
    // Réinitialiser les feedbacks
    const feedbacks = document.querySelectorAll('#addUserForm .validation-feedback');
    feedbacks.forEach(feedback => {
        feedback.textContent = '';
        feedback.className = 'validation-feedback';
    });
    
    // Réinitialiser les inputs
    const inputs = document.querySelectorAll('#addUserForm .form-control');
    inputs.forEach(input => {
        input.classList.remove('valid', 'invalid');
    });
    
    // Réinitialiser la force du mot de passe
    const strength = document.getElementById('addPasswordStrength');
    if (strength) {
        strength.className = 'password-strength';
    }
    
    updateAddSubmitButton();
}

function editUser(userId, username, email, role, status, medal = 'none') {
    console.log('editUser called with:', { userId, username, email, role, status, medal });
    
    // Vérifier que tous les éléments existent avant de les utiliser
    const editUserIdEl = document.getElementById('editUserId');
    const editUsernameEl = document.getElementById('editUsername');
    const editEmailEl = document.getElementById('editEmail');
    const editRoleEl = document.getElementById('editRole');
    const editStatusEl = document.getElementById('editStatus');
    const editMedalEl = document.getElementById('editMedal');
    
    if (!editUserIdEl) {
        console.error('editUserId element not found');
        alert('Erreur: Le formulaire d\'édition n\'est pas chargé correctement. Veuillez recharger la page.');
        return;
    }
    
    if (!editUsernameEl) {
        console.error('editUsername element not found');
        alert('Erreur: Le champ nom d\'utilisateur n\'est pas trouvé. Veuillez recharger la page.');
        return;
    }
    
    if (!editEmailEl) {
        console.error('editEmail element not found');
        alert('Erreur: Le champ email n\'est pas trouvé. Veuillez recharger la page.');
        return;
    }
    
    if (!editRoleEl) {
        console.error('editRole element not found');
        alert('Erreur: Le champ rôle n\'est pas trouvé. Veuillez recharger la page.');
        return;
    }
    
    if (!editStatusEl) {
        console.error('editStatus element not found');
        alert('Erreur: Le champ statut n\'est pas trouvé. Veuillez recharger la page.');
        return;
    }
    
    // Remplir le formulaire avec les vraies données
    editUserIdEl.value = userId;
    editUsernameEl.value = username;
    editEmailEl.value = email;
    editRoleEl.value = role;
    editStatusEl.value = status;
    
    // Médaille est optionnelle (peut ne pas exister si la colonne n'a pas été créée)
    if (editMedalEl) {
        editMedalEl.value = medal || 'none';
    } else {
        console.warn('editMedal element not found - medal field may not be available');
    }
    
    // Fermer le modal de profil si ouvert
    if (typeof hideModal === 'function') {
        hideModal('userProfileModal');
    }
    
    // Réinitialiser l'état de validation
    if (typeof editValidationState !== 'undefined') {
        editValidationState.username = true;
        editValidationState.email = true;
        editValidationState.role = true;
        editValidationState.status = true;
    }
    
    // Valider les champs pour mettre à jour l'état visuel (vert/rouge)
    if (typeof validateEditUsername === 'function') {
        validateEditUsername(editUsernameEl);
    }
    if (typeof validateEditEmail === 'function') {
        validateEditEmail(editEmailEl);
    }
    if (typeof validateEditRole === 'function') {
        validateEditRole(editRoleEl);
    }
    if (typeof validateEditStatus === 'function') {
        validateEditStatus(editStatusEl);
    }
    
    // Activer le bouton de soumission
    if (typeof updateEditSubmitButton === 'function') {
        updateEditSubmitButton();
    }
    
    // Afficher le modal
    if (typeof showModal === 'function') {
        showModal('editUserModal');
    } else {
        const modal = document.getElementById('editUserModal');
        if (modal) {
            modal.style.display = 'flex';
        } else {
            alert('Erreur: Le modal d\'édition n\'est pas trouvé. Veuillez recharger la page.');
            return;
        }
    }
    
    console.log('editUser called successfully for user:', userId);
}

// ==================== ÉVÉNEMENTS ====================

document.addEventListener('DOMContentLoaded', function() {
    // Fonction de filtrage combiné (recherche + médaille)
    function filterUsers() {
        const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
        const selectedMedal = document.getElementById('filterMedal').value;
        const rows = document.querySelectorAll('.admin-table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowMedal = row.getAttribute('data-medal') || 'none';
            
            // Vérifier si la ligne correspond aux critères
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const matchesMedal = selectedMedal === '' || rowMedal === selectedMedal;
            
            // Afficher ou masquer la ligne
            row.style.display = (matchesSearch && matchesMedal) ? '' : 'none';
        });
        
        // Afficher un message si aucun résultat
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        const noResultsMessage = document.getElementById('noResultsMessage');
        if (visibleRows.length === 0 && (searchTerm || selectedMedal)) {
            if (!noResultsMessage) {
                const tbody = document.querySelector('.admin-table tbody');
                const messageRow = document.createElement('tr');
                messageRow.id = 'noResultsMessage';
                messageRow.innerHTML = '<td colspan="9" style="text-align: center; padding: 40px; color: #a0a0a0;"><div style="font-size: 48px; margin-bottom: 15px;">🔍</div><h3 style="color: #ffaa00; margin-bottom: 10px;">Aucun utilisateur trouvé</h3><p>Aucun utilisateur ne correspond à vos critères de recherche.</p></td>';
                tbody.appendChild(messageRow);
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
    
    // Recherche en temps réel
    const searchInput = document.getElementById('searchUsers');
    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }
    
    // Filtre par médaille
    const filterMedal = document.getElementById('filterMedal');
    if (filterMedal) {
        filterMedal.addEventListener('change', filterUsers);
    }
    
    // Validation des formulaires avant soumission
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        const isValid = Object.values(addValidationState).every(state => state);
        if (!isValid) {
            e.preventDefault();
            alert('❌ Veuillez corriger les erreurs avant de créer l\'utilisateur.');
        }
    });
    
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const isValid = Object.values(editValidationState).every(state => state);
        if (!isValid) {
            e.preventDefault();
            alert('❌ Veuillez corriger les erreurs avant de sauvegarder.');
        }
    });
    
    // Fermer les modales avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideModal('addUserModal');
            hideModal('editUserModal');
            hideModal('userProfileModal');
        }
    });
    
    // Fermer les modales en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            hideModal(event.target.id);
        }
    });
});

function viewUserProfile(userId, username, email, role, status, registration, hasProfile) {
    console.log('Viewing user profile:', userId); // Debug
    // Find user in the real data from PHP
    const userData = userDataFromPHP.find(u => u.id == userId);
    
    // Extract profile data or use defaults
    const profile = userData?.profile || {};
    const userProfile = {
        firstName: profile.first_name || "Non renseigné",
        lastName: profile.last_name || "Non renseigné",
        discord: profile.discord || "Non renseigné",
        country: profile.country || "Non renseigné", 
        nationality: profile.nationality || "Non renseigné",
        gender: profile.gender || "Non renseigné",
        birthDate: profile.birth_date ? new Date(profile.birth_date).toLocaleDateString('fr-FR') : "Non renseigné",
        careerLevel: profile.career_level || "Non renseigné",
        expertise: profile.expertise || "Non renseigné",
        techStack: profile.tech_stack || "Non renseigné",
        timezone: profile.timezone || "Europe/Paris",
        bio: profile.bio || profile.description || "Aucune biographie renseignée.",
        avatar: userData?.avatar || '',
        canAddGames: userData?.can_add_games || 0,
        lastLogin: userData?.last_login || null
    };
    
    // Build avatar HTML using user data directly (only one avatar)
    let avatarHtml = '';
    const avatarUrl = userProfile.avatar || userData?.avatar || '';
    
    if(avatarUrl && avatarUrl.trim() !== '') {
        // User has an avatar image - use it with fallback
        const hash = username.split('').reduce((acc, char) => char.charCodeAt(0) + ((acc << 5) - acc), 0);
        const bgColor = '#' + Math.floor(Math.abs(Math.sin(hash) * 16777215)).toString(16).padStart(6, '0');
        const fallbackLetter = username.charAt(0).toUpperCase();
        
        // Clean avatar URL
        let cleanAvatarUrl = avatarUrl.trim();
        cleanAvatarUrl = cleanAvatarUrl.replace(/^\/+/, '');
        if(cleanAvatarUrl.indexOf('public/') !== 0) {
            cleanAvatarUrl = 'public/' + cleanAvatarUrl;
        }
        
        avatarHtml = `
            <div style="position: relative; display: inline-block;">
                <img src="${cleanAvatarUrl}" 
                     alt="${username}" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); display: block;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none; width: 120px; height: 120px; border-radius: 50%; background-color: ${bgColor}; color: #fff; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); position: absolute; top: 0; left: 0;">
                    ${fallbackLetter}
                </div>
            </div>
        `;
    } else {
        // No avatar - use colored circle with initial
        const hash = username.split('').reduce((acc, char) => char.charCodeAt(0) + ((acc << 5) - acc), 0);
        const bgColor = '#' + Math.floor(Math.abs(Math.sin(hash) * 16777215)).toString(16).padStart(6, '0');
        const letter = username.charAt(0).toUpperCase();
        avatarHtml = `<div style="width: 120px; height: 120px; border-radius: 50%; background-color: ${bgColor}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); margin: 0 auto;">${letter}</div>`;
    }

    const content = `
        <div style="text-align: center; margin-bottom: 30px;">
            ${avatarHtml}
            <h3 style="color: var(--accent-cyan); margin: 20px 0 10px 0;">
                ${userProfile.firstName !== 'Non renseigné' && userProfile.lastName !== 'Non renseigné' 
                    ? userProfile.firstName + ' ' + userProfile.lastName 
                    : username}
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 5px;">@${username}</p>
            <p style="color: var(--text-dim); font-size: 14px;">${email}</p>
            ${userData?.medal && userData.medal !== 'none' ? `
                <div style="margin-top: 10px;">
                    <span style="font-size: 24px;">${userData.medal === 'bronze' ? '🥉' : userData.medal === 'silver' ? '🥈' : '🥇'}</span>
                    <span style="color: ${userData.medal === 'bronze' ? '#cd7f32' : userData.medal === 'silver' ? '#c0c0c0' : '#ffd700'}; font-weight: 600; margin-left: 5px; font-size: 16px;">
                        ${userData.medal === 'bronze' ? 'Bronze' : userData.medal === 'silver' ? 'Argent' : 'Or'}
                    </span>
                </div>
            ` : ''}
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <!-- Informations du compte -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations du Compte
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Rôle</strong>
                        <span class="status-badge ${role === 'admin' ? 'status-active' : role === 'moderator' ? 'status-pending' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                            ${role === 'admin' ? '👑 Administrateur' : role === 'moderator' ? '🛡️ Modérateur' : '🎮 Joueur'}
                        </span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Statut</strong>
                        <span class="status-badge ${status === 'active' ? 'status-active' : status === 'pending' ? 'status-pending' : status === 'banned' ? 'status-banned' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                            ${status === 'active' ? '✅ Actif' : status === 'pending' ? '⏳ En attente' : status === 'banned' ? '🚫 Banni' : '⏸️ Inactif'}
                        </span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date d'inscription</strong>
                        <span style="color: var(--text-secondary);">${registration}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Fuseau horaire</strong>
                        <span style="color: var(--text-secondary);">${userProfile.timezone}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Peut ajouter des jeux</strong>
                        <span class="status-badge ${userProfile.canAddGames ? 'status-active' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px;">
                            ${userProfile.canAddGames ? 'Oui' : 'Non'}
                        </span>
                    </div>
                    ${userProfile.lastLogin ? `
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Dernière connexion</strong>
                        <span style="color: var(--text-secondary);">${new Date(userProfile.lastLogin).toLocaleString('fr-FR')}</span>
                    </div>
                    ` : ''}
                </div>
            </div>

            <!-- Informations personnelles -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user"></i> Informations Personnelles
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Pays</strong>
                        <span style="color: var(--text-secondary);">${userProfile.country}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Nationalité</strong>
                        <span style="color: var(--text-secondary);">${userProfile.nationality}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Genre</strong>
                        <span style="color: var(--text-secondary);">${userProfile.gender}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date de naissance</strong>
                        <span style="color: var(--text-secondary);">${userProfile.birthDate}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations professionnelles -->
        <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px; margin-bottom: 25px;">
            <h4 style="color: var(--accent-purple); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-briefcase"></i> Informations Professionnelles
            </h4>
            <div style="display: grid; gap: 15px;">
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Niveau de carrière</strong>
                    <span style="color: var(--text-secondary);">${userProfile.careerLevel}</span>
                </div>
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Expertise</strong>
                    <span style="color: var(--text-secondary);">${userProfile.expertise}</span>
                </div>
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Stack technique</strong>
                    <span style="color: var(--text-secondary);">${userProfile.techStack}</span>
                </div>
            </div>
        </div>

        <!-- Contact et Bio -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-cyan); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-phone"></i> Contact
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Discord</strong>
                        <span style="color: var(--text-secondary);">${userProfile.discord}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Email</strong>
                        <span style="color: var(--text-secondary);">${email}</span>
                    </div>
                </div>
            </div>

            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-file-alt"></i> Bio
                </h4>
                <p style="color: var(--text-secondary); line-height: 1.5; margin: 0;">
                    ${userProfile.bio}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 15px; justify-content: center; border-top: 1px solid var(--metal-dark); padding-top: 25px;">
            <button class="btn-admin btn-edit" onclick="editUser(${userId}, '${username.replace(/'/g, "\\'")}', '${email.replace(/'/g, "\\'")}', '${role}', '${status}', '${userData?.medal || 'none'}'); hideModal('userProfileModal');">
                <i class="fas fa-edit" style="margin-right: 8px;"></i> Modifier
            </button>
            <button class="btn-admin btn-edit" onclick="hideModal('userProfileModal')">
                <i class="fas fa-times" style="margin-right: 8px;"></i> Fermer
            </button>
        </div>
    `;

    const profileContent = document.getElementById('userProfileContent');
    if (profileContent) {
        profileContent.innerHTML = content;
    showModal('userProfileModal');
    } else {
        console.error('userProfileContent element not found');
    }
}

// Make functions globally available
window.viewUserProfile = viewUserProfile;
window.editUser = editUser;

// La recherche est maintenant gérée dans le DOMContentLoaded ci-dessus

// Initialiser les animations et les événements
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Setting up event listeners');
    
    // Wait a bit to ensure all scripts are loaded
    setTimeout(function() {
        // Event listeners for Add User buttons
        document.querySelectorAll('[data-modal="addUserModal"], #btnAddUser, #btnAddUserEmpty').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add user button clicked');
                if (typeof showModal === 'function') {
                    showModal('addUserModal');
                } else if (typeof window.showModal === 'function') {
                    window.showModal('addUserModal');
                } else {
                    console.error('showModal function not found');
                }
            });
        });
        
        // Event listeners for View User buttons
        document.querySelectorAll('.btn-view-user').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const userId = this.getAttribute('data-user-id');
                const username = this.getAttribute('data-username');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const status = this.getAttribute('data-status');
                const registration = this.getAttribute('data-registration');
                const hasProfile = this.getAttribute('data-has-profile') === 'true';
                
                console.log('View user button clicked for user:', userId);
                if (typeof viewUserProfile === 'function') {
                    viewUserProfile(userId, username, email, role, status, registration, hasProfile);
                } else if (typeof window.viewUserProfile === 'function') {
                    window.viewUserProfile(userId, username, email, role, status, registration, hasProfile);
                } else {
                    console.error('viewUserProfile function not found');
                }
            });
        });
        
        // Event listeners for Edit User buttons
        const editUserButtons = document.querySelectorAll('.btn-edit-user');
        console.log('Found', editUserButtons.length, 'edit user buttons');
        
        editUserButtons.forEach((btn, index) => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const userId = this.getAttribute('data-user-id');
                const username = this.getAttribute('data-username');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const status = this.getAttribute('data-status');
                const medal = this.getAttribute('data-medal') || 'none';
                
                console.log('Edit user button clicked for user:', userId, 'medal:', medal);
                console.log('editUser function available:', typeof editUser !== 'undefined', 'window.editUser:', typeof window.editUser !== 'undefined');
                
                // Try to call editUser function
                if (typeof editUser === 'function') {
                    try {
                        editUser(userId, username, email, role, status, medal);
                    } catch (error) {
                        console.error('Error calling editUser:', error);
                        alert('Erreur lors de l\'ouverture du formulaire d\'édition: ' + error.message);
                    }
                } else if (typeof window.editUser === 'function') {
                    try {
                        window.editUser(userId, username, email, role, status, medal);
                    } catch (error) {
                        console.error('Error calling window.editUser:', error);
                        alert('Erreur lors de l\'ouverture du formulaire d\'édition: ' + error.message);
                    }
                } else {
                    console.error('editUser function not found');
                    alert('Erreur: La fonction editUser n\'est pas disponible. Veuillez recharger la page.');
                }
            });
            
            // Ensure button is clickable
            btn.style.pointerEvents = 'auto';
            btn.style.cursor = 'pointer';
            btn.style.position = 'relative';
            btn.style.zIndex = '10';
        });
        
        // Close modal buttons
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const modal = this.closest('.modal');
                if (modal) {
                    const modalId = modal.id;
                    if (typeof hideModal === 'function') {
                        hideModal(modalId);
                    } else if (typeof window.hideModal === 'function') {
                        window.hideModal(modalId);
                    }
                }
            });
        });
    }, 100);
    
    // Animation des cartes
    setTimeout(() => {
        const cards = document.querySelectorAll('.admin-card, .stat-card');
        cards.forEach((card, index) => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    }, 300);
    
    // Generate particles for admin background - RESTORED
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
    </div>
</section>
<!-- Modal de Bannissement -->
<div class="modal" id="banUserModal" style="display: none;">
    <div class="modal-content" style="max-width: 500px; background: linear-gradient(135deg, rgba(15, 15, 18, 0.98) 0%, rgba(18, 18, 22, 0.98) 100%); border: 1px solid rgba(255, 51, 51, 0.3); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);">
        <button class="close-btn" onclick="hideBanModal()" style="position: absolute; top: 20px; right: 20px; z-index: 10; background: rgba(255, 255, 255, 0.1); border: none; color: #fff; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.2)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)';" title="Fermer">×</button>
        
        <div style="padding: 40px 30px 30px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 48px; margin-bottom: 15px;">🚫</div>
                <h2 style="color: #ff3333; font-size: 24px; font-weight: 700; margin: 0 0 10px 0;">Bannir un Utilisateur</h2>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 14px; margin: 0;">
                    Utilisateur: <strong id="banUserName" style="color: #ffffff;"></strong>
                </p>
            </div>
            
            <form id="banUserForm" method="POST" action="?action=admin_users">
                <input type="hidden" name="ban_user_modal" value="1">
                <input type="hidden" name="user_id" id="banUserId">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="color: #ffffff; font-weight: 600; margin-bottom: 10px; display: block;">
                        <i class="fas fa-clock" style="margin-right: 8px; color: #ffaa00;"></i>
                        Durée du bannissement
                    </label>
                    <select name="ban_duration" id="banDuration" class="form-control" required style="background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 51, 51, 0.3) !important; padding: 12px 15px; border-radius: 12px; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                        <option value="permanent" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">🔴 Permanent</option>
                        <option value="1" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏰ 1 jour</option>
                        <option value="7" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏰ 7 jours</option>
                        <option value="30" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏰ 30 jours</option>
                        <option value="90" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">⏰ 90 jours</option>
                        <option value="custom" style="background: rgba(26, 10, 46, 0.98); color: #ffffff;">📅 Date personnalisée</option>
                    </select>
                    <small style="color: rgba(255, 255, 255, 0.6); font-size: 11px; margin-top: 5px; display: block;">
                        Sélectionnez la durée du bannissement. L'utilisateur ne pourra pas se connecter jusqu'à l'expiration.
                    </small>
                </div>
                
                <div class="form-group" id="customDateGroup" style="display: none; margin-bottom: 20px;">
                    <label style="color: #ffffff; font-weight: 600; margin-bottom: 10px; display: block;">
                        <i class="fas fa-calendar-alt" style="margin-right: 8px; color: #ffaa00;"></i>
                        Date d'expiration
                    </label>
                    <input type="datetime-local" name="ban_custom_date" id="banCustomDate" class="form-control" style="background: linear-gradient(135deg, rgba(26, 10, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 51, 51, 0.3) !important; padding: 12px 15px; border-radius: 12px;">
                    <small style="color: rgba(255, 255, 255, 0.6); font-size: 11px; margin-top: 5px; display: block;">
                        Sélectionnez la date et l'heure d'expiration du bannissement.
                    </small>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="button" onclick="hideBanModal()" class="btn-admin btn-edit" style="flex: 1; padding: 12px 20px; border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-times" style="margin-right: 8px;"></i> Annuler
                    </button>
                    <button type="submit" class="btn-admin btn-delete" style="flex: 1; padding: 12px 20px; border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-ban" style="margin-right: 8px;"></i> Bannir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showBanModal(userId, username) {
        const modal = document.getElementById('banUserModal');
        const userIdInput = document.getElementById('banUserId');
        const userNameSpan = document.getElementById('banUserName');
        
        if (modal && userIdInput && userNameSpan) {
            userIdInput.value = userId;
            userNameSpan.textContent = username;
            
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        }
    }
    
    function hideBanModal() {
        const modal = document.getElementById('banUserModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }
    }
    
    // Gérer l'affichage du champ de date personnalisée
    document.addEventListener('DOMContentLoaded', function() {
        const banDuration = document.getElementById('banDuration');
        const customDateGroup = document.getElementById('customDateGroup');
        const banCustomDate = document.getElementById('banCustomDate');
        const banUserForm = document.getElementById('banUserForm');
        
        if (banDuration && customDateGroup && banCustomDate) {
            banDuration.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateGroup.style.display = 'block';
                    banCustomDate.required = true;
                    // Définir la date minimale à maintenant
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    banCustomDate.min = now.toISOString().slice(0, 16);
                } else {
                    customDateGroup.style.display = 'none';
                    banCustomDate.required = false;
                }
            });
            
            // Gérer la soumission du formulaire
            if (banUserForm) {
                banUserForm.addEventListener('submit', function(e) {
                    const duration = banDuration.value;
                    if (duration === 'custom') {
                        const customDate = banCustomDate.value;
                        if (!customDate) {
                            e.preventDefault();
                            alert('Veuillez sélectionner une date d\'expiration.');
                            return false;
                        }
                        // Convertir la date en format pour le backend
                        const dateInput = document.createElement('input');
                        dateInput.type = 'hidden';
                        dateInput.name = 'ban_duration';
                        dateInput.value = customDate;
                        this.appendChild(dateInput);
                    }
                });
            }
        }
        
        // Fermer le modal en cliquant en dehors
        const modal = document.getElementById('banUserModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideBanModal();
                }
            });
        }
    });
</script>

<style>
    #banUserModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    #banDuration:focus {
        border-color: rgba(255, 51, 51, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 51, 51, 0.1) !important;
    }
    
    #banCustomDate:focus {
        border-color: rgba(255, 51, 51, 0.5) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 51, 51, 0.1) !important;
    }
</style>

<?php include "views/admin/includes/footer.php"; ?>