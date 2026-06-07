<?php
require_once __DIR__ . '/../../controllers/CategoryController.php';
require_once __DIR__ . '/../../models/Database.php';

$database = new Database();
$db = $database->getConnection();
$categoryController = new CategoryController($db);

// Traitement identique mais avec interface admin
$games = [];
$selectedCategory = null;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_games'])) {
    $categoryId = $_POST['category_id'] ?? '';
    $errors = $categoryController->validateSearchForm($categoryId);
    
    if (empty($errors)) {
        $categoryId = (int)$categoryId;
        $games = $categoryController->getGamesByCategory($categoryId);
        $selectedCategory = $categoryController->getCategoryById($categoryId);
    }
}

$categories = $categoryController->getAllCategories();
$categoryStats = $categoryController->getCategoryStats();
?>

<div class="admin-header">
    <h1 class="admin-title">🔍 Recherche de Jeux par Catégorie</h1>
    <p class="admin-subtitle">Interface administrateur - Gestion par catégorie</p>
</div>

<!-- Messages d'erreur -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>❌ Erreur :</strong>
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form id="adminSearchForm" method="POST" action="">
        <input type="hidden" name="search_games" value="1">
        
        <div class="search-form-admin" style="display: flex; gap: 20px; align-items: end;">
            <div class="form-group" style="flex: 1;">
                <label>📁 Catégorie</label>
                <select name="category_id" id="adminCategoryId" class="form-control" required>
                    <option value="">Sélectionner une catégorie...</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <?php 
                                $gameCount = 0;
                                $categoryIdValue = $category['id'] ?? null;
                                $categoryName = $category['name'] ?? 'Catégorie sans nom';
                                
                                if ($categoryIdValue) {
                                    foreach ($categoryStats as $stat) {
                                        $statId = $stat['id'] ?? null;
                                        if ($statId && $statId == $categoryIdValue) {
                                            $gameCount = $stat['game_count'] ?? 0;
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <?php if ($categoryIdValue): ?>
                                <option value="<?= $categoryIdValue ?>" 
                                        <?= (isset($_POST['category_id']) && $_POST['category_id'] == $categoryIdValue) ? 'selected' : '' ?>
                                        data-game-count="<?= $gameCount ?>">
                                    <?= htmlspecialchars($categoryName) ?>
                                    <?php if ($gameCount > 0): ?>
                                        (<?= $gameCount ?> jeu<?= $gameCount > 1 ? 'x' : '' ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Aucune catégorie disponible</option>
                    <?php endif; ?>
                </select>
                <div class="category-info" id="categoryInfo" style="margin-top: 8px; font-size: 12px; color: var(--text-dim); display: none;">
                    <span id="selectedCategoryStats"></span>
                </div>
            </div>
            <button type="submit" class="btn-admin btn-add" style="padding: 12px 25px; display: flex; align-items: center; gap: 8px;">
                <span>🎯</span>
                Rechercher
            </button>
        </div>
    </form>

    <!-- Statistiques admin adaptées au style dashboard -->
    <div class="stats-grid" style="margin-top: 30px;">
        <?php 
        $totalGames = 0;
        $maxGames = 0;
        $emptyCategories = 0;
        $totalCategories = count($categories);
        
        foreach ($categoryStats as $stat) {
            $count = $stat['game_count'] ?? 0;
            $totalGames += $count;
            if ($count > $maxGames) {
                $maxGames = $count;
            }
            if ($count == 0) {
                $emptyCategories++;
            }
        }
        
        $averageGames = $totalCategories > 0 ? round($totalGames / $totalCategories, 1) : 0;
        ?>
        <div class="stat-card">
            <div class="stat-icon">📁</div>
            <div class="stat-number" data-target="<?= $totalCategories ?>">0</div>
            <div class="stat-label">Catégories</div>
            <p class="stat-description">Nombre total de catégories disponibles</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎮</div>
            <div class="stat-number" data-target="<?= $totalGames ?>">0</div>
            <div class="stat-label">Jeux Classés</div>
            <p class="stat-description">Total des jeux répartis par catégorie</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-number" data-target="<?= $maxGames ?>">0</div>
            <div class="stat-label">Max par Catégorie</div>
            <p class="stat-description">Plus grande catégorie en nombre de jeux</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div class="stat-number" data-target="<?= $averageGames ?>">0</div>
            <div class="stat-label">Moyenne</div>
            <p class="stat-description">Moyenne de jeux par catégorie</p>
        </div>
    </div>

    <!-- Liste des catégories avec statistiques -->
    <div style="margin-top: 30px;">
        <h4 style="color: var(--accent-cyan); margin-bottom: 20px; font-size: 18px;">📋 Liste des Catégories</h4>
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <?php 
                        $gameCount = 0;
                        $categoryId = $category['id'] ?? null;
                        $categoryName = $category['name'] ?? 'Catégorie sans nom';
                        
                        if ($categoryId) {
                            foreach ($categoryStats as $stat) {
                                $statId = $stat['id'] ?? null;
                                if ($statId && $statId == $categoryId) {
                                    $gameCount = $stat['game_count'] ?? 0;
                                    break;
                                }
                            }
                        }
                    ?>
                    <?php if ($categoryId): ?>
                        <div class="category-card" 
                             onclick="selectCategory(<?= $categoryId ?>)">
                            <div class="category-header">
                                <div class="category-info-main">
                                    <strong class="category-name"><?= htmlspecialchars($categoryName) ?></strong>
                                    <div class="category-id">ID: <?= $categoryId ?></div>
                                </div>
                                <div class="category-stats">
                                    <div class="game-count <?= $gameCount > 0 ? 'has-games' : 'no-games' ?>">
                                        <?= $gameCount ?>
                                    </div>
                                    <div class="game-label">
                                        jeu<?= $gameCount > 1 ? 'x' : '' ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($gameCount > 0): ?>
                                <div class="category-status status-active">
                                    ✅ Contient des jeux publiés
                                </div>
                            <?php else: ?>
                                <div class="category-status status-inactive">
                                    ⚠️ Aucun jeu dans cette catégorie
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-categories" style="grid-column: 1 / -1;">
                    <div class="no-data-icon">📁</div>
                    <h4>Aucune catégorie disponible</h4>
                    <p>Les catégories n'ont pas encore été créées dans la base de données.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Affichage des résultats en mode admin -->
<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_games']) && empty($errors)): ?>
    <?php if (!empty($games)): ?>
        <div class="admin-card">
            <div class="results-header">
                <h3 class="results-title">
                    📊 Résultats pour "<?= htmlspecialchars($selectedCategory['name'] ?? 'Catégorie inconnue') ?>"
                </h3>
                <div class="results-actions">
                    <div class="results-count">
                        <?= count($games) ?> jeu<?= count($games) > 1 ? 'x' : '' ?> trouvé<?= count($games) > 1 ? 's' : '' ?>
                    </div>
                    <button onclick="resetSearch()" class="btn-admin btn-edit">
                        🔄 Nouvelle recherche
                    </button>
                </div>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th style="min-width: 200px;">Nom</th>
                            <th style="width: 120px;">Catégorie</th>
                            <th style="width: 100px;">Statut</th>
                            <th style="min-width: 150px;">Impact Social</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td class="game-id"><?= $game['id'] ?? 'N/A' ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($game['image_url'] ?? '/game-masters/public/assets/img/dev1.jpg') ?>" 
                                         alt="<?= htmlspecialchars($game['name'] ?? 'Jeu sans nom') ?>"
                                         class="game-image-thumb"
                                         onerror="this.src='/game-masters/public/assets/img/dev1.jpg'">
                                </td>
                                <td>
                                    <strong class="game-name"><?= htmlspecialchars($game['name'] ?? 'Jeu sans nom') ?></strong>
                                    <div class="game-description-preview">
                                        <?= htmlspecialchars(substr($game['description'] ?? 'Aucune description disponible', 0, 50)) ?>...
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        <?= htmlspecialchars($game['category_name'] ?? 'Non catégorisé') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $game['status'] ?? 'unknown' ?>">
                                        <?= ($game['status'] ?? 'unknown') === 'published' ? 'Publié' : 
                                              (($game['status'] ?? 'unknown') === 'development' ? 'En Dev' : 'Inconnu') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="impact-social">
                                        <?= htmlspecialchars($game['impact_social'] ?? 'Aucun impact social défini') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="game-actions">
                                        <?php if (isset($game['id'])): ?>
                                            <a href="/game-masters/public/index.php?action=admin_games&edit=<?= $game['id'] ?>" 
                                               class="btn-admin btn-edit btn-small">
                                                ✏️ Modifier
                                            </a>
                                            <a href="/game-masters/public/index.php?action=admin_games&delete_game=1&id=<?= $game['id'] ?>" 
                                               class="btn-admin btn-delete btn-small"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')">
                                                🗑️ Supprimer
                                            </a>
                                        <?php else: ?>
                                            <span class="missing-id">ID manquant</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h3>Aucun jeu trouvé</h3>
                <p class="no-results-message">
                    Aucun jeu n'est disponible dans la catégorie "<?= htmlspecialchars($selectedCategory['name'] ?? 'Catégorie inconnue') ?>" pour le moment.
                </p>
                <div class="no-results-actions">
                    <button onclick="document.getElementById('adminCategoryId').focus()" class="btn-admin btn-edit">
                        🔄 Essayer une autre catégorie
                    </button>
                    <button onclick="resetSearch()" class="btn-admin btn-add">
                        📋 Voir toutes les catégories
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<style>
/* Styles pour la recherche par catégorie */
.search-form-admin {
    display: flex;
    gap: 20px;
    align-items: end;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.category-card {
    background: rgba(42, 42, 42, 0.3);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--metal-dark);
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-card:hover {
    border-color: var(--accent-purple);
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(153, 69, 255, 0.2);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.category-info-main {
    flex: 1;
}

.category-name {
    color: var(--text-primary);
    font-size: 16px;
    display: block;
    margin-bottom: 5px;
}

.category-id {
    font-size: 12px;
    color: var(--text-dim);
}

.category-stats {
    text-align: right;
}

.game-count {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 2px;
}

.game-count.has-games {
    color: var(--accent-green);
}

.game-count.no-games {
    color: var(--text-dim);
}

.game-label {
    font-size: 10px;
    color: var(--text-secondary);
    text-transform: uppercase;
}

.category-status {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 11px;
    text-align: center;
}

.status-active {
    background: rgba(0, 255, 136, 0.1);
    color: var(--accent-green);
    border: 1px solid rgba(0, 255, 136, 0.3);
}

.status-inactive {
    background: rgba(255, 68, 68, 0.1);
    color: var(--accent-red);
    border: 1px solid rgba(255, 68, 68, 0.3);
}

/* Styles pour les résultats */
.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--metal-dark);
}

.results-title {
    color: var(--accent-cyan);
    margin: 0;
    font-size: 20px;
}

.results-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.results-count {
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 600;
}

/* Styles pour le tableau des résultats */
.game-id {
    color: var(--text-dim);
    font-size: 12px;
}

.game-image-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--metal-dark);
    transition: all 0.3s ease;
}

.game-image-thumb:hover {
    border-color: var(--accent-purple);
    transform: scale(1.05);
}

.game-name {
    color: var(--text-primary);
    font-size: 14px;
    display: block;
    margin-bottom: 4px;
}

.game-description-preview {
    font-size: 11px;
    color: var(--text-dim);
    line-height: 1.3;
}

.category-badge {
    background: rgba(153, 69, 255, 0.2);
    color: var(--accent-purple);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid var(--accent-purple);
}

.impact-social {
    font-size: 12px;
    color: var(--text-secondary);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.game-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-small {
    padding: 6px 10px;
    font-size: 10px;
    white-space: nowrap;
}

.missing-id {
    color: var(--text-dim);
    font-size: 10px;
}

/* Styles pour aucun résultat */
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-dim);
}

.no-results-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-results h3 {
    color: var(--text-secondary);
    margin-bottom: 15px;
    font-size: 20px;
}

.no-results-message {
    margin-bottom: 25px;
    font-size: 14px;
    color: var(--text-dim);
}

.no-results-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

/* Styles pour aucune catégorie */
.no-categories {
    text-align: center;
    padding: 40px;
    color: var(--text-dim);
}

.no-data-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.no-categories h4 {
    color: var(--text-secondary);
    margin-bottom: 10px;
}
</style>

<script>
function resetSearch() {
    document.getElementById('adminSearchForm').reset();
    window.location.href = 'index.php?action=admin_category_search';
}

function selectCategory(categoryId) {
    document.getElementById('adminCategoryId').value = categoryId;
    document.getElementById('adminSearchForm').submit();
}

// Animation des compteurs
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        if (isNaN(target)) return;
        
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(updateCounter);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });

    // Animation des cartes de catégories
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });
});
</script>