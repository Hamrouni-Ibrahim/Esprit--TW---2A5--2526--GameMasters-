<?php 
$pageTitle = 'Éducations - Game Master';
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
        <h2 class="section-title">Liste des Éducations</h2>
        
        <!-- Search Form -->
        <div style="max-width: 600px; margin: 30px auto; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.2); border-radius: 15px; padding: 25px;">
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="controller" value="education">
                <input type="hidden" name="action" value="list">
                <input type="text" name="search" 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                       placeholder="Rechercher une éducation..." 
                       style="flex: 1; padding: 12px 20px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 10px; color: #fff; font-size: 14px;"
                       onfocus="this.style.borderColor='#e879f9'; this.style.background='rgba(255, 255, 255, 0.08)';"
                       onblur="this.style.borderColor='rgba(232, 121, 249, 0.3)'; this.style.background='rgba(255, 255, 255, 0.05)';">
                <button type="submit" style="padding: 12px 25px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s;" 
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(147, 51, 234, 0.4)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    🔍 Rechercher
                </button>
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="?controller=education&action=list" style="padding: 12px 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600;">
                        ✕ Effacer
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <p style="text-align: center; color: #e879f9; margin-bottom: 20px;">
                Résultats de recherche pour: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong>
            </p>
        <?php endif; ?>
        
        <?php if (!empty($results) && count($results) > 0) { ?>
            <div class="items-grid">
                <?php foreach($results as $row) { ?>
                    <div class="item-card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="card-meta">
                            <?php if (!empty($row['difficulte'])) { ?>
                                <span class="badge"><?php echo htmlspecialchars($row['difficulte']); ?></span>
                            <?php } ?>
                            <?php if (!empty($row['duree'])) { ?>
                                <span class="badge"><?php echo htmlspecialchars($row['duree']); ?>h</span>
                            <?php } ?>
                            <?php if (!empty($row['categorie'])) { ?>
                                <span class="badge"><?php echo htmlspecialchars($row['categorie']); ?></span>
                            <?php } ?>
                        </div>
                        <p><?php echo htmlspecialchars(substr($row['description'], 0, 150)) . '...'; ?></p>
                        <a href="?controller=education&action=detail&id=<?php echo $row['id']; ?>" class="btn btn-primary">Voir plus</a>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="no-items">
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    Aucune éducation trouvée pour "<?php echo htmlspecialchars($_GET['search']); ?>".
                <?php else: ?>
                    Aucune éducation disponible pour le moment.
                <?php endif; ?>
            </p>
        <?php } ?>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>
