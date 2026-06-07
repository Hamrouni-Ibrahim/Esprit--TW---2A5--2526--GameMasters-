<?php 
$pageTitle = 'Recherche Admin - Game Master';
$currentPage = 'search';
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
        <?php
        // Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
            $type = $_POST['type'];
            $category_id = $_POST['category'];
            
            if ($type === 'formation') {
                $results = $this->getFormationsByCategory($category_id);
                $title = "Formations correspondantes à la catégorie sélectionnée";
            } else if ($type === 'education') {
                $results = $this->getEducationsByCategory($category_id);
                $title = "Éducations correspondantes à la catégorie sélectionnée";
            } else if ($type === 'both') {
                // Get both formations and educations for the same category
                $formations = $this->getFormationsByCategory($category_id);
                $educations = $this->getEducationsByCategory($category_id);
                $results = array_merge($formations, $educations);
                $title = "Formations et Éducations correspondantes à la catégorie sélectionnée";
            }
        }
        ?>

        <div class="admin-header-section">
            <h2>Recherche de contenu par catégorie</h2>
        </div>
        
        <div class="admin-form-container">
            <form action="" method="POST" class="search-form">
                <div class="admin-form-group">
                    <label for="type">Type de contenu :</label>
                    <select name="type" id="type" required>
                        <option value="formation">Formations</option>
                        <option value="education">Éducations</option>
                        <option value="both">Formations et Éducations</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label for="category">Sélectionnez une catégorie :</label>
                    <select name="category" id="category" required>
                        <option value="">Choisissez une catégorie</option>
                        <?php
                        $categories = $this->getAllCategories();
                        foreach ($categories as $category) {
                            echo '<option value="'. $category['id']. '">' . htmlspecialchars($category['nom']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" name="search" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>

        <?php if (isset($results)) { ?>
            <div class="admin-header-section" style="margin-top: 60px;">
                <h2><?php echo htmlspecialchars($title); ?></h2>
            </div>
            
            <?php if (count($results) > 0) { ?>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Difficulté</th>
                                <th>Durée</th>
                                <th>Catégorie</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $item) { 
                                $controller = isset($item['prerequis']) ? 'education' : 'formation';
                                $type_label = isset($item['prerequis']) ? 'Éducation' : 'Formation';
                            ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <span class="badge" style="display: inline-block; padding: 6px 12px; background: rgba(232, 121, 249, 0.1); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 8px; font-size: 12px; color: #e879f9;">
                                            <?php echo $type_label; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['difficulte'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['duree'] ?? 0); ?>h</td>
                                    <td><?php echo htmlspecialchars($item['category_name'] ?? $item['categorie'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                    <td class="actions-cell">
                                        <a href="?controller=<?php echo $controller; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-edit">Modifier</a>
                                        <a href="?controller=<?php echo $controller; ?>&action=delete&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <p class="no-items">Aucun contenu trouvé pour cette catégorie.</p>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
