<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Beautiful Edit Participation Buttons */
    .btn-participation-update {
        flex: 1;
        padding: 16px 30px;
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 50%, #93c5fd 100%);
        background-size: 200% 200%;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 4px 15px rgba(59, 130, 246, 0.4),
            0 0 0 1px rgba(96, 165, 250, 0.2) inset;
        text-transform: uppercase;
        animation: gradientShift 3s ease infinite;
    }
    
    .btn-participation-update::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-participation-update:hover::before {
        left: 100%;
    }
    
    .btn-participation-update:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 25px rgba(59, 130, 246, 0.6),
            0 0 0 1px rgba(96, 165, 250, 0.4) inset,
            0 0 30px rgba(59, 130, 246, 0.4);
        background-position: right center;
    }
    
    .btn-participation-cancel {
        flex: 1;
        padding: 16px 30px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        color: #e879f9;
        border: 2px solid rgba(232, 121, 249, 0.4);
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        text-align: center;
        display: inline-block;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(232, 121, 249, 0.2);
    }
    
    .btn-participation-cancel::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(232, 121, 249, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-participation-cancel:hover::before {
        left: 100%;
    }
    
    .btn-participation-cancel:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, rgba(232, 121, 249, 0.2) 0%, rgba(192, 132, 252, 0.15) 100%);
        border-color: rgba(232, 121, 249, 0.7);
        box-shadow: 
            0 8px 25px rgba(232, 121, 249, 0.4),
            0 0 30px rgba(232, 121, 249, 0.3);
        color: #f0abfc;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
</style>

<section class="admin-content">
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>✏️ Modifier la Participation</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Modifier les informations de la participation</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['admin_success'])): ?>
            <div style="background: rgba(76, 175, 80, 0.2); border: 1px solid #4CAF50; color: #4CAF50; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['admin_success']) ?>
            </div>
            <?php unset($_SESSION['admin_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_error'])): ?>
            <div style="background: rgba(255, 77, 77, 0.2); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['admin_error']) ?>
            </div>
            <?php unset($_SESSION['admin_error']); ?>
        <?php endif; ?>

        <?php if ($participation): ?>
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Modifier les Détails de la Participation</h2>
                </div>
                <form method="POST" action="?action=admin_participation_edit&id=<?= $participation['id'] ?>">
                    <div class="admin-form-group">
                        <label for="participation_event">Événement *</label>
                        <select id="participation_event" name="event_id" required>
                            <option value="">Sélectionner un événement</option>
                            <?php foreach($events as $event): ?>
                                <option value="<?= $event['idevent'] ?>" <?= $event['idevent'] == $participation['idevent'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['nom_evenet']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label for="participation_name">Nom *</label>
                        <input type="text" id="participation_name" name="name" value="<?= htmlspecialchars($participation['nom']) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="participation_email">Email *</label>
                        <input type="email" id="participation_email" name="email" value="<?= htmlspecialchars($participation['email']) ?>" required>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn-participation-update">
                            <span style="margin-right: 8px;">💾</span>
                            Mettre à jour
                        </button>
                        <a href="?action=admin_participations" class="btn-participation-cancel">
                            <span style="margin-right: 8px;">↩</span>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; color: #ff4d4d; font-size: 1.2em;">
                Participation non trouvée.
                <a href="?action=admin_participations" class="btn-participation-cancel" style="margin-top: 20px; display: inline-block; text-decoration: none;">
                    <span style="margin-right: 8px;">↩</span>
                    Retour à la liste des participations
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>




