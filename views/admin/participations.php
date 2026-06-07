<?php
// This file is included by the controller, so it doesn't need full HTML structure
// The header and footer are included by the controller

// Ensure variables are set to avoid errors if controller doesn't provide them
if (!isset($participations)) $participations = [];
if (!isset($events)) $events = [];
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>👥 Gestion des Participations</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gérer toutes les participations aux événements</p>
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
                <h3 style="margin: 0; font-size: 18px;">📋 Liste des Participations</h3>
                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <button class="btn-admin btn-add" id="btnAddParticipation" data-modal="addParticipationModal">
                        <i class="fas fa-user-plus"></i> Nouvelle Participation
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Nom</th>
                        <th style="min-width: 200px;">Email</th>
                        <th style="min-width: 200px;">Événement</th>
                        <th style="min-width: 150px;">Date de Participation</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participations)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 40px; color: #a0a0a0;">
                                <div style="font-size: 48px; margin-bottom: 15px;">👥</div>
                                <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucune participation</h3>
                                <p>Commencez par ajouter une nouvelle participation.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($participations as $participation): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($participation['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($participation['email']) ?></td>
                                <td><?= htmlspecialchars($participation['nom_evenet'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($participation['date_participation'])) ?></td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="btn-admin btn-edit btn-view-participation" 
                                                data-participation-id="<?= $participation['id'] ?>"
                                                data-participation-nom="<?= htmlspecialchars($participation['nom'], ENT_QUOTES) ?>"
                                                data-participation-email="<?= htmlspecialchars($participation['email'], ENT_QUOTES) ?>"
                                                data-participation-event="<?= htmlspecialchars($participation['nom_evenet'] ?? 'N/A', ENT_QUOTES) ?>"
                                                data-participation-date="<?= date('d/m/Y H:i', strtotime($participation['date_participation'])) ?>"
                                                data-participation-event-id="<?= $participation['idevent'] ?? '' ?>"
                                                data-participation-description="<?= htmlspecialchars($participation['description'] ?? '', ENT_QUOTES) ?>"
                                                data-participation-date-debut="<?= isset($participation['date_debut']) && !empty($participation['date_debut']) ? date('d/m/Y H:i', strtotime($participation['date_debut'])) : '' ?>"
                                                data-participation-date-fin="<?= isset($participation['date_fin']) && !empty($participation['date_fin']) ? date('d/m/Y H:i', strtotime($participation['date_fin'])) : '' ?>"
                                                title="Voir les détails" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?action=admin_participation_edit&id=<?= $participation['id'] ?>" class="btn-admin btn-edit" title="Modifier" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=admin_participation_delete&id=<?= $participation['id'] ?>" class="btn-admin btn-delete" onclick="return confirm('Supprimer cette participation ?')" title="Supprimer" style="min-width: 40px; height: 40px; padding: 0;">
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

<!-- Modal Détails Participation -->
<div id="participationDetailsModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-user-check" style="margin-right: 10px;"></i> Détails de la Participation</h2>
            <button class="close-btn" onclick="hideModal('participationDetailsModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="participationDetailsContent">
            <!-- Le contenu sera chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- Modal Ajout Participation -->
<div id="addParticipationModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus" style="margin-right: 10px;"></i> Ajouter une Participation</h2>
            <button class="close-btn" onclick="hideModal('addParticipationModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="addParticipationForm" method="POST" action="?action=admin_participation_add">
            <div class="form-group">
                <label><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Événement *</label>
                <select id="participation_event" name="event_id" class="form-control" required>
                    <option value="">Sélectionner un événement</option>
                    <?php foreach($events as $event): ?>
                        <option value="<?= $event['idevent'] ?>"><?= htmlspecialchars($event['nom_evenet']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user" style="margin-right: 8px;"></i> Nom *</label>
                <input type="text" id="participation_name" name="name" class="form-control" 
                       required placeholder="Nom complet"
                       minlength="2" maxlength="100">
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email *</label>
                <input type="email" id="participation_email" name="email" class="form-control" 
                       required placeholder="email@example.com"
                       maxlength="255">
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-admin btn-add" style="flex: 1;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Créer la participation
                </button>
                <button type="button" class="btn-admin btn-edit" onclick="hideModal('addParticipationModal')">
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

// View participation details
function viewParticipationDetails(participationId, nom, email, eventName, participationDate, eventId, description, dateDebut, dateFin) {
    const content = `
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #9333ea 0%, #c084fc 50%, #e879f9 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff; border: 3px solid rgba(147, 51, 234, 0.5); box-shadow: 0 0 20px rgba(147, 51, 234, 0.3); margin: 0 auto;">
                ${nom.charAt(0).toUpperCase()}
            </div>
            <h3 style="color: var(--accent-cyan); margin: 20px 0 10px 0;">${nom}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 5px;">${email}</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <!-- Informations de participation -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations de Participation
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">ID Participation</strong>
                        <span style="color: var(--text-secondary);">#${participationId}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date d'inscription</strong>
                        <span style="color: var(--text-secondary);">${participationDate}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Email</strong>
                        <span style="color: var(--text-secondary); word-break: break-all;">${email}</span>
                    </div>
                </div>
            </div>

            <!-- Informations de l'événement -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-purple); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-calendar-alt"></i> Informations de l'Événement
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Nom de l'événement</strong>
                        <span style="color: var(--text-secondary);">${eventName}</span>
                    </div>
                    ${dateDebut ? `
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date de début</strong>
                        <span style="color: var(--text-secondary);">${dateDebut}</span>
                    </div>
                    ` : ''}
                    ${dateFin ? `
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date de fin</strong>
                        <span style="color: var(--text-secondary);">${dateFin}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>

        ${description ? `
        <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-align-left"></i> Description de l'Événement
            </h4>
            <p style="color: var(--text-secondary); line-height: 1.5; margin: 0;">${description}</p>
        </div>
        ` : ''}

        <!-- Actions -->
        <div style="display: flex; gap: 15px; justify-content: center; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 25px;">
            <a href="?action=admin_participation_edit&id=${participationId}" class="btn-admin btn-edit">
                <i class="fas fa-edit" style="margin-right: 8px;"></i> Modifier
            </a>
            <button class="btn-admin btn-edit" onclick="hideModal('participationDetailsModal')">
                <i class="fas fa-times" style="margin-right: 8px;"></i> Fermer
            </button>
        </div>
    `;

    const detailsContent = document.getElementById('participationDetailsContent');
    if (detailsContent) {
        detailsContent.innerHTML = content;
        showModal('participationDetailsModal');
    }
}

// Make function globally available
window.viewParticipationDetails = viewParticipationDetails;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for Add Participation button
    const btnAddParticipation = document.getElementById('btnAddParticipation');
    if (btnAddParticipation) {
        btnAddParticipation.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof showModal === 'function') {
                showModal('addParticipationModal');
            } else if (typeof window.showModal === 'function') {
                window.showModal('addParticipationModal');
            }
        });
    }

    // Event listeners for View Participation buttons
    document.querySelectorAll('.btn-view-participation').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const participationId = this.getAttribute('data-participation-id');
            const nom = this.getAttribute('data-participation-nom');
            const email = this.getAttribute('data-participation-email');
            const eventName = this.getAttribute('data-participation-event');
            const participationDate = this.getAttribute('data-participation-date');
            const eventId = this.getAttribute('data-participation-event-id');
            const description = this.getAttribute('data-participation-description');
            const dateDebut = this.getAttribute('data-participation-date-debut');
            const dateFin = this.getAttribute('data-participation-date-fin');
            
            if (typeof viewParticipationDetails === 'function') {
                viewParticipationDetails(participationId, nom, email, eventName, participationDate, eventId, description, dateDebut, dateFin);
            } else if (typeof window.viewParticipationDetails === 'function') {
                window.viewParticipationDetails(participationId, nom, email, eventName, participationDate, eventId, description, dateDebut, dateFin);
            }
        });
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
