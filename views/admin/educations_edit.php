<?php 
$pageTitle = 'Modifier une Éducation - Admin';
$currentPage = 'educations';
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
        <?php if (isset($result) && $result) { ?>
            <h2 class="admin-header-section" style="font-size: 32px; margin-bottom: 30px;">Modifier une Éducation</h2>

            <div class="admin-form-container">
                <form method="POST" action="?controller=education&action=edit&id=<?php echo $result['id']; ?>" id="educationForm" onsubmit="return validateEducationForm()">
                    <div class="admin-form-group">
                        <label for="title">Titre *</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($result['title']); ?>" required>
                        <span class="error-message" id="titleError"></span>
                    </div>

                    <div class="admin-form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="10" required><?php echo htmlspecialchars($result['description'] ?? ''); ?></textarea>
                        <span class="error-message" id="descriptionError"></span>
                    </div>

                    <div class="admin-form-group">
                        <label for="competences">Compétences acquises</label>
                        <input type="text" id="competences" name="competences" value="<?php echo htmlspecialchars($result['competences'] ?? ''); ?>" placeholder="ex: C++, POO, Boucles de jeu">
                    </div>

                    <div class="admin-form-group">
                        <label for="difficulte">Niveau de difficulté *</label>
                        <select id="difficulte" name="difficulte" required>
                            <option value="Débutant" <?php echo (isset($result['difficulte']) && $result['difficulte'] == 'Débutant') ? 'selected' : ''; ?>>Débutant</option>
                            <option value="Intermédiaire" <?php echo (isset($result['difficulte']) && $result['difficulte'] == 'Intermédiaire') ? 'selected' : ''; ?>>Intermédiaire</option>
                            <option value="Avancé" <?php echo (isset($result['difficulte']) && $result['difficulte'] == 'Avancé') ? 'selected' : ''; ?>>Avancé</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label for="duree">Durée (en heures) *</label>
                        <input type="number" id="duree" name="duree" min="0" value="<?php echo htmlspecialchars($result['duree'] ?? 0); ?>" required>
                        <span class="error-message" id="dureeError"></span>
                    </div>

                    <div class="admin-form-group">
                        <label for="prerequis">Prérequis</label>
                        <textarea id="prerequis" name="prerequis" rows="3" placeholder="Connaissances préalables nécessaires"><?php echo htmlspecialchars($result['prerequis'] ?? ''); ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label for="categorie">Catégorie *</label>
                        <input type="text" id="categorie" name="categorie" value="<?php echo htmlspecialchars($result['categorie'] ?? ''); ?>" placeholder="ex: Programmation, Design" required>
                        <span class="error-message" id="categorieError"></span>
                    </div>

                    <div class="admin-form-group">
                        <label for="lien_ressources">Lien vers ressources</label>
                        <input type="url" id="lien_ressources" name="lien_ressources" value="<?php echo htmlspecialchars($result['lien_ressources'] ?? ''); ?>" placeholder="https://...">
                        <span class="error-message" id="lienRessourcesError"></span>
                    </div>

                    <div class="admin-form-group">
                        <label for="impact_social">Impact social</label>
                        <textarea id="impact_social" name="impact_social" rows="5" placeholder="Description facultative de l'impact sur la communauté"><?php echo htmlspecialchars($result['impact_social'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="?controller=education&action=adminList" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php } else { ?>
            <div class="no-items">
                <p>Éducation introuvable.</p>
                <div class="form-actions" style="justify-content: center; margin-top: 20px;">
                    <a href="?controller=education&action=adminList" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
