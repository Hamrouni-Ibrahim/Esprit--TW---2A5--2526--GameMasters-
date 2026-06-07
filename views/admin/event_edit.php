<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Beautiful Edit Event Buttons */
    .btn-event-update {
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
    
    .btn-event-update::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-event-update:hover::before {
        left: 100%;
    }
    
    .btn-event-update:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 25px rgba(59, 130, 246, 0.6),
            0 0 0 1px rgba(96, 165, 250, 0.4) inset,
            0 0 30px rgba(59, 130, 246, 0.4);
        background-position: right center;
    }
    
    .btn-event-cancel {
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
    
    .btn-event-cancel::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(232, 121, 249, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-event-cancel:hover::before {
        left: 100%;
    }
    
    .btn-event-cancel:hover {
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
            <h2>✏️ Modifier l'Événement</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Modifier les informations de l'événement : <?= htmlspecialchars($event['nom_evenet'] ?? 'N/A') ?></p>
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

        <?php if ($event): ?>
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Modifier les Détails de l'Événement</h2>
                </div>
                <form method="POST" action="?action=admin_event_edit&id=<?= $event['idevent'] ?>" enctype="multipart/form-data">
                    <?php
                    // Handle both new structure (date_debut, date_fin) and old structure (dateevent, duree)
                    if (isset($event['date_debut']) && isset($event['date_fin'])) {
                        $date_debut = new DateTime($event['date_debut']);
                        $date_fin = new DateTime($event['date_fin']);
                        $date_debut_value = $date_debut->format('Y-m-d');
                        $heure_debut_value = $date_debut->format('H:i');
                        $date_fin_value = $date_fin->format('Y-m-d');
                        $heure_fin_value = $date_fin->format('H:i');
                    } else {
                        // Old structure fallback
                        $date_debut = new DateTime($event['dateevent']);
                        $date_debut_value = $date_debut->format('Y-m-d');
                        $heure_debut_value = '00:00';
                        $date_fin_value = $date_debut->format('Y-m-d');
                        $heure_fin_value = '00:00';
                    }
                    ?>
                    <div class="admin-form-group">
                        <label for="event_nom">Nom de l'Événement *</label>
                        <input type="text" id="event_nom" name="nom" value="<?= htmlspecialchars($event['nom_evenet']) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_date_debut">Date de Début *</label>
                        <input type="date" id="event_date_debut" name="date_debut" value="<?= htmlspecialchars($date_debut_value) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_heure_debut">Heure de Début *</label>
                        <input type="time" id="event_heure_debut" name="heure_debut" value="<?= htmlspecialchars($heure_debut_value) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_date_fin">Date de Fin *</label>
                        <input type="date" id="event_date_fin" name="date_fin" value="<?= htmlspecialchars($date_fin_value) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_heure_fin">Heure de Fin *</label>
                        <input type="time" id="event_heure_fin" name="heure_fin" value="<?= htmlspecialchars($heure_fin_value) ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_description">Description</label>
                        <textarea id="event_description" name="description" rows="5"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label for="event_image">Image de l'Événement</label>
                        <?php if (!empty($event['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="<?= htmlspecialchars($event['image']) ?>" alt="Image actuelle" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2);">
                                <p style="color: #a0a0a0; font-size: 12px; margin-top: 5px;">Image actuelle</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="event_image" name="image" accept="image/*">
                        <small style="color: #a0a0a0; font-size: 12px;">Formats acceptés: JPG, PNG, GIF (max 5MB). Laisser vide pour conserver l'image actuelle.</small>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn-event-update">
                            <span style="margin-right: 8px;">💾</span>
                            Mettre à jour
                        </button>
                        <a href="?action=admin_events" class="btn-event-cancel">
                            <span style="margin-right: 8px;">↩</span>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; color: #ff4d4d; font-size: 1.2em;">
                Événement non trouvé.
                <a href="?action=admin_events" class="btn-event-cancel" style="margin-top: 20px; display: inline-block; text-decoration: none;">
                    <span style="margin-right: 8px;">↩</span>
                    Retour à la liste des événements
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
