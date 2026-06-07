<?php 
$pageTitle = 'Mes Favoris - Game Master';
$currentPage = 'favorites';
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
        <h2 class="section-title">Mes Favoris</h2>
        
        <?php if (count($favoriteFormations) > 0 || count($favoriteEducations) > 0) { ?>
            
            <?php if (count($favoriteFormations) > 0) { ?>
                <h3 style="color: #00ffcc; margin-top: 40px; margin-bottom: 20px; font-size: 24px; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);">
                     Formations Favorites (<?php echo count($favoriteFormations); ?>)
                </h3>
                <div class="items-grid">
                    <?php foreach ($favoriteFormations as $formation) { ?>
                        <div class="item-card">
                            <div class="content-type-badge">
                                🎓 Formation
                            </div>
                            <h3><?php echo htmlspecialchars($formation['title']); ?></h3>
                            <div class="card-meta">
                                <?php if (!empty($formation['category_name'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($formation['category_name']); ?></span>
                                <?php } ?>
                                <?php if (!empty($formation['difficulte'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($formation['difficulte']); ?></span>
                                <?php } ?>
                                <?php if (!empty($formation['duree'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($formation['duree']); ?>h</span>
                                <?php } ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($formation['description'], 0, 150)) . '...'; ?></p>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="?controller=formation&action=detail&id=<?php echo $formation['id']; ?>" class="btn btn-primary" style="flex: 1;">Voir plus</a>
                                <button class="btn-favorite active" onclick="toggleFormationFavorite(<?php echo $formation['id']; ?>, this)" title="Retirer des favoris">
                                    <span class="favorite-icon">⭐</span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (count($favoriteEducations) > 0) { ?>
                <h3 style="color: #ff8e53; margin-top: 40px; margin-bottom: 20px; font-size: 24px; text-shadow: 0 0 20px rgba(255, 142, 83, 0.5);">
                     Éducations Favorites (<?php echo count($favoriteEducations); ?>)
                </h3>
                <div class="items-grid">
                    <?php foreach ($favoriteEducations as $education) { ?>
                        <div class="item-card">
                            <div class="content-type-badge">
                                📚 Éducation
                            </div>
                            <h3><?php echo htmlspecialchars($education['title']); ?></h3>
                            <div class="card-meta">
                                <?php if (!empty($education['category_name'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($education['category_name']); ?></span>
                                <?php } ?>
                                <?php if (!empty($education['difficulte'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($education['difficulte']); ?></span>
                                <?php } ?>
                                <?php if (!empty($education['duree'])) { ?>
                                    <span class="badge"><?php echo htmlspecialchars($education['duree']); ?>h</span>
                                <?php } ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($education['description'], 0, 150)) . '...'; ?></p>
                            <?php if (!empty($education['formation_title'])) { ?>
                                <p style="color: #00ffcc; font-size: 12px; margin-top: 10px;">
                                    Partie de: <?php echo htmlspecialchars($education['formation_title']); ?>
                                </p>
                            <?php } ?>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="?controller=education&action=detail&id=<?php echo $education['id']; ?>" class="btn btn-primary" style="flex: 1;">Voir plus</a>
                                <button class="btn-favorite active" onclick="toggleEducationFavorite(<?php echo $education['id']; ?>, this)" title="Retirer des favoris">
                                    <span class="favorite-icon">⭐</span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div style="text-align: center; padding: 60px 20px; color: rgba(255, 255, 255, 0.7);">
                <div style="font-size: 64px; margin-bottom: 20px;">⭐</div>
                <h3 style="color: #ffffff; margin-bottom: 15px;">Aucun favori pour le moment</h3>
                <p style="margin-bottom: 30px; line-height: 1.8;">
                    Commencez à explorer les formations et éducations disponibles et ajoutez-les à vos favoris pour y accéder facilement plus tard.
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="?controller=formation&action=list" class="btn btn-primary">Voir les formations</a>
                    <a href="?controller=education&action=list" class="btn btn-primary">Voir les éducations</a>
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
    padding: 10px 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-favorite:hover {
    background: rgba(255, 215, 0, 0.2);
    border-color: rgba(255, 215, 0, 0.5);
    transform: scale(1.1);
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
function toggleFormationFavorite(formationId, button) {
    fetch('?controller=favorite&action=toggleFormation&id=' + formationId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                    // Reload page to update the list
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } else {
                alert(data.message || "Une erreur est survenue");
            }
        })
        .catch(error => console.error('Error:', error));
}

function toggleEducationFavorite(educationId, button) {
    fetch('?controller=favorite&action=toggleEducation&id=' + educationId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                    // Reload page to update the list
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } else {
                alert(data.message || "Une erreur est survenue");
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

<?php include "views/front/includes/footer.php"; ?>

