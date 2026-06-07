<?php
// This file is included by the controller, so it doesn't need full HTML structure
// The header and footer are included by the controller

// Ensure variables are set to avoid errors if controller doesn't provide them
if (!isset($events)) $events = [];
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>📅 Gestion des Événements</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gérer tous les événements du site</p>
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

        <!-- Actions principales -->
        <div class="admin-card" style="padding: 20px 15px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h3 style="margin: 0; font-size: 18px;">📋 Liste des Événements</h3>
                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <button class="btn-admin btn-add" id="btnAddEvent" data-modal="addEventModal">
                        <i class="fas fa-calendar-plus"></i> Nouvel Événement
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width: 200px;">Nom</th>
                        <th style="min-width: 150px;">Date de Début</th>
                        <th style="min-width: 150px;">Date de Fin</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 40px; color: #a0a0a0;">
                                <div style="font-size: 48px; margin-bottom: 15px;">📅</div>
                                <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucun événement</h3>
                                <p>Commencez par ajouter un nouvel événement.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($events as $event): 
                            // Handle both new structure (date_debut, date_fin) and old structure (dateevent, duree)
                            if (isset($event['date_debut']) && isset($event['date_fin'])) {
                                $date_debut = new DateTime($event['date_debut']);
                                $date_fin = new DateTime($event['date_fin']);
                                $date_debut_str = $date_debut->format('d/m/Y H:i');
                                $date_fin_str = $date_fin->format('d/m/Y H:i');
                            } else {
                                // Old structure fallback
                                $date_debut = new DateTime($event['dateevent']);
                                $date_debut_str = $date_debut->format('d/m/Y');
                                $date_fin_str = 'N/A';
                            }
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($event['nom_evenet']) ?></strong></td>
                                <td><?= $date_debut_str ?></td>
                                <td><?= $date_fin_str ?></td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <a href="?action=admin_event_edit&id=<?= $event['idevent'] ?>" class="btn-admin btn-edit" title="Modifier" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=admin_event_delete&id=<?= $event['idevent'] ?>" class="btn-admin btn-delete" onclick="return confirm('Supprimer cet événement ?')" title="Supprimer" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-trash"></i>
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
</section>

<!-- Modal Ajout Événement -->
<div id="addEventModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2><i class="fas fa-calendar-plus" style="margin-right: 10px;"></i> Ajouter un Événement</h2>
            <button class="close-btn" onclick="hideModal('addEventModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="addEventForm" method="POST" action="?action=admin_event_add" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading" style="margin-right: 8px;"></i> Nom de l'Événement *</label>
                <input type="text" id="event_nom" name="nom" class="form-control" 
                       required placeholder="Ex: Conférence IA & Futur Digital"
                       minlength="3" maxlength="255">
            </div>

            <div class="form-group">
                <label><i class="fas fa-image" style="margin-right: 8px;"></i> Image de l'Événement</label>
                <input type="file" id="event_image" name="image" class="form-control" accept="image/*">
                <small style="color: #a0a0a0; font-size: 12px; display: block; margin-top: 5px;">Formats acceptés: JPG, PNG, GIF (max 5MB)</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Date de Début *</label>
                    <input type="date" id="event_date_debut" name="date_debut" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock" style="margin-right: 8px;"></i> Heure de Début *</label>
                    <input type="time" id="event_heure_debut" name="heure_debut" class="form-control" value="00:00" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="margin-right: 8px;"></i> Date de Fin *</label>
                    <input type="date" id="event_date_fin" name="date_fin" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock" style="margin-right: 8px;"></i> Heure de Fin *</label>
                    <input type="time" id="event_heure_fin" name="heure_fin" class="form-control" value="00:00" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left" style="margin-right: 8px;"></i> Description</label>
                <textarea id="event_description" name="description" class="form-control" rows="5" placeholder="Description détaillée de l'événement..."></textarea>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-admin btn-add" style="flex: 1;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Créer l'événement
                </button>
                <button type="button" class="btn-admin btn-edit" onclick="hideModal('addEventModal')">
                    <i class="fas fa-times" style="margin-right: 8px;"></i> Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important;';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
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

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for Add Event button
    const btnAddEvent = document.getElementById('btnAddEvent');
    if (btnAddEvent) {
        btnAddEvent.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof showModal === 'function') {
                showModal('addEventModal');
            } else if (typeof window.showModal === 'function') {
                window.showModal('addEventModal');
            }
        });
    }

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

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (typeof hideModal === 'function') {
                    hideModal(this.id);
                } else if (typeof window.hideModal === 'function') {
                    window.hideModal(this.id);
                }
            }
        });
    });
});
</script>
