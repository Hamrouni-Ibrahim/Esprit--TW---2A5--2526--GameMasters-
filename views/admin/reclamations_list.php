<?php
// This file is included by the controller, so header/footer are already included
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>📋 Gestion des Réclamations</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Administrez et répondez aux réclamations des utilisateurs</p>
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

        <!-- Search and Filter Section -->
        <div class="section-card" style="margin-bottom: 30px;">
            <div class="section-header">
                <h2 class="section-title">🔍 Recherche et Filtres</h2>
            </div>
            <form method="GET" action="?action=admin_reclamations" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <input type="hidden" name="action" value="admin_reclamations">
                
                <div class="admin-form-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           placeholder="Titre ou description...">
                </div>

                <div class="admin-form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                        <option value="traité" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'traité') ? 'selected' : ''; ?>>Traité</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label for="date_debut">Date début</label>
                    <input type="date" id="date_debut" name="date_debut"
                           value="<?php echo isset($_GET['date_debut']) ? htmlspecialchars($_GET['date_debut']) : ''; ?>">
                </div>

                <div class="admin-form-group">
                    <label for="date_fin">Date fin</label>
                    <input type="date" id="date_fin" name="date_fin"
                           value="<?php echo isset($_GET['date_fin']) ? htmlspecialchars($_GET['date_fin']) : ''; ?>">
                </div>

                <div class="admin-form-group">
                    <label for="order_by">Trier par</label>
                    <select id="order_by" name="order_by">
                        <option value="date_desc" <?php echo (!isset($_GET['order_by']) || $_GET['order_by'] == 'date_desc') ? 'selected' : ''; ?>>Plus récent</option>
                        <option value="date_asc" <?php echo (isset($_GET['order_by']) && $_GET['order_by'] == 'date_asc') ? 'selected' : ''; ?>>Plus ancien</option>
                        <option value="titre_asc" <?php echo (isset($_GET['order_by']) && $_GET['order_by'] == 'titre_asc') ? 'selected' : ''; ?>>Titre A-Z</option>
                        <option value="titre_desc" <?php echo (isset($_GET['order_by']) && $_GET['order_by'] == 'titre_desc') ? 'selected' : ''; ?>>Titre Z-A</option>
                        <option value="statut_asc" <?php echo (isset($_GET['order_by']) && $_GET['order_by'] == 'statut_asc') ? 'selected' : ''; ?>>Statut A-Z</option>
                        <option value="statut_desc" <?php echo (isset($_GET['order_by']) && $_GET['order_by'] == 'statut_desc') ? 'selected' : ''; ?>>Statut Z-A</option>
                    </select>
                </div>

                <div class="admin-form-group" style="display: flex; gap: 10px; align-items: flex-end;">
                    <button type="submit" class="btn-reclamation-search" style="flex: 1;">
                        <span style="margin-right: 8px;">🔍</span>
                        Rechercher
                    </button>
                    <a href="?action=admin_reclamations" class="btn-reclamation-clear" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <span style="margin-right: 8px;">🗑️</span>
                        Effacer
                    </a>
                </div>
            </form>
        </div>

        <!-- Reclamations List -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">Liste des Réclamations</h2>
            </div>
            <div style="overflow-x: auto;">
                <?php 
                // Ensure reclamations array is set
                if (!isset($reclamations)) {
                    $reclamations = [];
                }
                $resultCount = is_array($reclamations) ? count($reclamations) : 0;
                $hasFilters = !empty($_GET['search']) || !empty($_GET['statut']) || !empty($_GET['date_debut']) || !empty($_GET['date_fin']);
                ?>
                
                <?php if($resultCount > 0): ?>
                    <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid #00ffcc; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;">
                        <span style="color: #00ffcc; font-weight: 600;">
                            📊 <?php echo $resultCount; ?> réclamation<?php echo $resultCount > 1 ? 's' : ''; ?> trouvée<?php echo $resultCount > 1 ? 's' : ''; ?>
                            <?php if($hasFilters): ?>
                                <span style="color: #ffffff;">(filtres appliqués)</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reclamations as $row): ?>
                                <tr>
                                    <td style="color: #00ffcc; font-weight: 700; font-family: monospace;">
                                        #<?php echo $row['id']; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['username'] ?? 'N/A'); ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['titre']); ?></strong></td>
                                    <td style="max-width: 300px;">
                                        <?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...
                                        <?php if(!empty($row['image_path'])): ?>
                                            <div style="margin-top: 5px; padding: 4px 8px; background: rgba(0, 255, 204, 0.1); border-radius: 4px; display: inline-block; font-size: 11px; color: #00ffcc;">
                                                📸 Image
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; <?php echo $row['statut'] == 'traité' ? 'background: rgba(0, 255, 136, 0.2); border: 1px solid rgba(0, 255, 136, 0.5); color: #00ff88;' : 'background: rgba(255, 204, 0, 0.2); border: 1px solid rgba(255, 204, 0, 0.5); color: #ffcc00;'; ?>">
                                            <?php echo ucfirst($row['statut']); ?>
                                        </span>
                                    </td>
                                    <td style="color: #707070; font-size: 12px;">
                                        <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                            <button class="btn-admin btn-edit btn-view-reclamation" 
                                                    data-reclamation-id="<?= $row['id'] ?>"
                                                    data-reclamation-username="<?= htmlspecialchars($row['username'] ?? 'N/A', ENT_QUOTES) ?>"
                                                    data-reclamation-titre="<?= htmlspecialchars($row['titre'], ENT_QUOTES) ?>"
                                                    data-reclamation-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                                                    data-reclamation-statut="<?= htmlspecialchars($row['statut'], ENT_QUOTES) ?>"
                                                    data-reclamation-date="<?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>"
                                                    data-reclamation-image="<?= htmlspecialchars($row['image_path'] ?? '', ENT_QUOTES) ?>"
                                                    title="Voir les détails" style="min-width: 40px; height: 40px; padding: 0;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="?action=admin_reclamation_respond&id=<?php echo $row['id']; ?>" 
                                               class="btn-reclamation-respond" title="Répondre">
                                                <span style="font-size: 18px;">✉</span>
                                            </a>
                                            <a href="?action=admin_reclamation_edit&id=<?php echo $row['id']; ?>" 
                                               class="btn-reclamation-edit" title="Modifier">
                                                <span style="font-size: 18px;">✎</span>
                                            </a>
                                            <a href="?action=admin_reclamation_delete&id=<?php echo $row['id']; ?>"
                                               class="btn-reclamation-delete"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')"
                                               title="Supprimer">
                                                <span style="font-size: 18px;">×</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 64px; margin-bottom: 20px;">
                            <?php echo $hasFilters ? '🔍' : '📊'; ?>
                        </div>
                        <h3 style="color: #ffffff; font-size: 24px; margin-bottom: 15px;">
                            <?php echo $hasFilters ? 'Aucun Résultat' : 'Aucune Réclamation'; ?>
                        </h3>
                        <p style="color: #a0a0a0; font-size: 16px; margin-bottom: 30px;">
                            <?php if($hasFilters): ?>
                                Aucun résultat ne correspond à vos critères de recherche.
                            <?php else: ?>
                                Aucune réclamation à afficher pour le moment.
                            <?php endif; ?>
                        </p>
                        <?php if($hasFilters): ?>
                            <a href="?action=admin_reclamations" class="btn-reclamation-clear" style="display: inline-block; text-decoration: none;">
                                <span style="margin-right: 8px;">🗑️</span>
                                Effacer les filtres
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Détails Réclamation -->
<div id="reclamationDetailsModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> Détails de la Réclamation</h2>
            <button class="close-btn" onclick="hideModal('reclamationDetailsModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="reclamationDetailsContent">
            <!-- Le contenu sera chargé dynamiquement -->
        </div>
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

