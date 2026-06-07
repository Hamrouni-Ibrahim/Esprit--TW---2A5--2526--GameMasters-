<?php
// Ce fichier est inclus dans le header/footer, pas besoin de structure HTML complète
?>

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
        <div class="admin-header-section">
            <h2>Gestion des Catégories Jeux</h2>
            <button onclick="document.getElementById('addCategoryForm').style.display='block'" class="btn btn-primary">Ajouter une Catégorie</button>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; color: #00ff88; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid #ff6b6b; color: #ff6b6b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ❌ <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div id="addCategoryForm" style="display: <?php echo $editCategory ? 'block' : 'none'; ?>; margin-bottom: 30px;">
            <div class="admin-card">
                <h3><?php echo $editCategory ? 'Modifier' : 'Ajouter'; ?> une Catégorie</h3>
                <form method="POST" action="?action=admin_game_categories">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
                    <?php endif; ?>
                    <div class="admin-form-group">
                        <label for="category_name">Nom de la catégorie *</label>
                        <input type="text" 
                               id="category_name" 
                               name="name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>" 
                               required 
                               placeholder="Ex: Action, Aventure, Puzzle...">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $editCategory ? 'update_category' : 'create_category'; ?>" class="btn btn-primary">
                            <?php echo $editCategory ? 'Mettre à jour' : 'Créer'; ?>
                        </button>
                        <a href="?action=admin_game_categories" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories List -->
        <div class="admin-card">
            <h3>Liste des Catégories Jeux</h3>
            <?php if (!empty($gameCategories)): ?>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Nombre de jeux</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gameCategories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                    <td>
                                        <span style="background: rgba(153, 69, 255, 0.2); color: #e879f9; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                            <?php echo $category['game_count']; ?> jeu<?php echo $category['game_count'] > 1 ? 'x' : ''; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></td>
                                    <td class="actions-cell">
                                        <a href="?action=admin_game_categories&edit=<?php echo $category['id']; ?>" class="btn btn-edit">Modifier</a>
                                        <a href="?action=admin_game_categories&delete=<?php echo $category['id']; ?>" 
                                           class="btn btn-delete"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-items">Aucune catégorie disponible. <button onclick="document.getElementById('addCategoryForm').style.display='block'" class="btn btn-primary">Ajoutez-en une</button></p>
            <?php endif; ?>
        </div>
    </div>
</section>


