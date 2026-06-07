<?php 
$pageTitle = 'Gestion des Demandes de Test - Admin';
$currentPage = 'test_requests';
include "views/admin/includes/header.php"; 
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
    </div>
    <div class="admin-container">
        
        <div class="admin-header-section">
            <div>
                <h2>📝 Demandes de Test QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Gérer les demandes d'accès au test des utilisateurs</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="?controller=adminTest&action=listRequests&status=pending" class="btn btn-primary">⏳ En Attente</a>
                <a href="?controller=adminTest&action=listRequests&status=approved" class="btn" style="background: rgba(0, 255, 88, 0.2); color: #00ff88;">✅ Approuvées</a>
                <a href="?controller=adminTest&action=listRequests&status=rejected" class="btn" style="background: rgba(255, 51, 51, 0.2); color: #ff6b6b;">❌ Rejetées</a>
                <a href="?controller=adminTest&action=listRequests" class="btn btn-secondary">Toutes</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-card" style="padding: 20px 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h3 style="margin: 0; font-size: 18px;">📋 Liste des Demandes</h3>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="min-width: 150px;">Utilisateur</th>
                            <th style="min-width: 180px;">Email</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 120px;">Statut</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 40px; color: #a0a0a0;">
                                    <div style="font-size: 48px; margin-bottom: 15px;">📝</div>
                                    <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucune demande</h3>
                                    <p>Aucune demande de test trouvée.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $req): 
                                $statusColors = [
                                    'pending' => ['bg' => 'rgba(255, 193, 7, 0.2)', 'text' => '#ffc107', 'label' => '⏳ En Attente'],
                                    'approved' => ['bg' => 'rgba(0, 255, 88, 0.2)', 'text' => '#00ff88', 'label' => '✅ Approuvée'],
                                    'rejected' => ['bg' => 'rgba(255, 51, 51, 0.2)', 'text' => '#ff6b6b', 'label' => '❌ Rejetée']
                                ];
                                $statusInfo = $statusColors[$req['status']] ?? ['bg' => 'rgba(128, 128, 128, 0.2)', 'text' => '#808080', 'label' => '❓ Inconnu'];
                                // Handle case where user might be deleted
                                $username = !empty($req['username']) ? htmlspecialchars($req['username']) : 'Utilisateur supprimé (ID: ' . $req['user_id'] . ')';
                                $email = !empty($req['email']) ? htmlspecialchars($req['email']) : 'N/A';
                            ?>
                                <tr>
                                    <td style="color: #00ffcc; font-weight: 700; font-family: monospace;">#<?php echo $req['id']; ?></td>
                                    <td>
                                        <strong style="color: var(--accent-cyan);"><?php echo $username; ?></strong>
                                        <?php if (empty($req['username'])): ?>
                                            <span style="color: #ff6b6b; font-size: 0.85em; display: block; margin-top: 5px;">(Compte supprimé)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #a0a0a0;"><?php echo $email; ?></td>
                                    <td style="color: #707070; font-size: 12px;"><?php echo isset($req['created_at']) ? date('d/m/Y H:i', strtotime($req['created_at'])) : 'N/A'; ?></td>
                                    <td>
                                        <span class="badge" style="display: inline-block; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: <?php echo $statusInfo['bg']; ?>; border: 1px solid <?php echo str_replace('0.2', '0.5', $statusInfo['bg']); ?>; color: <?php echo $statusInfo['text']; ?>;">
                                            <?php echo $statusInfo['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <a href="?controller=adminTest&action=reviewRequest&id=<?php echo $req['id']; ?>" 
                                               class="btn-admin btn-edit" title="Voir les détails" style="min-width: 40px; height: 40px; padding: 0;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>