// View reclamation details
function viewReclamationDetails(id, username, titre, description, statut, date, imagePath) {
    const statusColor = statut === 'traité' ? '#00ff88' : '#ffcc00';
    const statusBg = statut === 'traité' ? 'rgba(0, 255, 136, 0.2)' : 'rgba(255, 204, 0, 0.2)';
    const statusBorder = statut === 'traité' ? 'rgba(0, 255, 136, 0.5)' : 'rgba(255, 204, 0, 0.5)';
    
    const content = `
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #ef4444 0%, #f87171 50%, #fca5a5 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff; border: 3px solid rgba(239, 68, 68, 0.5); box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); margin: 0 auto;">
                ⚠️
            </div>
            <h3 style="color: var(--accent-red); margin: 20px 0 10px 0;">${titre}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 10px;">Réclamation #${id}</p>
            <span style="display: inline-block; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 700; text-transform: uppercase; background: ${statusBg}; border: 2px solid ${statusBorder}; color: ${statusColor};">
                ${statut === 'traité' ? '✅ Traité' : '⏳ En attente'}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <!-- Informations de la réclamation -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">ID Réclamation</strong>
                        <span style="color: var(--text-secondary);">#${id}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Utilisateur</strong>
                        <span style="color: var(--text-secondary);">${username}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date de création</strong>
                        <span style="color: var(--text-secondary);">${date}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Statut</strong>
                        <span style="display: inline-block; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: ${statusBg}; border: 1px solid ${statusBorder}; color: ${statusColor};">
                            ${statut === 'traité' ? '✅ Traité' : '⏳ En attente'}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenu de la réclamation -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-purple); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-file-alt"></i> Détails
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Titre</strong>
                        <span style="color: var(--text-secondary);">${titre}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-align-left"></i> Description
            </h4>
            <p style="color: var(--text-secondary); line-height: 1.5; margin: 0; white-space: pre-wrap;">${description}</p>
        </div>

        ${imagePath ? `
        <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h4 style="color: var(--accent-cyan); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-image"></i> Image jointe
            </h4>
            <div style="text-align: center;">
                <img src="${imagePath}" alt="Image de la réclamation" style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);" onerror="this.style.display='none';">
            </div>
        </div>
        ` : ''}

        <!-- Actions -->
        <div style="display: flex; gap: 15px; justify-content: center; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 25px;">
            <a href="?action=admin_reclamation_respond&id=${id}" class="btn-admin btn-edit" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(74, 222, 128, 0.15) 100%); border-color: rgba(34, 197, 94, 0.4); color: #4ade80;">
                <i class="fas fa-reply" style="margin-right: 8px;"></i> Répondre
            </a>
            <a href="?action=admin_reclamation_edit&id=${id}" class="btn-admin btn-edit">
                <i class="fas fa-edit" style="margin-right: 8px;"></i> Modifier
            </a>
            <button class="btn-admin btn-edit" onclick="hideModal('reclamationDetailsModal')">
                <i class="fas fa-times" style="margin-right: 8px;"></i> Fermer
            </button>
        </div>
    `;

    const detailsContent = document.getElementById('reclamationDetailsContent');
    if (detailsContent) {
        detailsContent.innerHTML = content;
        showModal('reclamationDetailsModal');
    }
}

