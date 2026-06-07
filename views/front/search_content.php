<?php 
$pageTitle = 'Recherche de Contenu - Game Master';
$currentPage = 'search';
include "views/front/includes/header.php"; 
?>

<!-- Content Section -->
<section class="content-section">
    <div class="content-bg"></div>
    <div class="content-shapes">
        <div class="content-shape shape1"></div>
        <div class="content-shape shape2"></div>
        <div class="content-shape shape3"></div>
        <div class="content-shape shape4"></div>
        <div class="content-shape shape5"></div>
        <div class="content-shape shape6"></div>
    </div>
    <div class="content-particles" id="contentParticles"></div>
    <div class="content-container">
        <h2 class="section-title">Recherche de contenu par catégorie</h2>
        
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

        <form action="" method="POST" class="search-form">
            <div class="form-group">
                <label for="type">Type de contenu :</label>
                <select name="type" id="type" required>
                    <option value="formation">Formations</option>
                    <option value="education">Éducations</option>
                    <option value="both">Formations et Éducations</option>
                </select>
            </div>

            <div class="form-group">
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

            <button type="submit" name="search" class="btn btn-primary" style="width: 100%;">Rechercher</button>
        </form>

        <?php if (isset($results)) { ?>
            <h2 class="section-title" style="margin-top: 60px;"><?php echo htmlspecialchars($title); ?></h2>
            
            <?php if (count($results) > 0) { ?>
                <div class="items-grid">
                    <?php foreach ($results as $item) { ?>
                        <div class="item-card">
                            <div class="content-type-badge">
                                <?php 
                                if (isset($item['prerequis'])) {
                                    echo '📚 Éducation';
                                    $controller = 'education';
                                } else {
                                    echo '🎓 Formation';
                                    $controller = 'formation';
                                }
                                ?>
                            </div>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <div class="card-meta">
                                <?php if (!empty($item['category_name'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($item['category_name']); ?></span>
                                <?php } ?>
                                <?php if (!empty($item['difficulte'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($item['difficulte']); ?></span>
                                <?php } ?>
                                <?php if (!empty($item['duree'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($item['duree']); ?>h</span>
                                <?php } ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($item['description'], 0, 150)) . '...'; ?></p>
                            
                            <?php if (isset($item['prerequis'])) { ?>
                                <a href="?controller=education&action=detail&id=<?php echo $item['id']; ?>" class="btn btn-primary">Voir plus</a>
                            <?php } else { ?>
                                <a href="?controller=formation&action=detail&id=<?php echo $item['id']; ?>" class="btn btn-primary">Voir plus</a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="no-items">Aucun contenu trouvé pour cette catégorie.</p>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>
