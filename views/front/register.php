<?php
// Set page variables for header
$pageTitle = 'Inscription - Game Master';
$currentPage = 'register';

// Include main site header
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
        <style>
            .auth-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 70vh;
                padding: 40px 20px;
                animation: fadeInUp 0.6s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .auth-container {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                padding: 50px 40px;
                border-radius: 20px;
                width: 100%;
                max-width: 500px;
                text-align: center;
                border: 1px solid rgba(0, 255, 204, 0.2);
                box-shadow: 
                    0 20px 60px rgba(0, 0, 0, 0.3),
                    0 0 40px rgba(0, 255, 204, 0.1),
                    inset 0 0 20px rgba(255, 255, 255, 0.05);
                position: relative;
                overflow: hidden;
                animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                max-height: 90vh;
                overflow-y: auto;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .auth-container::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, 
                    transparent 30%, 
                    rgba(0, 255, 204, 0.1) 50%, 
                    transparent 70%);
                animation: rotate 8s linear infinite;
            }

            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .auth-container h2 {
                margin-bottom: 30px;
                color: #00ffcc;
                font-size: 2.2em;
                font-weight: 700;
                text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);
                position: relative;
                z-index: 1;
                animation: glow 2s ease-in-out infinite alternate;
            }

            @keyframes glow {
                from {
                    text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);
                }
                to {
                    text-shadow: 0 0 30px rgba(0, 255, 204, 0.8), 0 0 40px rgba(0, 255, 204, 0.4);
                }
            }

            .form-group {
                margin-bottom: 20px;
                text-align: left;
                position: relative;
                z-index: 1;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #00ffcc;
                font-weight: 600;
                font-size: 0.95em;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .form-control {
                width: 100%;
                padding: 15px 18px;
                border-radius: 12px;
                border: 2px solid rgba(255, 255, 255, 0.1);
                background: rgba(0, 0, 0, 0.3);
                color: #ffffff;
                font-size: 1em;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                z-index: 1;
            }

            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.4);
            }

            .form-control:focus {
                outline: none;
                border-color: #00ffcc;
                box-shadow: 
                    0 0 0 3px rgba(0, 255, 204, 0.2),
                    0 0 20px rgba(0, 255, 204, 0.3);
                background: rgba(0, 0, 0, 0.4);
                transform: translateY(-2px);
            }

            .form-control.error {
                border-color: #ff4444;
                box-shadow: 0 0 0 3px rgba(255, 68, 68, 0.2);
                animation: shake 0.5s;
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }

            .form-control.success {
                border-color: #00ff88;
                box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.2);
            }

            .error-message {
                color: #ff6b6b;
                font-size: 0.85em;
                margin-top: 8px;
                display: block;
                animation: fadeIn 0.3s;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .success-message {
                color: #00ff88;
                font-size: 0.85em;
                margin-top: 8px;
                display: block;
            }

            .password-strength {
                height: 4px;
                background: rgba(0, 0, 0, 0.3);
                border-radius: 2px;
                margin-top: 8px;
                overflow: hidden;
                position: relative;
                z-index: 1;
            }

            .password-strength-bar {
                height: 100%;
                width: 0%;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 2px;
            }

            .strength-weak { background: #ff4444; width: 25%; }
            .strength-medium { background: #ffaa00; width: 50%; }
            .strength-strong { background: #00ff88; width: 75%; }
            .strength-very-strong { background: #00d1ff; width: 100%; }

            .btn {
                width: 100%;
                padding: 16px;
                background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
                border: none;
                border-radius: 12px;
                font-weight: 700;
                cursor: pointer;
                color: #0a0e27;
                margin-top: 15px;
                font-size: 1.1em;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                z-index: 1;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3);
            }

            .btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .btn:hover::before {
                width: 300px;
                height: 300px;
            }

            .btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 25px rgba(0, 255, 204, 0.5);
            }

            .btn:active {
                transform: translateY(-1px);
            }

            .btn:disabled {
                background: #666;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            .error {
                color: #ff6b6b;
                background: rgba(255, 107, 107, 0.15);
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 20px;
                text-align: left;
                border: 1px solid rgba(255, 107, 107, 0.3);
                animation: slideDown 0.4s ease-out;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .success {
                color: #00ff88;
                background: rgba(0, 255, 136, 0.15);
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 20px;
                border: 1px solid rgba(0, 255, 136, 0.3);
                animation: slideDown 0.4s ease-out;
            }

            .recaptcha-container {
                display: flex;
                justify-content: center;
                margin: 20px 0;
                position: relative;
                z-index: 1;
            }

            .login-link {
                margin-top: 25px;
                color: rgba(255, 255, 255, 0.7);
                position: relative;
                z-index: 1;
            }

            .login-link a {
                color: #00ffcc;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
            }

            .login-link a:hover {
                color: #00ccff;
                text-shadow: 0 0 10px rgba(0, 255, 204, 0.5);
            }

            .avatar-tabs {
                display: flex;
                background: rgba(0, 0, 0, 0.3);
                border-radius: 8px;
                padding: 4px;
                margin-bottom: 20px;
                position: relative;
                z-index: 1;
            }

            .tab-label {
                flex: 1;
                padding: 10px;
                cursor: pointer;
                border-radius: 6px;
                transition: all 0.3s;
                font-size: 14px;
                color: rgba(255, 255, 255, 0.7);
            }

            .tab-label.active {
                background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
                color: #0a0e27;
                font-weight: bold;
            }

            .avatar-grid {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 8px;
                padding: 10px;
                background: rgba(0, 0, 0, 0.2);
                border-radius: 8px;
                position: relative;
                z-index: 1;
            }

            .avatar-option {
                aspect-ratio: 1;
                border-radius: 50%;
                cursor: pointer;
                border: 2px solid transparent;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .avatar-option:hover {
                transform: scale(1.15);
                border-color: rgba(0, 255, 204, 0.5);
                box-shadow: 0 0 15px rgba(0, 255, 204, 0.4);
            }

            .avatar-option.selected {
                border-color: #00ffcc;
                box-shadow: 0 0 20px rgba(0, 255, 204, 0.6);
                transform: scale(1.1);
            }

            .avatar-option img {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
            }
        </style>

        <div class="auth-wrapper">
            <div class="auth-container">
                <!-- Logo and Title -->
                <div style="text-align: center; margin-bottom: 30px; position: relative; z-index: 1;">
                    <a href="?controller=formation&action=userDashboard" style="text-decoration: none; display: inline-block;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 15px;">
                            <div class="logo-icon" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid rgba(0, 255, 204, 0.4); background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); box-shadow: 0 0 20px rgba(0, 255, 204, 0.4), 0 0 40px rgba(244, 114, 182, 0.3); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: all 0.3s ease;">
                                <img src="public/images/logo.png" alt="Game Master Logo" class="logo-image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; filter: brightness(1.1) contrast(1.1);" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <svg viewBox="0 0 24 24" class="logo-fallback" style="display: none; width: 40px; height: 40px; fill: #00ffcc;">
                                    <path d="M3 13h2v8H3zm4-8h2v13H7zm4-2h2v15h-2zm4 4h2v11h-2zm4-2h2v13h-2z"/>
                                </svg>
                            </div>
                            <div style="text-align: left;">
                                <div class="logo-text" style="font-size: 32px; font-weight: 500; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; line-height: 1.2;">
                                    <span class="prism" style="background: linear-gradient(135deg, #00ffcc 0%, #06b6d4 50%, #f472b6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 500;">Game</span>
                                    <span class="flux" style="background: linear-gradient(135deg, #06b6d4 0%, #f472b6 50%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 500;">Master</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <h2>✨ Créer un compte</h2>
        
        <?php if(isset($errors) && !empty($errors)): ?>
            <div class="error-messages">
                <?php foreach($errors as $error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($success) && $success): ?>
            <div class="success"><?php echo $message; ?></div>
            <?php if(isset($verification_code)): ?>
                <div class="success" style="margin-top: 10px; font-size: 18px; text-align: center;">
                    <strong>Code de vérification:</strong> <span style="font-size: 24px; letter-spacing: 5px;"><?php echo htmlspecialchars($verification_code); ?></span>
                    <br><small>Copiez ce code pour vérifier votre email</small>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form id="registerForm" method="POST" action="?action=register" enctype="multipart/form-data" novalidate autocomplete="off">
            <!-- Avatar Section -->
            <div class="form-group" style="text-align: center; margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 15px; color: #00ffcc; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">👤 Avatar de profil</label>
                
                <!-- Tabs -->
                <div class="avatar-tabs" style="display: flex; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 4px; margin-bottom: 20px;">
                    <label class="tab-label active" data-tab="preset" onclick="switchAvatarTab('preset')">Sélectionner</label>
                    <label class="tab-label" data-tab="upload" onclick="switchAvatarTab('upload')">Importer</label>
                </div>

                <style>
                    .tab-label {
                        flex: 1;
                        padding: 10px;
                        cursor: pointer;
                        border-radius: 6px;
                        transition: all 0.3s;
                        font-size: 14px;
                    }
                    .tab-label.active {
                        background: var(--accent-color);
                        color: #000;
                        font-weight: bold;
                    }
                    .avatar-grid {
                        display: grid;
                        grid-template-columns: repeat(6, 1fr);
                        gap: 8px;
                        /* Removed max-height/overflow for no scrolling */
                        padding: 10px;
                        background: rgba(0,0,0,0.2);
                        border-radius: 8px;
                    }
                    .avatar-option {
                        aspect-ratio: 1;
                        border-radius: 50%;
                        cursor: pointer;
                        border: 2px solid transparent;
                        transition: all 0.2s;
                    }
                    .avatar-option:hover {
                        transform: scale(1.1);
                        border-color: rgba(255,255,255,0.5);
                    }
                    .avatar-option.selected {
                        border-color: var(--accent-color);
                        box-shadow: 0 0 10px var(--accent-color);
                    }
                    .avatar-option img {
                        width: 100%;
                        height: 100%;
                        border-radius: 50%;
                        object-fit: cover;
                    }
                </style>

                <!-- 1. Preset Grid -->
                <div id="tab-preset" class="avatar-tab-content">
                    <div class="avatar-grid">
                        <?php 
                        // Générer la grille d'avatars (18 avatars)
                        for($i=1; $i<=18; $i++) {
                            $imgName = "avatar{$i}.jpg";
                            echo "<div class='avatar-option' onclick=\"selectPreset('$imgName', this)\">";
                            echo "<img src='public/assets/img/avatars/$imgName' alt='Avatar $i' onerror=\"this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 200 200%27%3E%3Crect width=%27200%27 height=%27200%27 fill=%27%2300d1ff%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dy=%27.35em%27 text-anchor=%27middle%27 fill=%27%23FFFFFF%27 font-size=%27100%27 font-weight=%27bold%27%3E$i%3C/text%3E%3C/svg%3E';\">";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    <input type="hidden" name="avatar_preset" id="avatarPresetInput">
                </div>

                <!-- 2. Upload -->
                <div id="tab-upload" class="avatar-tab-content" style="display: none;">
                    <div style="padding: 20px; border: 2px dashed rgba(0, 255, 204, 0.3); border-radius: 12px; background: rgba(0, 0, 0, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='rgba(0, 255, 204, 0.5)'; this.style.background='rgba(0, 0, 0, 0.3)'" onmouseout="this.style.borderColor='rgba(0, 255, 204, 0.3)'; this.style.background='rgba(0, 0, 0, 0.2)'">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="form-control" style="padding: 8px; margin-bottom: 10px;">
                        <small style="color: rgba(255, 255, 255, 0.6); display: block;">Format : JPG, PNG, GIF (Max 2Mo)</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" class="form-control" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       placeholder="Entrez votre pseudo (3-20 caractères)"
                       minlength="3" maxlength="20"
                       pattern="[a-zA-Z0-9_]+"
                       autocomplete="new-username"
                       title="Seulement lettres, chiffres et underscores">
                <span class="error-message" id="usernameError"></span>
            </div>
            
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" class="form-control" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       placeholder="Entrez votre email valide"
                       autocomplete="new-email">
                <span class="error-message" id="emailError"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required 
                       placeholder="Minimum 6 caractères"
                       minlength="6"
                       autocomplete="new-password">
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrength"></div>
                </div>
                <span class="error-message" id="passwordError"></span>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required 
                       placeholder="Confirmez votre mot de passe"
                       autocomplete="new-password">
                <span class="error-message" id="confirmPasswordError"></span>
            </div>
            
            <!-- reCAPTCHA -->
            <div class="form-group">
                <div class="recaptcha-container">
                    <?php 
                    // Load reCAPTCHA config if not already loaded
                    if (!defined('RECAPTCHA_ENABLED')) {
                        $configPath = __DIR__ . '/../../config/recaptcha.php';
                        if(file_exists($configPath)) {
                            require_once $configPath;
                        } else {
                            // Fallback: define defaults if config file doesn't exist
                            if(!defined('RECAPTCHA_ENABLED')) define('RECAPTCHA_ENABLED', false);
                            if(!defined('RECAPTCHA_SITE_KEY')) define('RECAPTCHA_SITE_KEY', '');
                            if(!defined('RECAPTCHA_SECRET_KEY')) define('RECAPTCHA_SECRET_KEY', '');
                        }
                    }
                    
                    if(defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && defined('RECAPTCHA_SITE_KEY') && !empty(RECAPTCHA_SITE_KEY)):
                    ?>
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY) ?>"></div>
                    <?php else: ?>
                    <div style="padding: 10px; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 8px; color: #ffc107; font-size: 0.9em;">
                        ⚠️ reCAPTCHA n'est pas configuré. Copiez .env.example vers .env et renseignez RECAPTCHA_SITE_KEY / RECAPTCHA_SECRET_KEY.
                    </div>
                    <?php endif; ?>
                </div>
                <span class="error-message" id="captchaError"></span>
            </div>
            
            <button type="submit" class="btn" id="submitBtn">🚀 S'inscrire</button>
        </form>
        <p class="login-link">Déjà inscrit ? <a href="?action=login">Se connecter</a></p>
            </div>
        </div>
    </div>
</section>

    <script>
        // Éléments du formulaire
        const form = document.getElementById('registerForm');
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submitBtn');

        // Messages d'erreur
        const usernameError = document.getElementById('usernameError');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const captchaError = document.getElementById('captchaError');
        const passwordStrength = document.getElementById('passwordStrength');

        // Expressions régulières
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validation en temps réel
        username.addEventListener('input', validateUsername);
        email.addEventListener('input', validateEmail);
        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validateConfirmPassword);

        // Validation complète avant soumission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isUsernameValid = validateUsername();
            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            const isCaptchaValid = validateCaptcha();

            if (isUsernameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid && isCaptchaValid) {
                form.submit();
            } else {
                alert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
            }
        });

        function validateCaptcha() {
            captchaError.textContent = '';
            const captchaResponse = document.querySelector('[name="g-recaptcha-response"]');
            
            // Si le captcha n'est pas présent (désactivé), on retourne true
            if (!captchaResponse) {
                return true;
            }
            
            if (!captchaResponse.value) {
                captchaError.textContent = 'Veuillez cocher la case "Je ne suis pas un robot"';
                captchaError.style.display = 'block';
                return false;
            }
            
            captchaError.style.display = 'none';
            return true;
        }

        function validateUsername() {
            const value = username.value.trim();
            
            if (value === '') {
                showError(username, usernameError, "Le nom d'utilisateur est requis");
                return false;
            }
            
            if (value.length < 3) {
                showError(username, usernameError, "Minimum 3 caractères");
                return false;
            }
            
            if (value.length > 20) {
                showError(username, usernameError, "Maximum 20 caractères");
                return false;
            }
            
            if (!usernameRegex.test(value)) {
                showError(username, usernameError, "Seulement lettres, chiffres et underscores (_)");
                return false;
            }
            
            // NOUVELLE VALIDATION : Ne peut pas être que des chiffres
            if (/^[0-9]+$/.test(value)) {
                showError(username, usernameError, "Le nom d'utilisateur ne peut pas être composé uniquement de chiffres");
                return false;
            }
            
            // NOUVELLE VALIDATION : Doit contenir au moins une lettre
            if (!/[a-zA-Z]/.test(value)) {
                showError(username, usernameError, "Le nom d'utilisateur doit contenir au moins une lettre");
                return false;
            }
            
            showSuccess(username, usernameError);
            return true;
        }

        function validateEmail() {
            const value = email.value.trim();
            
            if (value === '') {
                showError(email, emailError, "L'email est requis");
                return false;
            }
            
            if (!emailRegex.test(value)) {
                showError(email, emailError, "Format d'email invalide");
                return false;
            }
            
            showSuccess(email, emailError);
            return true;
        }

        function validatePassword() {
            const value = password.value;
            
            if (value === '') {
                showError(password, passwordError, "Le mot de passe est requis");
                updatePasswordStrength(0);
                return false;
            }
            
            if (value.length < 6) {
                showError(password, passwordError, "Minimum 6 caractères");
                updatePasswordStrength(25);
                return false;
            }
            
            // Calcul de la force du mot de passe
            let strength = 0;
            
            // Longueur
            if (value.length >= 8) strength += 25;
            
            // Lettres minuscules
            if (/[a-z]/.test(value)) strength += 25;
            
            // Lettres majuscules
            if (/[A-Z]/.test(value)) strength += 25;
            
            // Chiffres et caractères spéciaux
            if (/[\d@$!%*?&]/.test(value)) strength += 25;
            
            updatePasswordStrength(strength);
            
            if (strength < 50) {
                showError(password, passwordError, "Mot de passe faible");
                return false;
            }
            
            showSuccess(password, passwordError);
            return true;
        }

        function validateConfirmPassword() {
            const value = confirmPassword.value;
            const passwordValue = password.value;
            
            if (value === '') {
                showError(confirmPassword, confirmPasswordError, "Veuillez confirmer le mot de passe");
                return false;
            }
            
            if (value !== passwordValue) {
                showError(confirmPassword, confirmPasswordError, "Les mots de passe ne correspondent pas");
                return false;
            }
            
            showSuccess(confirmPassword, confirmPasswordError);
            return true;
        }

        function updatePasswordStrength(strength) {
            passwordStrength.className = 'password-strength-bar';
            
            if (strength <= 25) {
                passwordStrength.classList.add('strength-weak');
            } else if (strength <= 50) {
                passwordStrength.classList.add('strength-medium');
            } else if (strength <= 75) {
                passwordStrength.classList.add('strength-strong');
            } else {
                passwordStrength.classList.add('strength-very-strong');
            }
        }

        function showError(input, errorElement, message) {
            input.classList.remove('success');
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function showSuccess(input, errorElement) {
            input.classList.remove('error');
            input.classList.add('success');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }


        // Avatar Tab Handling
        function switchAvatarTab(tabName) {
            // Update active tab
            document.querySelectorAll('.tab-label').forEach(el => el.classList.remove('active'));
            document.querySelector(`.tab-label[data-tab="${tabName}"]`).classList.add('active');
            
            // Show content
            document.querySelectorAll('.avatar-tab-content').forEach(el => el.style.display = 'none');
            document.getElementById('tab-' + tabName).style.display = 'block';
            
            // Reset inputs based on tab
            const presetInput = document.getElementById('avatarPresetInput');
            const fileInput = document.getElementById('avatarInput');
            
            if(tabName === 'upload') {
                presetInput.value = '';
                document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
            } else if(tabName === 'preset') {
                fileInput.value = '';
            }
        }

        function selectPreset(filename, element) {
            // Update visual selection
            document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            
            // Set input value
            document.getElementById('avatarPresetInput').value = filename;
            
            // Clear other inputs
            document.getElementById('avatarInput').value = '';
        }

        // Validation initiale au chargement
        document.addEventListener('DOMContentLoaded', function() {
            validateUsername(); // Check initial state
        });
    </script>

    <?php 
    // Load reCAPTCHA script if enabled
    if (!defined('RECAPTCHA_ENABLED')) {
        $configPath = __DIR__ . '/../../config/recaptcha.php';
        if(file_exists($configPath)) {
            require_once $configPath;
        } else {
            if(!defined('RECAPTCHA_ENABLED')) define('RECAPTCHA_ENABLED', false);
            if(!defined('RECAPTCHA_SITE_KEY')) define('RECAPTCHA_SITE_KEY', '');
        }
    }
    
    if(defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && defined('RECAPTCHA_SITE_KEY') && !empty(RECAPTCHA_SITE_KEY)):
    ?>
    <!-- Google reCAPTCHA v2 Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>

<?php include "views/front/includes/footer.php"; ?>
