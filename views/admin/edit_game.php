<?php
// Vérifier que l'admin est connecté
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?action=login');
    exit;
}
?>
<style>
    .edit-game-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 40px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }
    
    .admin-header {
        margin-bottom: 30px;
    }
    
    .admin-title {
        font-size: 32px;
        font-weight: 700;
        color: #e879f9;
        margin-bottom: 10px;
    }
    
    .admin-subtitle {
        font-size: 14px;
        color: #a0a0a0;
        margin: 0;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        color: #fff;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 1px;
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 15px;
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #9333ea;
        box-shadow: 0 0 15px rgba(153, 69, 255, 0.2);
    }
    
    .form-control.valid {
        border-color: #00ff88;
        box-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
    }
    
    .form-control.invalid {
        border-color: #ff4444;
        box-shadow: 0 0 10px rgba(255, 68, 68, 0.2);
    }
    
    .validation-feedback {
        margin-top: 8px;
        font-size: 12px;
        display: block;
    }
    
    .validation-feedback.success {
        color: #00ff88;
    }
    
    .validation-feedback.error {
        color: #ff4444;
    }
    
    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #9333ea, #c084fc);
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(153, 69, 255, 0.3);
    }
    
    .btn-cancel {
        display: inline-block;
        padding: 12px 30px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .alert {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .alert-error {
        background: rgba(255, 68, 68, 0.1);
        color: #ff4444;
        border: 1px solid rgba(255, 68, 68, 0.3);
    }
    
    .current-image {
        margin-top: 10px;
        max-width: 200px;
        border-radius: 8px;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
</style>

<section class="admin-content">
    <div class="admin-container">
        <div class="edit-game-container">
            <div class="admin-header">
                <h1 class="admin-title">✏️ Modifier le Jeu</h1>
                <p class="admin-subtitle">Modifiez les informations du jeu</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="?action=admin_edit_game" enctype="multipart/form-data">
                <input type="hidden" name="update_game" value="1">
                <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['id'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom du jeu *</label>
                    <input type="text" id="gameName" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($game['name'] ?? ''); ?>"
                           placeholder="Entrez le nom du jeu" minlength="2" maxlength="100">
                    <div class="validation-feedback" id="nameFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label>Impact social *</label>
                    <input type="text" id="impactSocial" name="impact_social" class="form-control" required 
                           value="<?php echo htmlspecialchars($game['impact_social'] ?? ''); ?>"
                           placeholder="Ex: Santé mentale, Écologie, Éducation..." minlength="5" maxlength="500">
                    <div class="validation-feedback" id="impactFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="6" required 
                              placeholder="Décrivez votre jeu..." minlength="10" maxlength="2000"><?php echo htmlspecialchars($game['description'] ?? ''); ?></textarea>
                    <div class="validation-feedback" id="descriptionFeedback"></div>
                    <small style="color: #a0a0a0; font-size: 11px; display: block; margin-top: 5px;">
                        <span id="charCount"><?php echo strlen($game['description'] ?? ''); ?></span> / 2000 caractères
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="category_id" class="form-control">
                        <option value="">Aucune catégorie</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($game['category_id']) && $game['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Statut *</label>
                    <select name="status" class="form-control" required>
                        <option value="development" <?php echo (isset($game['status']) && $game['status'] === 'development') ? 'selected' : ''; ?>>En développement</option>
                        <option value="published" <?php echo (isset($game['status']) && $game['status'] === 'published') ? 'selected' : ''; ?>>Publié</option>
                        <option value="archived" <?php echo (isset($game['status']) && $game['status'] === 'archived') ? 'selected' : ''; ?>>Archivé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Image du jeu *</label>
                    <?php if(!empty($game['image_url'])): ?>
                        <div style="margin-bottom: 15px;">
                            <p style="color: #a0a0a0; font-size: 12px; margin-bottom: 10px;">Image actuelle:</p>
                            <img src="<?php echo htmlspecialchars($game['image_url']); ?>" 
                                 alt="Image actuelle" 
                                 class="current-image"
                                 onerror="this.style.display='none'">
                        </div>
                    <?php endif; ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <label style="font-size: 12px; color: #a0a0a0; margin-bottom: 8px; display: block;">
                                📤 Uploader une nouvelle image
                            </label>
                            <input type="file" id="imageFile" name="image" class="form-control" 
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div style="text-align: center; color: #a0a0a0; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                            OU
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #a0a0a0; margin-bottom: 8px; display: block;">
                                🔗 URL externe
                            </label>
                            <input type="url" id="imageUrl" name="image_url" class="form-control" 
                                   value="<?php echo htmlspecialchars($game['image_url'] ?? ''); ?>"
                                   placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                    <div class="validation-feedback" id="imageFeedback"></div>
                    <small style="color: #a0a0a0; font-size: 11px;">
                        Formats acceptés: JPG, PNG, GIF, WebP (max 5MB). Laissez vide pour conserver l'image actuelle.
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Vidéo de démonstration (optionnel)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <label style="font-size: 12px; color: #a0a0a0; margin-bottom: 8px; display: block;">
                                📤 Uploader une nouvelle vidéo
                            </label>
                            <input type="file" name="video" class="form-control" 
                                   accept="video/mp4,video/webm,video/ogg">
                        </div>
                        <div style="text-align: center; color: #a0a0a0; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                            OU
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #a0a0a0; margin-bottom: 8px; display: block;">
                                🔗 URL YouTube/Vidéo
                            </label>
                            <input type="url" name="demo_url" class="form-control" 
                                   value="<?php echo htmlspecialchars($game['demo_url'] ?? ''); ?>"
                                   placeholder="https://youtube.com/embed/...">
                        </div>
                    </div>
                    <small style="color: #a0a0a0; font-size: 11px;">
                        Formats acceptés: MP4, WebM, OGG (max 50MB) ou URL YouTube
                    </small>
                </div>
                
                <button type="submit" id="submitBtn" class="btn-submit">
                    💾 Enregistrer les Modifications
                </button>
                
                <div style="text-align: center;">
                    <a href="?action=admin_games" class="btn-cancel">
                        ← Retour à la liste
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Validation du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('gameName');
    const impactInput = document.getElementById('impactSocial');
    const descriptionInput = document.getElementById('description');
    const imageFile = document.getElementById('imageFile');
    const imageUrl = document.getElementById('imageUrl');
    const charCount = document.getElementById('charCount');
    
    // Compteur de caractères
    descriptionInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    
    // Validation basique
    function validateForm() {
        let isValid = true;
        
        if (nameInput.value.trim().length < 2) {
            isValid = false;
        }
        
        if (impactInput.value.trim().length < 5) {
            isValid = false;
        }
        
        if (descriptionInput.value.trim().length < 10) {
            isValid = false;
        }
        
        // Image: soit fichier, soit URL, soit déjà existante
        const hasFile = imageFile.files.length > 0;
        const hasUrl = imageUrl.value.trim().length > 0;
        const hasCurrentImage = <?php echo !empty($game['image_url']) ? 'true' : 'false'; ?>;
        
        if (!hasFile && !hasUrl && !hasCurrentImage) {
            isValid = false;
        }
        
        return isValid;
    }
    
    // Validation avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs requis.');
        }
    });
});
</script>


