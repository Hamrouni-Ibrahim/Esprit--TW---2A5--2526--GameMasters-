<?php 
$pageTitle = 'Gestion des Éducations - Admin';
$currentPage = 'educations';
include "views/admin/includes/header.php"; 
?>

<style>
    /* Beautiful Admin Education Buttons */
    .btn-education-edit,
    .btn-education-delete {
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
    
    .btn-education-edit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
        border-color: rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
    }
    
    .btn-education-edit::before {
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
    
    .btn-education-edit:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-education-edit:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(59, 130, 246, 0.8);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(59, 130, 246, 0.4),
            0 0 20px rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }
    
    .btn-education-edit span {
        position: relative;
        z-index: 1;
    }
    
    .btn-education-delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(248, 113, 113, 0.15) 100%);
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2);
    }
    
    .btn-education-delete::before {
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
    
    .btn-education-delete:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-education-delete:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(239, 68, 68, 0.8);
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(248, 113, 113, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(239, 68, 68, 0.4),
            0 0 20px rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    
    .btn-education-delete span {
        position: relative;
        z-index: 1;
    }
</style>

<!-- Admin Content -->
<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
        <div class="admin-shape shape4"></div>
        <div class="admin-shape shape5"></div>
        <div class="admin-shape shape6"></div>
    </div>
    <div class="admin-particles" id="adminParticles"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>Liste des Éducations</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Les éducations doivent être créées depuis une formation. <a href="?controller=formation&action=adminList" style="color: #e879f9;">Voir les formations</a></p>
        </div>

        <?php if (count($results) > 0) { ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Formation</th>
                            <th>Difficulté</th>
                            <th>Durée</th>
                            <th>Catégorie</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $row) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td>
                                    <?php if (!empty($row['formation_title'])): ?>
                                        <span style="color: #e879f9;"><?php echo htmlspecialchars($row['formation_title']); ?></span>
                                    <?php else: ?>
                                        <span style="color: #a0a0a0; font-style: italic;">Aucune formation</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['difficulte'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['duree'] ?? 0); ?>h</td>
                                <td><?php echo htmlspecialchars($row['categorie'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <a href="?controller=education&action=edit&id=<?php echo $row['id']; ?>" class="btn-education-edit" title="Modifier">
                                            <span style="font-size: 18px;">✎</span>
                                        </a>
                                        <a href="?controller=education&action=delete&id=<?php echo $row['id']; ?>" 
                                           class="btn-education-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette éducation ?');" 
                                           title="Supprimer">
                                            <span style="font-size: 18px;">×</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-items">Aucune éducation disponible. <a href="?controller=formation&action=adminList">Créez une formation et ajoutez-y des éducations</a>.</p>
        <?php } ?>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
