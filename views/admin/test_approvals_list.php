<?php 
$pageTitle = 'Gestion des Approbations de Test - Admin';
$currentPage = 'test_approvals';
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
                <h2>✅ Approbations de Test QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Consulter toutes les approbations de test des utilisateurs</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="?controller=adminTest&action=listApprovals&status=approved" class="btn" style="background: rgba(0, 255, 88, 0.2); color: #00ff88;">✅ Approuvées</a>
                <a href="?controller=adminTest&action=listApprovals&status=rejected" class="btn" style="background: rgba(255, 51, 51, 0.2); color: #ff6b6b;">❌ Rejetées</a>
                <a href="?controller=adminTest&action=listApprovals&status=pending" class="btn" style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">⏳ En Attente</a>
                <a href="?controller=adminTest&action=listApprovals" class="btn btn-secondary">Toutes</a>
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

        <div class="admin-form-container">
            <?php if (empty($approvals)): ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <p style="color: #a0a0a0; font-size: 1.1em;">Aucune approbation trouvée.</p>
                </div>
            <?php else: ?>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Tentative ID</th>
                                <th>Score</th>
                                <th>Statut</th>
                                <th>Admin</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvals as $approval): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($approval['id']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($approval['username'])) {
                                            echo htmlspecialchars($approval['username']);
                                        } else {
                                            echo '<span style="color: #888;">Utilisateur supprimé</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="?controller=adminTest&action=reviewResult&attempt_id=<?php echo $approval['attempt_id']; ?>" style="color: #00ffcc;">
                                            #<?php echo htmlspecialchars($approval['attempt_id']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <strong style="color: <?php 
                                            $score = $approval['score'] ?? 0;
                                            if ($score >= 75) echo '#00ff88';
                                            elseif ($score >= 50) echo '#ffc107';
                                            else echo '#ff6b6b';
                                        ?>;">
                                            <?php echo number_format($score, 1); ?>%
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $approval['status'] ?? 'pending';
                                        $statusColors = [
                                            'approved' => ['color' => '#00ff88', 'bg' => 'rgba(0, 255, 88, 0.2)', 'icon' => '✅'],
                                            'rejected' => ['color' => '#ff6b6b', 'bg' => 'rgba(255, 51, 51, 0.2)', 'icon' => '❌'],
                                            'pending' => ['color' => '#ffc107', 'bg' => 'rgba(255, 193, 7, 0.2)', 'icon' => '⏳']
                                        ];
                                        $statusInfo = $statusColors[$status] ?? $statusColors['pending'];
                                        ?>
                                        <span style="padding: 5px 12px; border-radius: 20px; font-size: 0.85em; background: <?php echo $statusInfo['bg']; ?>; color: <?php echo $statusInfo['color']; ?>;">
                                            <?php echo $statusInfo['icon']; ?> <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($approval['admin_username'])) {
                                            echo htmlspecialchars($approval['admin_username']);
                                        } else {
                                            echo '<span style="color: #888;">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($approval['created_at'])) {
                                            $date = new DateTime($approval['created_at']);
                                            echo $date->format('d/m/Y H:i');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="?controller=adminTest&action=reviewResult&attempt_id=<?php echo $approval['attempt_id']; ?>" 
                                           class="btn btn-sm" 
                                           style="background: rgba(0, 255, 204, 0.2); color: #00ffcc;">
                                            👁️ Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>





