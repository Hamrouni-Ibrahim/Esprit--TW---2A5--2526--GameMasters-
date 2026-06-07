<?php 
$pageTitle = (isset($result['title']) ? htmlspecialchars($result['title']) : 'Éducation') . ' - Game Master';
$currentPage = 'educations';
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
        <?php if (isset($result) && $result) { ?>
            <article class="detail-card">
                <h2><?php echo htmlspecialchars($result['title']); ?></h2>
                
                <div class="meta-info">
                    <?php if (!empty($result['categorie'])) { ?>
                        <span class="badge"><?php echo htmlspecialchars($result['categorie']); ?></span>
                    <?php } ?>
                    <?php if (!empty($result['difficulte'])) { ?>
                        <span class="badge"><?php echo htmlspecialchars($result['difficulte']); ?></span>
                    <?php } ?>
                    <?php if (!empty($result['duree'])) { ?>
                        <span class="badge"><?php echo htmlspecialchars($result['duree']); ?> heures</span>
                    <?php } ?>
                </div>

                <?php if (!empty($result['formation_title']) && !empty($result['formation_id'])) { ?>
                    <div class="info-section" style="background: rgba(0, 255, 204, 0.05); border-left-color: #00ffcc;">
                        <h3 style="color: #00ffcc;">📚 Partie de la Formation</h3>
                        <p style="margin: 10px 0;">
                            <strong>Cette éducation fait partie de:</strong><br>
                            <a href="?controller=formation&action=detail&id=<?php echo $result['formation_id']; ?>" 
                               style="color: #00ffcc; text-decoration: none; font-size: 18px; font-weight: 600;">
                                <?php echo htmlspecialchars($result['formation_title']); ?>
                            </a>
                        </p>
                        <a href="?controller=formation&action=detail&id=<?php echo $result['formation_id']; ?>" 
                           class="btn btn-primary" style="margin-top: 10px;">
                            Voir la formation complète
                        </a>
                    </div>
                <?php } ?>

                <div class="description">
                    <p><?php echo nl2br(htmlspecialchars($result['description'])); ?></p>
                </div>

                <?php if (!empty($result['prerequis'])) { ?>
                    <div class="info-section">
                        <h3>Prérequis</h3>
                        <p><?php echo nl2br(htmlspecialchars($result['prerequis'])); ?></p>
                    </div>
                <?php } ?>

                <?php if (!empty($result['competences'])) { ?>
                    <div class="info-section">
                        <h3>Compétences acquises</h3>
                        <p><?php echo htmlspecialchars($result['competences']); ?></p>
                    </div>
                <?php } ?>

                <?php if (!empty($result['lien_ressources'])) { ?>
                    <div class="info-section">
                        <h3>Ressources complémentaires</h3>
                        <p><a href="<?php echo htmlspecialchars($result['lien_ressources']); ?>" target="_blank" class="btn btn-primary">Accéder aux ressources</a></p>
                    </div>
                <?php } ?>

                <?php if (!empty($result['impact_social'])) { ?>
                    <div class="info-section">
                        <h3>Impact social</h3>
                        <p><?php echo nl2br(htmlspecialchars($result['impact_social'])); ?></p>
                    </div>
                <?php } ?>

                <div class="meta">
                    <small>Publié le: <?php echo date('d/m/Y H:i', strtotime($result['created_at'])); ?></small>
                </div>
                <div class="actions" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <a href="?controller=education&action=list" class="btn btn-secondary">Retour à la liste</a>
                    <button class="btn-favorite <?php echo isset($isFavorite) && $isFavorite ? 'active' : ''; ?>" 
                            onclick="toggleEducationFavorite(<?php echo $result['id']; ?>, this)" 
                            title="<?php echo isset($isFavorite) && $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                        <span class="favorite-icon"><?php echo isset($isFavorite) && $isFavorite ? '⭐' : '☆'; ?></span>
                        <span class="favorite-text"><?php echo isset($isFavorite) && $isFavorite ? 'Favori' : 'Ajouter aux favoris'; ?></span>
                    </button>
                </div>
            </article>
        <?php } else { ?>
            <div class="error-message">
                <p>Éducation introuvable.</p>
                <div class="actions" style="justify-content: center; margin-top: 20px;">
                    <a href="?controller=education&action=list" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<style>
.btn-favorite {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 12px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
}

.btn-favorite:hover {
    background: rgba(255, 215, 0, 0.2);
    border-color: rgba(255, 215, 0, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
}

.btn-favorite.active {
    background: rgba(255, 215, 0, 0.2);
    border-color: rgba(255, 215, 0, 0.5);
}

.btn-favorite.active .favorite-icon {
    filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.8));
}

.favorite-icon {
    font-size: 20px;
    transition: all 0.3s ease;
}
</style>

<script>
function toggleEducationFavorite(educationId, button) {
    fetch('?controller=favorite&action=toggleEducation&id=' + educationId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const icon = button.querySelector('.favorite-icon');
                const text = button.querySelector('.favorite-text');
                
                if (data.is_favorite) {
                    button.classList.add('active');
                    icon.textContent = '⭐';
                    text.textContent = 'Favori';
                    button.title = 'Retirer des favoris';
                } else {
                    button.classList.remove('active');
                    icon.textContent = '☆';
                    text.textContent = 'Ajouter aux favoris';
                    button.title = 'Ajouter aux favoris';
                }
            } else {
                alert(data.message || "Une erreur est survenue");
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

<?php include "views/front/includes/footer.php"; ?>
