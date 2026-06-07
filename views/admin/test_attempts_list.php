<?php 
$pageTitle = 'Gestion des Tentatives de Test - Admin';
$currentPage = 'test_attempts';
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
                <h2>📊 Tentatives de Test QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Consulter toutes les tentatives de test des utilisateurs</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="?controller=adminTest&action=listAttempts&status=in_progress" class="btn" style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">⏳ En Cours</a>
                <a href="?controller=adminTest&action=listAttempts&status=completed" class="btn" style="background: rgba(0, 255, 88, 0.2); color: #00ff88;">✅ Terminées</a>
                <a href="?controller=adminTest&action=listAttempts&status=expired" class="btn" style="background: rgba(255, 51, 51, 0.2); color: #ff6b6b;">⏱️ Expirées</a>
                <a href="?controller=adminTest&action=listAttempts" class="btn btn-secondary">Toutes</a>
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
                <h3 style="margin: 0; font-size: 18px;">📋 Liste des Tentatives</h3>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="min-width: 150px;">Utilisateur</th>
                            <th style="min-width: 180px;">Email</th>
                            <th style="width: 100px;">Score</th>
                            <th style="width: 120px;">Questions</th>
                            <th style="width: 100px;">Temps</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 120px;">Statut</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attempts)): ?>
                            <tr>
                                <td colspan="9" style="text-align:center; padding: 40px; color: #a0a0a0;">
                                    <div style="font-size: 48px; margin-bottom: 15px;">📊</div>
                                    <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucune tentative</h3>
                                    <p>Aucune tentative de test trouvée.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attempts as $attempt): 
                                $statusColors = [
                                    'in_progress' => ['bg' => 'rgba(255, 193, 7, 0.2)', 'text' => '#ffc107', 'label' => '⏳ En Cours'],
                                    'completed' => ['bg' => 'rgba(0, 255, 88, 0.2)', 'text' => '#00ff88', 'label' => '✅ Terminé'],
                                    'expired' => ['bg' => 'rgba(255, 51, 51, 0.2)', 'text' => '#ff6b6b', 'label' => '⏱️ Expiré']
                                ];
                                $statusInfo = $statusColors[$attempt['status']] ?? ['bg' => 'rgba(128, 128, 128, 0.2)', 'text' => '#808080', 'label' => '❓ Inconnu'];
                                
                                $timeTaken = $attempt['time_taken'] ?? 0;
                                $minutes = floor($timeTaken / 60);
                                $seconds = $timeTaken % 60;
                                $timeDisplay = $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
                            ?>
                                <tr>
                                    <td style="color: #00ffcc; font-weight: 700; font-family: monospace;">#<?php echo $attempt['id']; ?></td>
                                    <td>
                                        <strong style="color: var(--accent-cyan);"><?php echo htmlspecialchars($attempt['username']); ?></strong>
                                    </td>
                                    <td style="color: #a0a0a0;"><?php echo htmlspecialchars($attempt['email']); ?></td>
                                    <td>
                                        <?php if ($attempt['status'] === 'completed'): ?>
                                            <strong style="color: <?php echo $attempt['score'] >= 75 ? '#00ff88' : ($attempt['score'] >= 50 ? '#ffc107' : '#ff6b6b'); ?>; font-size: 16px;">
                                                <?php echo number_format($attempt['score'], 1); ?>%
                                            </strong>
                                        <?php else: ?>
                                            <span style="color: #a0a0a0;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #e0e0e0;">
                                        <strong><?php echo $attempt['correct_answers']; ?></strong> / <?php echo $attempt['total_questions']; ?>
                                    </td>
                                    <td style="color: #a0a0a0; font-family: monospace;"><?php echo $timeDisplay; ?></td>
                                    <td style="color: #707070; font-size: 12px;"><?php echo date('d/m/Y H:i', strtotime($attempt['started_at'])); ?></td>
                                    <td>
                                        <span class="badge" style="display: inline-block; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: <?php echo $statusInfo['bg']; ?>; border: 1px solid <?php echo str_replace('0.2', '0.5', $statusInfo['bg']); ?>; color: <?php echo $statusInfo['text']; ?>;">
                                            <?php echo $statusInfo['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <a href="?controller=adminTest&action=reviewResult&attempt_id=<?php echo $attempt['id']; ?>" 
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





