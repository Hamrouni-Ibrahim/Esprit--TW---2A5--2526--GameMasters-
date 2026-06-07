<?php
// Set page variables for header
$pageTitle = 'Connexion - Game Master';
$currentPage = 'login';

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
                max-width: 450px;
                text-align: center;
                border: 1px solid rgba(0, 255, 204, 0.2);
                box-shadow: 
                    0 20px 60px rgba(0, 0, 0, 0.3),
                    0 0 40px rgba(0, 255, 204, 0.1),
                    inset 0 0 20px rgba(255, 255, 255, 0.05);
                position: relative;
                overflow: hidden;
                animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
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
                position: relative;
                z-index: 1;
            }
            
            .error a {
                position: relative;
                z-index: 9999 !important;
                pointer-events: auto !important;
                cursor: pointer !important;
                display: inline-block !important;
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

            .divider {
                margin: 30px 0;
                text-align: center;
                position: relative;
                z-index: 1;
            }

            .divider::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
            }

            .divider span {
                background: rgba(255, 255, 255, 0.03);
                padding: 0 20px;
                color: rgba(255, 255, 255, 0.5);
                position: relative;
            }

            .face-login-btn {
                display: inline-block;
                padding: 14px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                position: relative;
                z-index: 1;
                overflow: hidden;
            }

            .face-login-btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .face-login-btn:hover::before {
                width: 300px;
                height: 300px;
            }

            .face-login-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            }

            .register-link {
                margin-top: 25px;
                color: rgba(255, 255, 255, 0.7);
                position: relative;
                z-index: 1;
            }

            .register-link a {
                color: #00ffcc;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
            }

            .register-link a:hover {
                color: #00ccff;
                text-shadow: 0 0 10px rgba(0, 255, 204, 0.5);
            }

            .forgot-password-link {
                text-align: right;
                margin-top: 8px;
            }

            .forgot-password-link a {
                color: #00ffcc;
                text-decoration: none;
                font-size: 0.9em;
                transition: all 0.3s;
            }

            .forgot-password-link a:hover {
                color: #00ccff;
                text-shadow: 0 0 8px rgba(0, 255, 204, 0.4);
            }
        </style>

        <div class="auth-wrapper">
            <div class="auth-container">
                <!-- Logo and Title -->
                <div style="text-align: center; margin-bottom: 30px; position: relative; z-index: 1;">
                    <a href="?controller=formation&action=userDashboard" style="text-decoration: none; display: inline-block;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 80px; height: 60px; background: linear-gradient(135deg, rgba(0, 200, 180, 0.6) 0%, rgba(0, 160, 200, 0.6) 50%, rgba(100, 50, 180, 0.6) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(0, 255, 204, 0.3); box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);">
                                <img src="public/images/logo.png" alt="Game Master Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <svg viewBox="0 0 24 24" style="display: none; width: 40px; height: 40px; fill: #0a0e27;">
                                    <path d="M3 13h2v8H3zm4-8h2v13H7zm4-2h2v15h-2zm4 4h2v11h-2zm4-2h2v13h-2z"/>
                                </svg>
                            </div>
                            <div style="text-align: left;">
                                <div style="font-size: 28px; font-weight: 700; background: linear-gradient(135deg, #ffffff 0%, #00ffcc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1.2;">
                                    <span>Game</span> <span style="color: #00ffcc;">Master</span>
                                </div>
                                <div style="font-size: 12px; color: rgba(255, 255, 255, 0.6); letter-spacing: 2px; text-transform: uppercase; margin-top: 4px;">
                                    Gaming & Impact Social
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <h2>🔐 Connexion</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php if(strpos($error, 'email n\'a pas été vérifié') !== false): ?>
                <div class="verification-notice" style="margin-top: 15px; padding: 15px; background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 12px; text-align: center; animation: slideDown 0.4s ease-out; position: relative; z-index: 9999;">
                    <p style="margin: 0 0 12px 0; color: #00ffcc; font-weight: 600; position: relative; z-index: 10000;">📧 Vous devez vérifier votre email pour vous connecter.</p>
                    <a href="?action=verify_email_page" id="verify-link" class="verify-link-btn" style="display: inline-block; color: #00ffcc; text-decoration: none; font-weight: 600; border: 2px solid rgba(0, 255, 204, 0.5); padding: 10px 20px; border-radius: 8px; transition: all 0.3s; position: relative; z-index: 10000; cursor: pointer; pointer-events: auto; background: rgba(0, 255, 204, 0.1);" onmouseover="this.style.borderColor='#00ffcc'; this.style.background='rgba(0, 255, 204, 0.2)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)'" onmouseout="this.style.borderColor='rgba(0, 255, 204, 0.5)'; this.style.background='rgba(0, 255, 204, 0.1)'; this.style.transform='scale(1)'; this.style.boxShadow='none'" onclick="window.location.href='?action=verify_email_page'; return false;">Aller à la page de vérification</a>
                    <?php 
                    // In development, show the latest verification code from email_log.txt
                    $isDevelopment = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
                    if ($isDevelopment && file_exists('email_log.txt')) {
                        $lines = file('email_log.txt');
                        if (!empty($lines)) {
                            $lastLine = trim(end($lines));
                            if (preg_match('/Code: (\d{6})/', $lastLine, $matches)) {
                                echo '<p style="margin: 10px 0 0 0; font-size: 14px; color: #aaa;">Code de vérification (dev): <strong style="color: #00ffcc; font-size: 18px; letter-spacing: 3px;">' . $matches[1] . '</strong></p>';
                            }
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="success">Inscription réussie ! Vous pouvez maintenant vous connecter.</div>
        <?php endif; ?>

        <?php if(isset($_GET['logout']) && $_GET['logout'] == '1'): ?>
            <div class="success">Déconnexion réussie !</div>
        <?php endif; ?>
        
        <form method="POST" action="?action=login" id="loginForm" novalidate>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" class="form-control" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       placeholder="Entrez votre email">
                <span class="error-message" id="emailError"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required 
                       placeholder="Entrez votre mot de passe">
                <span class="error-message" id="passwordError"></span>
                <div class="forgot-password-link">
                    <a href="?action=forgot_password">Mot de passe oublié ?</a>
                </div>
            </div>
            
            <!-- reCAPTCHA -->
            <!-- reCAPTCHA supprimé -->
            
            <button type="submit" class="btn" id="submitBtn">Se connecter</button>
        </form>
        
                <!-- Option de connexion par reconnaissance faciale -->
                <div class="divider">
                    <span>OU</span>
                </div>
                <a href="?action=face_login" class="face-login-btn">
                    🎭 Se connecter avec votre visage
                </a>
                
                <p class="register-link">Pas encore inscrit ? <a href="?action=register">Créer un compte</a></p>
            </div>
        </div>
    </div>
</section>

    <script>
        const form = document.getElementById('loginForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');

        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(form) {
            email.addEventListener('input', validateEmail);
            password.addEventListener('input', validatePassword);

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();

                if (isEmailValid && isPasswordValid) {
                    form.submit();
                } else {
                    // Feedback visuel si nécessaire
                }
            });
        }

        function validateEmail() {
            if(!email) return false;
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
            if(!password) return false;
            const value = password.value;
            
            if (value === '') {
                showError(password, passwordError, "Le mot de passe est requis");
                return false;
            }
            
            showSuccess(password, passwordError);
            return true;
        }

        function showError(input, errorElement, message) {
            if(!input || !errorElement) return;
            input.classList.remove('success');
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function showSuccess(input, errorElement) {
            if(!input || !errorElement) return;
            input.classList.remove('error');
            input.classList.add('success');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }

        // Validation initiale
        document.addEventListener('DOMContentLoaded', function() {
            if(email && password) {
                // On ne valide pas immédiatement pour ne pas afficher d'erreurs au chargement
                // sauf si les champs sont pré-remplis par le navigateur
                if(email.value) validateEmail();
                if(password.value) validatePassword();
            }
        });
    </script>

<?php include "views/front/includes/footer.php"; ?>
