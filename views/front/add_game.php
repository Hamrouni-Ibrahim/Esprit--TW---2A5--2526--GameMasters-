<?php
// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ?action=login');
    exit;
}

// Tous les utilisateurs connectés peuvent maintenant soumettre des jeux
// L'admin approuvera ou refusera les soumissions
$noPermission = false;

// Note: Header is already included by index.php, so we don't include it again here
?>
    <style>
        /* High priority styles - must override admin header styles */
        .add-game-container * {
            box-sizing: border-box;
        }
        
        .add-game-container {
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            padding: 40px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 1px solid rgba(232, 121, 249, 0.25);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(232, 121, 249, 0.1) inset;
            position: relative;
            z-index: 1;
        }
        
        /* Ensure the form is centered in admin content area */
        .admin-content {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 40px 50px 80px !important;
        }
        
        .admin-content .add-game-container {
            margin-left: auto !important;
            margin-right: auto !important;
            display: block !important;
            width: 100% !important;
            max-width: 1000px !important;
            flex-shrink: 0;
        }
        
        /* For admin layout, ensure proper centering */
        body.admin .add-game-container,
        .admin-wrapper .add-game-container,
        .admin-content .add-game-container {
            margin: 0 auto !important;
            display: block !important;
        }
        
        .admin-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .admin-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #e879f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .admin-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
        }
        
        .no-permission {
            text-align: center;
            padding: 60px 40px;
            background: rgba(255, 68, 68, 0.1);
            border: 2px solid var(--accent-red);
            border-radius: 15px;
        }
        
        .no-permission-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .no-permission h2 {
            color: var(--accent-red);
            margin-bottom: 15px;
        }
        
        .no-permission p {
            color: var(--text-secondary);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e879f9;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            border: 1.5px solid rgba(232, 121, 249, 0.3) !important;
            border-radius: 12px;
            color: #ffffff !important;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2),
                        0 0 0 1px rgba(232, 121, 249, 0.1) inset;
        }
        
        .form-control:not(.invalid):not(:focus):not(:hover) {
            border-color: rgba(232, 121, 249, 0.3) !important;
        }
        
        /* Force purple borders - override any red default styles */
        .add-game-container .form-group input,
        .add-game-container .form-group textarea,
        .add-game-container .form-group select,
        .add-game-container input[type="text"],
        .add-game-container input[type="url"],
        .add-game-container input[type="file"],
        .add-game-container textarea,
        .add-game-container select {
            border: 1.5px solid rgba(232, 121, 249, 0.3) !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            color: #ffffff !important;
        }
        
        .add-game-container .form-group input:focus,
        .add-game-container .form-group textarea:focus,
        .add-game-container .form-group select:focus,
        .add-game-container input[type="text"]:focus,
        .add-game-container input[type="url"]:focus,
        .add-game-container textarea:focus,
        .add-game-container select:focus {
            border-color: rgba(232, 121, 249, 0.6) !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.08) 100%) !important;
            box-shadow: 0 4px 20px rgba(232, 121, 249, 0.3),
                        0 0 0 1px rgba(232, 121, 249, 0.2) inset !important;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-control:hover:not(:focus) {
            border-color: rgba(232, 121, 249, 0.4);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%);
        }
        
        /* Styles spécifiques pour les selects */
        select.form-control {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            background-color: transparent !important;
            color: #ffffff !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23e879f9' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 15px center !important;
            background-size: 16px !important;
            padding-right: 45px;
            cursor: pointer;
        }
        
        select.form-control:hover {
            border-color: rgba(232, 121, 249, 0.4);
        }
        
        select.form-control:focus {
            border-color: rgba(232, 121, 249, 0.6);
        }
        
        /* Styles pour les options du select */
        select.form-control option {
            background: rgba(26, 10, 46, 0.98) !important;
            background-color: rgba(26, 10, 46, 0.98) !important;
            color: #ffffff !important;
            padding: 12px;
        }
        
        /* Style pour l'option sélectionnée */
        select.form-control option:checked,
        select.form-control option:focus {
            background: rgba(232, 121, 249, 0.3) !important;
            background-color: rgba(232, 121, 249, 0.3) !important;
            color: #ffffff !important;
        }
        
        /* Style pour les options au survol */
        select.form-control option:hover {
            background: rgba(232, 121, 249, 0.2) !important;
            background-color: rgba(232, 121, 249, 0.2) !important;
        }
        
        /* Pour les navigateurs qui supportent ::-webkit-scrollbar dans les selects */
        select.form-control::-webkit-scrollbar {
            width: 8px;
        }
        
        select.form-control::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }
        
        select.form-control::-webkit-scrollbar-thumb {
            background: rgba(0, 209, 255, 0.5);
            border-radius: 4px;
        }
        
        select.form-control::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 209, 255, 0.8);
        }
        
        .form-control:focus {
            outline: none;
            border-color: rgba(232, 121, 249, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.08) 100%);
            box-shadow: 0 4px 20px rgba(232, 121, 249, 0.3),
                        0 0 0 1px rgba(232, 121, 249, 0.2) inset;
            transform: translateY(-1px);
        }
        
        .form-control.valid {
            border-color: rgba(0, 255, 136, 0.5);
            box-shadow: 0 2px 15px rgba(0, 255, 136, 0.2),
                        0 0 0 1px rgba(0, 255, 136, 0.1) inset;
        }
        
        .form-control.invalid {
            border-color: rgba(239, 68, 68, 0.6) !important;
            box-shadow: 0 2px 15px rgba(239, 68, 68, 0.2),
                        0 0 0 1px rgba(239, 68, 68, 0.1) inset;
        }
        
        /* CRITICAL OVERRIDE: Force purple borders - override ALL admin header styles */
        .add-game-container input,
        .add-game-container textarea,
        .add-game-container select,
        .add-game-container input.form-control,
        .add-game-container textarea.form-control,
        .add-game-container select.form-control,
        .add-game-container .form-control,
        .add-game-container input[type="text"],
        .add-game-container input[type="url"],
        .add-game-container input[type="file"] {
            border: 1.5px solid rgba(232, 121, 249, 0.3) !important;
            border-color: rgba(232, 121, 249, 0.3) !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            background-color: transparent !important;
            color: #ffffff !important;
            outline: none !important;
        }
        
        .add-game-container input:focus,
        .add-game-container textarea:focus,
        .add-game-container select:focus,
        .add-game-container input.form-control:focus,
        .add-game-container textarea.form-control:focus,
        .add-game-container select.form-control:focus,
        .add-game-container .form-control:focus {
            border: 1.5px solid rgba(232, 121, 249, 0.6) !important;
            border-color: rgba(232, 121, 249, 0.6) !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.08) 100%) !important;
            box-shadow: 0 4px 20px rgba(232, 121, 249, 0.3),
                        0 0 0 1px rgba(232, 121, 249, 0.2) inset !important;
            outline: none !important;
        }
        
        .add-game-container input:hover:not(:focus),
        .add-game-container textarea:hover:not(:focus),
        .add-game-container select:hover:not(:focus),
        .add-game-container input.form-control:hover:not(:focus),
        .add-game-container textarea.form-control:hover:not(:focus),
        .add-game-container select.form-control:hover:not(:focus),
        .add-game-container .form-control:hover:not(:focus) {
            border: 1.5px solid rgba(232, 121, 249, 0.4) !important;
            border-color: rgba(232, 121, 249, 0.4) !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%) !important;
        }
        
        /* Ensure select dropdown arrow is visible and purple */
        .add-game-container select,
        .add-game-container select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23e879f9' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 15px center !important;
            background-size: 16px !important;
            padding-right: 45px !important;
        }
        
        .validation-feedback {
            margin-top: 8px;
            font-size: 12px;
            display: block;
        }
        
        .validation-feedback.success {
            color: var(--accent-green);
        }
        
        .validation-feedback.error {
            color: var(--accent-red);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            color: var(--accent-green);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        
        .alert-error {
            background: rgba(255, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        
        .btn-submit {
            width: 100%;
            padding: 15px 25px;
            background: linear-gradient(135deg, #9333ea 0%, #6366f1 50%, #3b82f6 100%);
            border: 1px solid rgba(147, 51, 234, 0.3);
            border-radius: 12px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(147, 51, 234, 0.4), 
                        0 0 0 1px rgba(147, 51, 234, 0.1) inset;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #a855f7 0%, #818cf8 50%, #60a5fa 100%);
            box-shadow: 0 6px 30px rgba(147, 51, 234, 0.5),
                        0 0 0 1px rgba(147, 51, 234, 0.2) inset;
            transform: translateY(-2px);
        }
        
        .btn-submit:active {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(153, 69, 255, 0.3);
        }
        
        .info-box {
            background: rgba(0, 209, 255, 0.1);
            border: 1px solid var(--accent-blue);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .info-box p {
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
        }
    </style>

<?php 
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<?php if(!$isAdmin): ?>
<!-- Content Section for Add Game (Frontend) -->
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
        <h2 class="section-title">Ajouter un Jeu</h2>
        
        <div class="add-game-container">
<?php else: ?>
<!-- Admin Content Area - form is centered in admin-content -->
<div class="add-game-container">
<?php endif; ?>
        <div class="admin-header">
            <h1 class="admin-title"><i class="fas fa-gamepad" style="margin-right: 10px;"></i> Ajouter un Jeu</h1>
            <p class="admin-subtitle">
                <?php if($isAdmin): ?>
                    Mode Administrateur: Les jeux que vous ajoutez seront automatiquement publiés et approuvés.
                <?php else: ?>
                    Soumettez votre jeu pour approbation par un administrateur
                <?php endif; ?>
            </p>
        </div>
        
        <?php if(isset($noPermission) && $noPermission): ?>
            <div class="no-permission">
                <div class="no-permission-icon"><i class="fas fa-lock"></i></div>
                <h2>Permission Requise</h2>
                <p>Vous n'avez pas la permission d'ajouter des jeux.</p>
                <p>Contactez un administrateur pour obtenir cette permission.</p>
                <a href="?controller=formation&action=userDashboard" class="btn-admin btn-edit" style="display: inline-block; margin-top: 20px;">
                    <i class="fas fa-home" style="margin-right: 8px;"></i> Retour à l'accueil
                </a>
            </div>
        <?php else: ?>
            <?php if(isset($success) && $success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i><?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Section "Vous avez une idée de jeu ?" - Cachée pour les admins -->
            <?php if(!$isAdmin): ?>
            <div class="idea-section" style="background: linear-gradient(135deg, rgba(0, 255, 204, 0.15), rgba(0, 204, 255, 0.1)); border: 2px solid rgba(0, 255, 204, 0.4); padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0, 255, 204, 0.2);">
                <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-gamepad"></i></div>
                <h2 style="color: var(--accent-cyan); margin: 0 0 12px 0; font-size: 24px; font-weight: 700;">Vous avez une idée de jeu ?</h2>
                <p style="margin: 0; color: var(--text-primary); font-size: 14px; line-height: 1.6;">
                    Partagez votre jeu avec la communauté ! Votre soumission sera examinée par un administrateur qui décidera de l'ajouter ou non à la plateforme.
                </p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="?action=add_game" enctype="multipart/form-data">
                <input type="hidden" name="add_game" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo AuthController::generateCsrfToken(); ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nom du jeu *</label>
                    <input type="text" id="gameName" name="name" class="form-control" required 
                           placeholder="Entrez le nom du jeu" minlength="2" maxlength="100">
                    <div class="validation-feedback" id="nameFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-folder"></i> Catégorie *</label>
                    <select id="category" name="category_id" class="form-control" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php if(isset($categories) && !empty($categories)): ?>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="validation-feedback" id="categoryFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heart"></i> Impact social *</label>
                    <input type="text" id="impactSocial" name="impact_social" class="form-control" required 
                           placeholder="Ex: Santé mentale, Écologie, Éducation..." minlength="5" maxlength="500">
                    <div class="validation-feedback" id="impactFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-file-alt"></i> Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="6" required 
                              placeholder="Décrivez votre jeu..." minlength="10" maxlength="2000"></textarea>
                    <div class="validation-feedback" id="descriptionFeedback"></div>
                    <small style="color: rgba(255, 255, 255, 0.5); font-size: 11px; display: block; margin-top: 5px;">
                        <span id="charCount">0</span> / 2000 caractères
                    </small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Image du jeu *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <label style="font-size: 12px; color: rgba(255, 255, 255, 0.6); margin-bottom: 8px; display: block;">
                                <i class="fas fa-upload" style="margin-right: 5px;"></i> Uploader une image
                            </label>
                            <input type="file" id="imageFile" name="image" class="form-control" 
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div style="text-align: center; color: rgba(255, 255, 255, 0.5); font-size: 14px; display: flex; align-items: center; justify-content: center;">
                            OU
                        </div>
                        <div>
                            <label style="font-size: 12px; color: rgba(255, 255, 255, 0.6); margin-bottom: 8px; display: block;">
                                <i class="fas fa-link" style="margin-right: 5px;"></i> URL externe
                            </label>
                            <input type="url" id="imageUrl" name="image_url" class="form-control" 
                                   placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                    <div class="validation-feedback" id="imageFeedback"></div>
                    <small style="color: rgba(255, 255, 255, 0.5); font-size: 11px;">
                        Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)
                    </small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-video"></i> Vidéo de démonstration (optionnel)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <label style="font-size: 12px; color: rgba(255, 255, 255, 0.6); margin-bottom: 8px; display: block;">
                                <i class="fas fa-upload" style="margin-right: 5px;"></i> Uploader une vidéo
                            </label>
                            <input type="file" name="video" class="form-control" 
                                   accept="video/mp4,video/webm,video/ogg">
                        </div>
                        <div style="text-align: center; color: rgba(255, 255, 255, 0.5); font-size: 14px; display: flex; align-items: center; justify-content: center;">
                            OU
                        </div>
                        <div>
                            <label style="font-size: 12px; color: rgba(255, 255, 255, 0.6); margin-bottom: 8px; display: block;">
                                <i class="fab fa-youtube" style="margin-right: 5px;"></i> URL YouTube/Vidéo
                            </label>
                            <input type="url" name="demo_url" class="form-control" 
                                   placeholder="https://youtube.com/embed/...">
                        </div>
                    </div>
                    <small style="color: rgba(255, 255, 255, 0.5); font-size: 11px;">
                        Formats acceptés: MP4, WebM, OGG (max 50MB) ou URL YouTube
                    </small>
                </div>
                
                <input type="hidden" name="status" value="development">
                
                <button type="submit" id="submitBtn" class="btn-submit">
                    🚀 Soumettre le Jeu
                </button>
            </form>
            
            <script>
            // Force correct styles on all form inputs - must run immediately
            (function() {
                function applyCorrectStyles() {
                    const formInputs = document.querySelectorAll('.add-game-container input, .add-game-container textarea, .add-game-container select');
                    formInputs.forEach(input => {
                        // Only apply if not invalid (validation will handle invalid state)
                        if (!input.classList.contains('invalid')) {
                            input.style.setProperty('border-color', 'rgba(232, 121, 249, 0.3)', 'important');
                            input.style.setProperty('background', 'linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%)', 'important');
                            input.style.setProperty('color', '#ffffff', 'important');
                        }
                    });
                }
                
                // Apply immediately if DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', applyCorrectStyles);
                } else {
                    applyCorrectStyles();
                }
                
                // Also apply after a short delay to override any late-loading styles
                setTimeout(applyCorrectStyles, 100);
                setTimeout(applyCorrectStyles, 500);
            })();
            
            // Validation complète du formulaire
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form[method="POST"]');
                const nameInput = document.getElementById('gameName');
                const categoryInput = document.getElementById('category');
                const impactInput = document.getElementById('impactSocial');
                const descriptionInput = document.getElementById('description');
                const imageFile = document.getElementById('imageFile');
                const imageUrl = document.getElementById('imageUrl');
                const submitBtn = document.getElementById('submitBtn');
                const charCount = document.getElementById('charCount');
                
                // Fonction pour afficher les messages de validation
                function showFeedback(element, feedbackId, message, isValid) {
                    const feedback = document.getElementById(feedbackId);
                    element.classList.remove('invalid', 'valid');
                    feedback.textContent = message;
                    feedback.className = 'validation-feedback';
                    
                    if (isValid) {
                        element.classList.add('valid');
                        feedback.classList.add('success');
                    } else {
                        element.classList.add('invalid');
                        feedback.classList.add('error');
                    }
                }
                
                // Validation de la catégorie
                function validateCategory() {
                    const value = categoryInput.value;
                    if (!value || value === '') {
                        showFeedback(categoryInput, 'categoryFeedback', '❌ Veuillez sélectionner une catégorie', false);
                        return false;
                    }
                    showFeedback(categoryInput, 'categoryFeedback', '✅ Catégorie valide', true);
                    return true;
                }
                
                // Validation du nom
                function validateName() {
                    const value = nameInput.value.trim();
                    if (value.length < 2) {
                        showFeedback(nameInput, 'nameFeedback', '❌ Le nom doit contenir au moins 2 caractères', false);
                        return false;
                    }
                    if (value.length > 100) {
                        showFeedback(nameInput, 'nameFeedback', '❌ Le nom ne peut pas dépasser 100 caractères', false);
                        return false;
                    }
                    // Vérifier si le nom commence par un chiffre
                    if (/^[0-9]/.test(value)) {
                        showFeedback(nameInput, 'nameFeedback', '❌ Le nom ne peut pas commencer par un chiffre', false);
                        return false;
                    }
                    showFeedback(nameInput, 'nameFeedback', '✅ Nom valide', true);
                    return true;
                }
                
                // Validation de l'impact social
                function validateImpact() {
                    const value = impactInput.value.trim();
                    if (value.length < 5) {
                        showFeedback(impactInput, 'impactFeedback', '❌ L\'impact social doit contenir au moins 5 caractères', false);
                        return false;
                    }
                    if (value.length > 500) {
                        showFeedback(impactInput, 'impactFeedback', '❌ L\'impact social ne peut pas dépasser 500 caractères', false);
                        return false;
                    }
                    showFeedback(impactInput, 'impactFeedback', '✅ Impact social valide', true);
                    return true;
                }
                
                // Validation de la description
                function validateDescription() {
                    const value = descriptionInput.value.trim();
                    const currentLength = value.length;
                    charCount.textContent = currentLength;
                    
                    if (currentLength < 10) {
                        showFeedback(descriptionInput, 'descriptionFeedback', '❌ La description doit contenir au moins 10 caractères', false);
                        return false;
                    }
                    if (currentLength > 2000) {
                        showFeedback(descriptionInput, 'descriptionFeedback', '❌ La description ne peut pas dépasser 2000 caractères', false);
                        return false;
                    }
                    showFeedback(descriptionInput, 'descriptionFeedback', '✅ Description valide', true);
                    return true;
                }
                
                // Validation de l'image
                function validateImage() {
                    const hasFile = imageFile.files.length > 0;
                    const hasUrl = imageUrl.value.trim().length > 0;
                    
                    if (!hasFile && !hasUrl) {
                        showFeedback(imageFile, 'imageFeedback', '❌ Vous devez fournir une image (upload ou URL)', false);
                        return false;
                    }
                    
                    // Validation du fichier
                    if (hasFile) {
                        const file = imageFile.files[0];
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        
                        if (!allowedTypes.includes(file.type)) {
                            showFeedback(imageFile, 'imageFeedback', '❌ Format non supporté. Utilisez JPG, PNG, GIF ou WebP', false);
                            return false;
                        }
                        
                        if (file.size > maxSize) {
                            showFeedback(imageFile, 'imageFeedback', '❌ Le fichier est trop volumineux (max 5MB)', false);
                            return false;
                        }
                    }
                    
                    // Validation de l'URL
                    if (hasUrl && !hasFile) {
                        const urlPattern = /^https?:\/\/.+\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i;
                        if (!urlPattern.test(imageUrl.value.trim())) {
                            showFeedback(imageUrl, 'imageFeedback', '❌ URL d\'image invalide', false);
                            return false;
                        }
                    }
                    
                    showFeedback(imageFile, 'imageFeedback', '✅ Image valide', true);
                    return true;
                }
                
                // Validation complète du formulaire
                function validateForm() {
                    const isCategoryValid = validateCategory();
                    const isNameValid = validateName();
                    const isImpactValid = validateImpact();
                    const isDescriptionValid = validateDescription();
                    const isImageValid = validateImage();
                    
                    const isValid = isCategoryValid && isNameValid && isImpactValid && isDescriptionValid && isImageValid;
                    submitBtn.disabled = !isValid;
                    
                    if (isValid) {
                        submitBtn.style.opacity = '1';
                        submitBtn.style.cursor = 'pointer';
                    } else {
                        submitBtn.style.opacity = '0.6';
                        submitBtn.style.cursor = 'not-allowed';
                    }
                    
                    return isValid;
                }
                
                // Événements de validation
                categoryInput.addEventListener('change', validateCategory);
                categoryInput.addEventListener('blur', validateCategory);
                
                nameInput.addEventListener('input', validateName);
                nameInput.addEventListener('blur', validateName);
                
                impactInput.addEventListener('input', validateImpact);
                impactInput.addEventListener('blur', validateImpact);
                
                descriptionInput.addEventListener('input', validateDescription);
                descriptionInput.addEventListener('blur', validateDescription);
                
                imageFile.addEventListener('change', function() {
                    if (imageFile.files.length > 0) {
                        imageUrl.value = '';
                    }
                    validateImage();
                    validateForm();
                });
                
                imageUrl.addEventListener('input', function() {
                    if (imageUrl.value.trim().length > 0) {
                        imageFile.value = '';
                    }
                    validateImage();
                    validateForm();
                });
                
                // Validation avant soumission
                form.addEventListener('submit', function(e) {
                    // Always allow form submission - server-side validation will handle errors
                    // But log validation state for debugging
                    const isValid = validateForm();
                    console.log('Form validation result:', isValid);
                    console.log('Form data:', {
                        name: nameInput.value,
                        category_id: categoryInput.value,
                        description: descriptionInput.value,
                        impact_social: impactInput.value,
                        status: document.querySelector('input[name="status"]')?.value,
                        image: imageFile.files.length > 0 ? 'file uploaded' : 'no file',
                        image_url: imageUrl.value,
                        csrf_token: document.querySelector('input[name="csrf_token"]')?.value
                    });
                    
                    // Don't prevent submission - let server handle validation
                    // if (!isValid) {
                    //     e.preventDefault();
                    //     alert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
                    //     const firstError = document.querySelector('.invalid');
                    //     if (firstError) {
                    //         firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    //     }
                    // }
                });
                
                // Validation initiale
                validateForm();
            });
            </script>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="?action=my_games" style="color: var(--accent-cyan); text-decoration: none;">
                    Voir mes jeux soumis
                </a>
            </div>
        <?php endif; ?>
    <?php if(!$isAdmin): ?>
    </div>
</section>
<?php else: ?>
</div>
<?php endif; ?>

<!-- Footer is handled by the main footer -->
<?php if(!$isAdmin): ?>
<?php include "views/front/includes/footer.php"; ?>
<?php endif; ?>

