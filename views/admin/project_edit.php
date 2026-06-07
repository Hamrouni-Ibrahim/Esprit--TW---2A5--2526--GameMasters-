<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Beautiful Edit Project Buttons */
    .btn-project-update {
        flex: 1;
        padding: 16px 30px;
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 50%, #93c5fd 100%);
        background-size: 200% 200%;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 4px 15px rgba(59, 130, 246, 0.4),
            0 0 0 1px rgba(96, 165, 250, 0.2) inset;
        text-transform: uppercase;
        animation: gradientShift 3s ease infinite;
    }
    
    .btn-project-update::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-project-update:hover::before {
        left: 100%;
    }
    
    .btn-project-update:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 25px rgba(59, 130, 246, 0.6),
            0 0 0 1px rgba(96, 165, 250, 0.4) inset,
            0 0 30px rgba(59, 130, 246, 0.4);
        background-position: right center;
    }
    
    .btn-project-cancel {
        flex: 1;
        padding: 16px 30px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        color: #e879f9;
        border: 2px solid rgba(232, 121, 249, 0.4);
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        text-align: center;
        display: inline-block;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(232, 121, 249, 0.2);
    }
    
    .btn-project-cancel::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(232, 121, 249, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-project-cancel:hover::before {
        left: 100%;
    }
    
    .btn-project-cancel:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, rgba(232, 121, 249, 0.2) 0%, rgba(192, 132, 252, 0.15) 100%);
        border-color: rgba(232, 121, 249, 0.7);
        box-shadow: 
            0 8px 25px rgba(232, 121, 249, 0.4),
            0 0 30px rgba(232, 121, 249, 0.3);
        color: #f0abfc;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
</style>

<section class="admin-content">
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>✏️ Modifier le Projet</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Modifier les informations du projet</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['admin_success'])): ?>
            <div style="background: rgba(76, 175, 80, 0.2); border: 1px solid #4CAF50; color: #4CAF50; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['admin_success']) ?>
            </div>
            <?php unset($_SESSION['admin_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_error'])): ?>
            <div style="background: rgba(255, 77, 77, 0.2); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['admin_error']) ?>
            </div>
            <?php unset($_SESSION['admin_error']); ?>
        <?php endif; ?>

        <div class="section-card">
            <form method="POST" action="?action=admin_project_edit&id=<?= $project['id'] ?>" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
                
                <div class="admin-form-group">
                    <label for="project_title">Titre *</label>
                    <input type="text" id="project_title" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
                </div>
                
                <div class="admin-form-group">
                    <label for="project_category">Catégorie *</label>
                    <input type="text" id="project_category" name="category" value="<?= htmlspecialchars($project['category']) ?>" required>
                </div>
                
                <div class="admin-form-group">
                    <label for="project_image">Image</label>
                    <?php if (!empty($project['image'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= htmlspecialchars($project['image']) ?>" alt="Image actuelle" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);">
                            <p style="color: #a0a0a0; font-size: 0.85em; margin-top: 5px;">Image actuelle</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="project_image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: #a0a0a0; font-size: 0.85em; display: block; margin-top: 5px;">
                        Laisser vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, GIF, WebP (max 5MB)
                    </small>
                </div>
                
                <div class="admin-form-group">
                    <label for="project_description">Description *</label>
                    <textarea id="project_description" name="description" rows="8" required><?= htmlspecialchars($project['description']) ?></textarea>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn-project-update">
                        <span style="margin-right: 8px;">💾</span>
                        Mettre à jour
                    </button>
                    <a href="?action=admin_projects" class="btn-project-cancel">
                        <span style="margin-right: 8px;">↩</span>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

