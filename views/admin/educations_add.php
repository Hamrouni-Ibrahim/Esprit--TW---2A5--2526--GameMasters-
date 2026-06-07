<?php 
$pageTitle = 'Ajouter une Éducation - Admin';
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
        <h2 class="admin-header-section" style="font-size: 32px; margin-bottom: 30px;">Ajouter une Éducation</h2>

        <div class="admin-form-container">
            <?php if (isset($formation) && $formation): ?>
                <div style="background: rgba(147, 51, 234, 0.1); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                    <p style="color: #e879f9; margin: 0;"><strong>Ajout d'éducation pour la formation:</strong> <?php echo htmlspecialchars($formation['title']); ?></p>
                </div>
            <?php else: ?>
                <?php
                // Load all formations for dropdown if no formation_id provided
                if (!isset($formationsList)) {
                    require_once "models/Formation.php";
                    $formationModel = new Formation();
                    $allFormations = $formationModel->getAll();
                    $formationsList = [];
                    while ($row = $allFormations->fetch(PDO::FETCH_ASSOC)) {
                        $formationsList[] = $row;
                    }
                }
                ?>
                <?php if (empty($formationsList)): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; padding: 20px; margin-bottom: 20px; text-align: center;">
                        <p style="color: #f87171; margin: 0 0 15px 0;"><strong>⚠️ Aucune formation disponible</strong></p>
                        <p style="color: #a0a0a0; margin: 0 0 15px 0;">Vous devez créer une formation avant de pouvoir ajouter une éducation.</p>
                        <a href="?controller=formation&action=add" class="btn btn-primary" style="display: inline-block;">Créer une Formation</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form method="POST" action="?controller=education&action=add<?php echo isset($formation) && $formation ? '&formation_id=' . $formation['id'] : ''; ?>" id="educationForm" onsubmit="return validateEducationForm()">
                <?php if (isset($formation) && $formation): ?>
                    <input type="hidden" name="formation_id" value="<?php echo $formation['id']; ?>">
                <?php else: ?>
                    <?php if (!empty($formationsList)): ?>
                        <div class="admin-form-group">
                            <label for="formation_id">Formation * <span style="color: #a0a0a0; font-size: 12px;">(Une éducation doit être associée à une formation)</span></label>
                            <select id="formation_id" name="formation_id" required>
                                <option value="">-- Sélectionner une formation --</option>
                                <?php foreach ($formationsList as $form): ?>
                                    <option value="<?php echo $form['id']; ?>"><?php echo htmlspecialchars($form['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-message" id="formationIdError"></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
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
                    <label for="competences">Compétences acquises</label>
                    <input type="text" id="competences" name="competences" placeholder="ex: C++, POO, Boucles de jeu">
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
                    <label for="prerequis">Prérequis</label>
                    <textarea id="prerequis" name="prerequis" rows="3" placeholder="Connaissances préalables nécessaires"></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="categorie">Catégorie *</label>
                    <input type="text" id="categorie" name="categorie" placeholder="ex: Programmation, Design" required>
                    <span class="error-message" id="categorieError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="lien_ressources">Lien vers ressources</label>
                    <input type="url" id="lien_ressources" name="lien_ressources" placeholder="https://...">
                    <span class="error-message" id="lienRessourcesError"></span>
                </div>

                <div class="admin-form-group">
                    <label for="impact_social">Impact social</label>
                    <textarea id="impact_social" name="impact_social" rows="5" placeholder="Description facultative de l'impact sur la communauté"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ajouter l'Éducation</button>
                    <?php if (isset($formation) && $formation): ?>
                        <a href="?controller=formation&action=adminList" class="btn btn-secondary">Annuler</a>
                    <?php else: ?>
                        <a href="?controller=education&action=adminList" class="btn btn-secondary">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>