// Make functions globally available
window.showModal = showModal;
window.hideModal = hideModal;
window.viewReclamationDetails = viewReclamationDetails;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners for View Reclamation buttons
    document.querySelectorAll('.btn-view-reclamation').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const id = this.getAttribute('data-reclamation-id');
            const username = this.getAttribute('data-reclamation-username');
            const titre = this.getAttribute('data-reclamation-titre');
            const description = this.getAttribute('data-reclamation-description');
            const statut = this.getAttribute('data-reclamation-statut');
            const date = this.getAttribute('data-reclamation-date');
            const imagePath = this.getAttribute('data-reclamation-image');
            
            if (typeof viewReclamationDetails === 'function') {
                viewReclamationDetails(id, username, titre, description, statut, date, imagePath);
            } else if (typeof window.viewReclamationDetails === 'function') {
                window.viewReclamationDetails(id, username, titre, description, statut, date, imagePath);
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

<style>
    /* Beautiful Admin Reclamation Buttons */
    .btn-reclamation-search {
        padding: 12px 25px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
        border: 2px solid rgba(59, 130, 246, 0.4);
        border-radius: 10px;
        color: #60a5fa;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-reclamation-search::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-search:hover::before {
        width: 200px;
        height: 200px;
    }
    
    .btn-reclamation-search:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.8);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(59, 130, 246, 0.4),
            0 0 20px rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }
    
    .btn-reclamation-search span {
        position: relative;
        z-index: 1;
    }
    
    .btn-reclamation-clear {
        padding: 12px 25px;
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.15) 0%, rgba(156, 163, 175, 0.15) 100%);
        border: 2px solid rgba(107, 114, 128, 0.4);
        border-radius: 10px;
        color: #9ca3af;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(107, 114, 128, 0.2);
        text-transform: uppercase;
    }
    
    .btn-reclamation-clear::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(107, 114, 128, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-clear:hover::before {
        width: 200px;
        height: 200px;
    }
    
    .btn-reclamation-clear:hover {
        transform: translateY(-2px);
        border-color: rgba(107, 114, 128, 0.8);
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.3) 0%, rgba(156, 163, 175, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(107, 114, 128, 0.4),
            0 0 20px rgba(107, 114, 128, 0.3);
        color: #d1d5db;
    }
    
    .btn-reclamation-clear span {
        position: relative;
        z-index: 1;
    }
    
    .btn-reclamation-respond,
    .btn-reclamation-edit,
    .btn-reclamation-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        border: 2px solid;
    }
    
    .btn-reclamation-respond {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(74, 222, 128, 0.15) 100%);
        border-color: rgba(34, 197, 94, 0.4);
        color: #4ade80;
        box-shadow: 0 2px 10px rgba(34, 197, 94, 0.2);
    }
    
    .btn-reclamation-respond::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(34, 197, 94, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-respond:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-reclamation-respond:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(34, 197, 94, 0.8);
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.3) 0%, rgba(74, 222, 128, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(34, 197, 94, 0.4),
            0 0 20px rgba(34, 197, 94, 0.3);
        color: #86efac;
    }
    
    .btn-reclamation-edit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
        border-color: rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
    }
    
    .btn-reclamation-edit::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-edit:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-reclamation-edit:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(59, 130, 246, 0.8);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(59, 130, 246, 0.4),
            0 0 20px rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }
    
    .btn-reclamation-delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(248, 113, 113, 0.15) 100%);
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2);
    }
    
    .btn-reclamation-delete::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-delete:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-reclamation-delete:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(239, 68, 68, 0.8);
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(248, 113, 113, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(239, 68, 68, 0.4),
            0 0 20px rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    
    .btn-reclamation-respond span,
    .btn-reclamation-edit span,
    .btn-reclamation-delete span {
        position: relative;
        z-index: 1;
    }
</style>

