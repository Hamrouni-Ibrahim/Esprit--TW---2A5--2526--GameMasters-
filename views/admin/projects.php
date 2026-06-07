<?php
// This file is included by the controller, so it doesn't need full HTML structure
// The header and footer are included by the controller

// Ensure variables are set to avoid errors if controller doesn't provide them
if (!isset($projects)) $projects = [];
if (!isset($projectStats)) $projectStats = ['total_projects' => 0, 'total_categories' => 0];
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>🌍 Gestion des Projets</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gérer tous les projets internationaux</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div class="stat-card" style="--card-accent: var(--accent-cyan);">
                <div class="stat-header">
                    <div class="stat-icon">📊</div>
                    <div class="stat-title">Projets Actifs</div>
                </div>
                <div class="stat-value"><?= $projectStats['total_projects'] ?? 0 ?></div>
            </div>
            <div class="stat-card" style="--card-accent: var(--accent-purple);">
                <div class="stat-header">
                    <div class="stat-icon">📁</div>
                    <div class="stat-title">Catégories</div>
                </div>
                <div class="stat-value"><?= $projectStats['total_categories'] ?? 0 ?></div>
            </div>
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

        <?php 
        // Check if projects table exists
        $tableExists = false;
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'projects'");
            $tableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $tableExists = false;
        }
        
        if (!$tableExists): ?>
            <div style="background: rgba(255, 193, 7, 0.2); border: 1px solid #ffc107; color: #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="margin-top: 0; color: #ffc107;">⚠️ Table 'projects' non trouvée</h3>
                <p>La table <code>projects</code> n'existe pas dans la base de données. Veuillez exécuter la migration SQL :</p>
                <p style="margin: 10px 0;"><strong>Fichier :</strong> <code>database/migrations/create_projects_table.sql</code></p>
                <p style="margin: 10px 0;">Ou exécutez cette requête SQL dans phpMyAdmin :</p>
                <pre style="background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 5px; overflow-x: auto; color: #00ffcc; margin: 10px 0;">CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT '',
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>
            </div>
        <?php endif; ?>

        <!-- Actions principales -->
        <div class="admin-card" style="padding: 20px 15px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h3 style="margin: 0; font-size: 18px;">📋 Liste des Projets</h3>
                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <button class="btn-admin btn-add" id="btnAddProject" data-modal="addProjectModal">
                        <i class="fas fa-plus"></i> Nouveau Projet
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Image</th>
                        <th style="min-width: 200px;">Titre</th>
                        <th style="min-width: 150px;">Catégorie</th>
                        <th style="min-width: 150px;">Date de création</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 40px; color: #a0a0a0;">
                                <div style="font-size: 48px; margin-bottom: 15px;">🌍</div>
                                <h3 style="color: #ffaa00; margin-bottom: 10px;">Aucun projet</h3>
                                <p>Commencez par ajouter un nouveau projet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($projects as $p): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($p['image'])): ?>
                                        <img src="<?= htmlspecialchars($p['image']) ?>" 
                                             style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);" 
                                             alt="<?= htmlspecialchars($p['title']) ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; width: 80px; height: 60px; background: linear-gradient(135deg, #9333ea 0%, #c084fc 100%); border-radius: 8px; align-items: center; justify-content: center; color: #fff; font-size: 24px;">
                                            🌍
                                        </div>
                                    <?php else: ?>
                                        <div style="width: 80px; height: 60px; background: linear-gradient(135deg, #9333ea 0%, #c084fc 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; border: 1px solid rgba(255,255,255,0.1);">
                                            🌍
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                                <td>
                                    <span class="badge badge-cat" style="display: inline-block; padding: 6px 12px; background: rgba(147, 51, 234, 0.2); border: 1px solid rgba(147, 51, 234, 0.4); border-radius: 20px; font-size: 12px; font-weight: 600; color: #c084fc;">
                                        <?= htmlspecialchars($p['category']) ?>
                                    </span>
                                </td>
                                <td style="color: #a0a0a0; font-size: 14px;">
                                    <?= isset($p['created_at']) ? date('d/m/Y', strtotime($p['created_at'])) : 'N/A' ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <a href="?action=admin_project_edit&id=<?= $p['id'] ?>" class="btn-admin btn-edit" title="Modifier" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=admin_project_delete&id=<?= $p['id'] ?>" class="btn-admin btn-delete" onclick="return confirm('Supprimer ce projet ?')" title="Supprimer" style="min-width: 40px; height: 40px; padding: 0;">
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

<!-- Modal Ajout Projet -->
<div id="addProjectModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2><i class="fas fa-plus" style="margin-right: 10px;"></i> Ajouter un Projet</h2>
            <button class="close-btn" onclick="hideModal('addProjectModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="addProjectForm" method="POST" action="?action=admin_project_add" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading" style="margin-right: 8px;"></i> Titre *</label>
                <input type="text" id="project_title" name="title" class="form-control" 
                       required placeholder="Titre du projet"
                       minlength="3" maxlength="255">
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag" style="margin-right: 8px;"></i> Catégorie *</label>
                <input type="text" id="project_category" name="category" class="form-control" 
                       required placeholder="Ex: Education, Santé, Environnement"
                       maxlength="100">
            </div>

            <div class="form-group">
                <label><i class="fas fa-image" style="margin-right: 8px;"></i> Image *</label>
                <input type="file" id="project_image" name="image" class="form-control" 
                       accept="image/jpeg,image/png,image/gif,image/webp" required>
                <small style="color: #a0a0a0; font-size: 12px; display: block; margin-top: 5px;">
                    Formats acceptés : JPG, PNG, GIF, WebP (max 5MB)
                </small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left" style="margin-right: 8px;"></i> Description *</label>
                <textarea id="project_description" name="description" class="form-control" 
                          rows="5" required placeholder="Détails du projet..."></textarea>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-admin btn-add" style="flex: 1;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Créer le projet
                </button>
                <button type="button" class="btn-admin btn-edit" onclick="hideModal('addProjectModal')">
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
    // Event listener for Add Project button
    const btnAddProject = document.getElementById('btnAddProject');
    if (btnAddProject) {
        btnAddProject.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof showModal === 'function') {
                showModal('addProjectModal');
            } else if (typeof window.showModal === 'function') {
                window.showModal('addProjectModal');
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
