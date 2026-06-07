<?php 
$pageTitle = (isset($result['title']) ? htmlspecialchars($result['title']) : 'Formation') . ' - Game Master';
$currentPage = 'formations';
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

                <div class="description">
                    <p><?php echo nl2br(htmlspecialchars($result['description'])); ?></p>
                </div>

                <?php if (!empty($result['competences'])) { ?>
                    <div class="info-section">
                        <h3>Compétences enseignées</h3>
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

                <!-- Skill Tree Section -->
                <?php include "views/front/skill_tree.php"; ?>

                <?php if (isset($relatedEducations) && count($relatedEducations) > 0) { ?>
                    <div class="info-section" style="background: rgba(0, 255, 204, 0.05); border-left-color: #00ffcc; margin-top: 30px;">
                        <h3 style="color: #00ffcc;">📖 Éducations de cette Formation</h3>
                        <p style="margin-bottom: 20px; color: #a0a0a0;">
                            Cette formation comprend <?php echo count($relatedEducations); ?> éducation(s):
                        </p>
                        <div class="educations-list" style="display: grid; gap: 15px;">
                            <?php foreach($relatedEducations as $education) { ?>
                                <div class="education-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 10px; padding: 20px; transition: all 0.3s ease; cursor: pointer;" 
                                     onmouseover="this.style.background='rgba(0, 255, 204, 0.08)'; this.style.borderColor='rgba(0, 255, 204, 0.4)'; this.style.transform='translateY(-2px)';" 
                                     onmouseout="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(0, 255, 204, 0.2)'; this.style.transform='translateY(0)';">
                                    <h4 style="margin: 0 0 10px 0; color: #ffffff;">
                                        <a href="?controller=education&action=detail&id=<?php echo $education['id']; ?>" 
                                           style="color: #00ffcc; text-decoration: none;">
                                            <?php echo htmlspecialchars($education['title']); ?>
                                        </a>
                                    </h4>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                                        <?php if (!empty($education['difficulte'])) { ?>
                                            <span class="badge"><?php echo htmlspecialchars($education['difficulte']); ?></span>
                                        <?php } ?>
                                        <?php if (!empty($education['duree'])) { ?>
                                            <span class="badge"><?php echo htmlspecialchars($education['duree']); ?>h</span>
                                        <?php } ?>
                                    </div>
                                    <?php if (!empty($education['description'])) { ?>
                                        <p style="color: #a0a0a0; font-size: 14px; margin: 10px 0; line-height: 1.6;">
                                            <?php echo htmlspecialchars(substr($education['description'], 0, 150)); ?>
                                            <?php echo strlen($education['description']) > 150 ? '...' : ''; ?>
                                        </p>
                                    <?php } ?>
                                    <a href="?controller=education&action=detail&id=<?php echo $education['id']; ?>" 
                                       class="btn btn-primary" style="margin-top: 10px; display: inline-block;">
                                        Voir les détails
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="info-section" style="background: rgba(255, 255, 255, 0.02); border-left-color: #707070;">
                        <h3 style="color: #a0a0a0;">📖 Éducations</h3>
                        <p style="color: #707070;">Aucune éducation n'est encore associée à cette formation.</p>
                    </div>
                <?php } ?>

                <!-- AI Recommendations Section -->
                <?php if (isset($recommendations) && count($recommendations) > 0) { ?>
                    <div class="info-section" style="background: linear-gradient(135deg, rgba(100, 100, 255, 0.1), rgba(200, 100, 255, 0.05)); border-left-color: #a066ff; margin-top: 30px;">
                        <h3 style="color: #a066ff;">✨ Recommandé pour vous (IA)</h3>
                        <p style="margin-bottom: 20px; color: #e0e0e0;">
                            Basé sur le contenu de cette formation, nous pensons que vous aimerez aussi :
                        </p>
                        <div class="recommendations-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                            <?php foreach($recommendations as $rec) { 
                                $isEducation = isset($rec['type']) && $rec['type'] === 'education';
                                $link = $isEducation 
                                    ? "?controller=education&action=detail&id=" . $rec['id'] 
                                    : "?controller=formation&action=detail&id=" . $rec['id'];
                                $badgeColor = $isEducation ? '#ff9900' : '#a066ff';
                                $badgeText = $isEducation ? 'Éducation' : 'Formation';
                            ?>
                                <div class="recommendation-card" style="background: rgba(0, 0, 0, 0.2); border: 1px solid <?php echo $badgeColor; ?>; border-radius: 10px; padding: 15px; transition: all 0.3s ease;">
                                    <div style="margin-bottom: 8px;">
                                        <span style="background: <?php echo $badgeColor; ?>; color: #fff; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: bold;">
                                            <?php echo $badgeText; ?>
                                        </span>
                                    </div>
                                    <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;">
                                        <a href="<?php echo $link; ?>" style="color: #fff; text-decoration: none;">
                                            <?php echo htmlspecialchars($rec['title']); ?>
                                        </a>
                                    </h4>
                                    <div style="font-size: 0.8rem; color: #aaa; margin-bottom: 10px;">
                                        <?php if (!empty($rec['categorie'])) { ?>
                                            <span style="background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($rec['categorie']); ?></span>
                                        <?php } ?>
                                        <?php if (isset($rec['recommendation_score'])) { 
                                            $score = $rec['recommendation_score'];
                                            $percentage = min(100, max(0, $score)); // Ensure it's between 0-100
                                        ?>
                                            <span style="color: <?php echo $badgeColor; ?>; float: right; font-weight: bold;">
                                                ✓ <?php echo $percentage; ?>% de correspondance
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <a href="<?php echo $link; ?>" class="btn btn-sm" style="background: rgba(255, 255, 255, 0.1); color: #fff; width: 100%; text-align: center; display: block; padding: 8px 0; border-radius: 5px; text-decoration: none;">
                                        Voir
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="meta">
                    <small>Publié le: <?php echo date('d/m/Y H:i', strtotime($result['created_at'])); ?></small>
                </div>
                <div class="actions" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <a href="?controller=formation&action=list" class="btn btn-secondary">Retour à la liste</a>
                    <button class="btn-favorite <?php echo isset($isFavorite) && $isFavorite ? 'active' : ''; ?>" 
                            onclick="toggleFormationFavorite(<?php echo $result['id']; ?>, this)" 
                            title="<?php echo isset($isFavorite) && $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                        <span class="favorite-icon"><?php echo isset($isFavorite) && $isFavorite ? '⭐' : '☆'; ?></span>
                        <span class="favorite-text"><?php echo isset($isFavorite) && $isFavorite ? 'Favori' : 'Ajouter aux favoris'; ?></span>
                    </button>
                </div>
            </article>
        <?php } else { ?>
            <div class="error-message">
                <p>Formation introuvable.</p>
                <div class="actions" style="justify-content: center; margin-top: 20px;">
                    <a href="?controller=formation&action=list" class="btn btn-secondary">Retour à la liste</a>
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
function toggleFormationFavorite(formationId, button) {
    fetch('?controller=favorite&action=toggleFormation&id=' + formationId)
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
