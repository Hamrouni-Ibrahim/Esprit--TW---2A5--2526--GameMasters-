<?php 
$pageTitle = 'Examiner la Demande de Test - Admin';
$currentPage = 'test_requests';
include "views/admin/includes/header.php"; 
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        
        <div class="admin-header-section">
            <div>
                <h2>👁️ Examiner la Demande de Test</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Utilisateur: <strong><?php echo htmlspecialchars($request['username']); ?></strong></p>
            </div>
            <a href="?controller=adminTest&action=listRequests" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-form-container">
            <div class="card">
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #e879f9; margin-bottom: 15px;">📋 Informations de la Demande</h3>
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 20px;">
                        <p><strong>Utilisateur:</strong> <?php echo htmlspecialchars($request['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($request['email']); ?></p>
                        <p><strong>Date de demande:</strong> <?php echo date('d/m/Y à H:i', strtotime($request['created_at'])); ?></p>
                        <p>
                            <strong>Statut actuel:</strong> 
                            <span class="badge" style="background: <?php 
                                echo $request['status'] === 'pending' ? 'rgba(255, 193, 7, 0.2)' : 
                                    ($request['status'] === 'approved' ? 'rgba(0, 255, 88, 0.2)' : 'rgba(255, 51, 51, 0.2)'); 
                            ?>; color: <?php 
                                echo $request['status'] === 'pending' ? '#ffc107' : 
                                    ($request['status'] === 'approved' ? '#00ff88' : '#ff6b6b'); 
                            ?>;">
                                <?php 
                                echo $request['status'] === 'pending' ? '⏳ En Attente' : 
                                    ($request['status'] === 'approved' ? '✅ Approuvée' : '❌ Rejetée'); 
                                ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <h3 style="color: #e879f9; margin-bottom: 15px;">📄 Lettre de Motivation</h3>
                    <div style="background: rgba(232, 121, 249, 0.1); border-left: 4px solid #e879f9; border-radius: 10px; padding: 20px; min-height: 150px;">
                        <p style="color: #e0e0e0; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($request['motivational_letter']); ?></p>
                    </div>
                </div>

                <?php if ($request['admin_response']): ?>
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #00ffcc; margin-bottom: 15px;">💬 Réponse Précédente</h3>
                        <div style="background: rgba(0, 255, 204, 0.1); border-left: 4px solid #00ffcc; border-radius: 10px; padding: 20px;">
                            <p style="color: #e0e0e0; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($request['admin_response']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?controller=adminTest&action=reviewRequest&id=<?php echo $request['id']; ?>">
                    <div class="admin-form-group">
                        <label for="status">Décision *</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>⏳ En Attente</option>
                            <option value="approved" <?php echo $request['status'] === 'approved' ? 'selected' : ''; ?>>✅ Approuver</option>
                            <option value="rejected" <?php echo $request['status'] === 'rejected' ? 'selected' : ''; ?>>❌ Rejeter</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label for="admin_response">Réponse / Commentaires (optionnel)</label>
                        <textarea id="admin_response" name="admin_response" rows="6" 
                                  placeholder="Ajoutez un commentaire ou des instructions pour l'utilisateur..."><?php echo htmlspecialchars($request['admin_response'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="?controller=adminTest&action=listRequests" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">💾 Enregistrer la Décision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>






