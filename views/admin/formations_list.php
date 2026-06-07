<?php 
$pageTitle = 'Gestion des Formations - Admin';
$currentPage = 'formations';
include "views/admin/includes/header.php"; 
?>

<style>
    /* Beautiful Admin Formation Buttons */
    .btn-formation-edit,
    .btn-formation-delete,
    .btn-formation-manage,
    .btn-formation-add-edu {
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
        font-size: 18px;
    }
    
    .btn-formation-edit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
        border-color: rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
    }
    
    .btn-formation-edit::before {
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
    
    .btn-formation-edit:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-formation-edit:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(59, 130, 246, 0.8);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(59, 130, 246, 0.4),
            0 0 20px rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }
    
    .btn-formation-edit span {
        position: relative;
        z-index: 1;
    }
    
    .btn-formation-delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(248, 113, 113, 0.15) 100%);
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2);
    }
    
    .btn-formation-delete::before {
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
    
    .btn-formation-delete:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-formation-delete:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(239, 68, 68, 0.8);
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(248, 113, 113, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(239, 68, 68, 0.4),
            0 0 20px rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    
    .btn-formation-delete span {
        position: relative;
        z-index: 1;
    }
    
    .btn-formation-manage {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(52, 211, 153, 0.15) 100%);
        border-color: rgba(16, 185, 129, 0.4);
        color: #34d399;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.2);
    }
    
    .btn-formation-manage::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-formation-manage:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-formation-manage:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(16, 185, 129, 0.8);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(52, 211, 153, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(16, 185, 129, 0.4),
            0 0 20px rgba(16, 185, 129, 0.3);
        color: #6ee7b7;
    }
    
    .btn-formation-add-edu {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
        border-color: rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
    }
    
    .btn-formation-add-edu::before {
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
    
    .btn-formation-add-edu:hover::before {
        width: 100px;
        height: 100px;
    }
    
    .btn-formation-add-edu:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: rgba(59, 130, 246, 0.8);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(59, 130, 246, 0.4),
            0 0 20px rgba(59, 130, 246, 0.3);
        color: #93c5fd;
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
            <h2>Liste des Formations</h2>
            <a href="?controller=formation&action=add" class="btn btn-primary">Ajouter une Formation</a>
        </div>

        <?php if ($results->rowCount() > 0) { ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Difficulté</th>
                            <th>Durée</th>
                            <th>Catégorie</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $results->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['difficulte'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['duree'] ?? 0); ?>h</td>
                                <td><?php echo htmlspecialchars($row['categorie'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <a href="?controller=formation&action=edit&id=<?php echo $row['id']; ?>" class="btn-formation-edit" title="Modifier">
                                            <span style="font-size: 18px;">✎</span>
                                        </a>
                                        <a href="?controller=formation&action=delete&id=<?php echo $row['id']; ?>" 
                                           class="btn-formation-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?');" 
                                           title="Supprimer">
                                            <span style="font-size: 18px;">×</span>
                                        </a>
                                        <a href="?controller=formation&action=manageTree&id=<?php echo $row['id']; ?>" class="btn-formation-manage" title="Gérer Arbre">
                                            🌳
                                        </a>
                                        <a href="?controller=education&action=add&formation_id=<?php echo $row['id']; ?>" class="btn-formation-add-edu" title="Ajouter Éducation">
                                            ➕
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-items">Aucune formation disponible. <a href="?controller=formation&action=add">Ajoutez-en une</a>.</p>
        <?php } ?>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
