<?php 
$pageTitle = 'Ajouter une Formation - Admin';
$currentPage = 'formations';
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
        <h2 class="admin-header-section" style="font-size: 32px; margin-bottom: 30px;">Ajouter une Formation</h2>

        <div class="admin-form-container">
            <form method="POST" action="?controller=formation&action=add" id="formationForm" onsubmit="return validateFormationForm()">
                <div class="admin-form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" required>
                    <span class="error-message" id="titleError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="10" required></textarea>
                    <span class="error-message" id="descriptionError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="competences">Compétences enseignées</label>
                    <input type="text" id="competences" name="competences" placeholder="ex: C#, Unity, Game Design">
                </div>

                <div class="admin-form-group">
                    <label for="difficulte">Niveau de difficulté *</label>
                    <select id="difficulte" name="difficulte" required>
                        <option value="Débutant">Débutant</option>
                        <option value="Intermédiaire">Intermédiaire</option>
                        <option value="Avancé">Avancé</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label for="duree">Durée (en heures) *</label>
                    <input type="number" id="duree" name="duree" min="0" required>
                    <span class="error-message" id="dureeError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="categorie">Catégorie *</label>
                    <input type="text" id="categorie" name="categorie" placeholder="ex: Plateforme 2D" required>
                    <span class="error-message" id="categorieError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="lien_ressources">Lien vers ressources</label>
                    <input type="url" id="lien_ressources" name="lien_ressources" placeholder="https://...">
                    <span class="error-message" id="lienRessourcesError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="impact_social">Impact social</label>
                    <textarea id="impact_social" name="impact_social" rows="5" placeholder="Informations facultatives sur l'impact communautaire"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ajouter la Formation</button>
                    <a href="?controller=formation&action=adminList" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
