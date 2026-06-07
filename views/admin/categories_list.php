<?php
$pageTitle = 'Gestion des Catégories - Game Master';
$currentPage = 'categories';
include "views/admin/includes/header.php";
?>

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
            <h2>Liste des Catégories</h2>
            <a href="?controller=category&action=adminAdd" class="btn btn-primary">Ajouter une Catégorie</a>
        </div>

        <?php if ($categories->rowCount() > 0) { ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $categories->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="?controller=category&action=adminEdit&id=<?php echo $row['id']; ?>" class="btn btn-edit">Modifier</a>
                                    <a href="?controller=category&action=adminDelete&id=<?php echo $row['id']; ?>"
                                       class="btn btn-delete"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-items">Aucune catégorie disponible. <a href="?controller=category&action=adminAdd">Ajoutez-en une</a>.</p>
        <?php } ?>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
