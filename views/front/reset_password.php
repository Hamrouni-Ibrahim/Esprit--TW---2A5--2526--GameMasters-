<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe | Gaming & Impact Social</title>
    <style>
        :root {
            --primary-bg: #0e0e16;
            --secondary-bg: #141429;
            --card-bg: #1a1a2e;
            --accent-color: #00d1ff;
            --text-color: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .auth-container {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .auth-container h2 {
            margin-bottom: 20px;
            color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--accent-color);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.3);
            color: var(--text-color);
            font-size: 1em;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(0,209,255,0.2);
        }

        .error-message {
            color: #ff4444;
            font-size: 0.85em;
            margin-top: 5px;
            display: block;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: var(--accent-color);
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            color: black;
            margin-top: 10px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .btn:hover {
            background: #00b8e6;
            transform: translateY(-2px);
        }

        .error {
            color: #ff4444;
            background: rgba(255,68,68,0.1);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: left;
        }

        .success {
            color: #00ff88;
            background: rgba(0,255,136,0.1);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Réinitialiser le mot de passe</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if(isset($success) && $success): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
            <p style="margin-top: 15px;"><a href="index.php?action=login" style="color: var(--accent-color);">Se connecter</a></p>
        <?php else: ?>
            <form method="POST" action="index.php?action=reset_password" id="resetForm">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? $_POST['email'] ?? ''); ?>">
                <input type="hidden" name="reset_code" value="<?php echo htmlspecialchars($_GET['code'] ?? $_POST['reset_code'] ?? ''); ?>">
                
                <?php if(empty($_GET['email']) && empty($_GET['code'])): ?>
                    <div class="error">Lien de réinitialisation invalide. Veuillez utiliser le lien reçu par email.</div>
                <?php else: ?>
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe :</label>
                    <input type="password" name="password" id="password" class="form-control" required 
                           placeholder="Minimum 8 caractères" minlength="8">
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe :</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required 
                           placeholder="Confirmez votre mot de passe">
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>
                
                <button type="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
                <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        const form = document.getElementById('resetForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validateConfirmPassword);

        form.addEventListener('submit', function(e) {
            const isPasswordValid = validatePassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            
            if(!isPasswordValid || !isConfirmPasswordValid) {
                e.preventDefault();
            }
        });

        function validatePassword() {
            const value = password.value;
            
            if(value === '') {
                showError(password, passwordError, "Le mot de passe est requis");
                return false;
            }
            
            if(value.length < 8) {
                showError(password, passwordError, "Minimum 8 caractères");
                return false;
            }
            
            showSuccess(password, passwordError);
            return true;
        }

        function validateConfirmPassword() {
            const value = confirmPassword.value;
            const passwordValue = password.value;
            
            if(value === '') {
                showError(confirmPassword, confirmPasswordError, "Veuillez confirmer le mot de passe");
                return false;
            }
            
            if(value !== passwordValue) {
                showError(confirmPassword, confirmPasswordError, "Les mots de passe ne correspondent pas");
                return false;
            }
            
            showSuccess(confirmPassword, confirmPasswordError);
            return true;
        }

        function showError(input, errorElement, message) {
            input.style.borderColor = '#ff4444';
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function showSuccess(input, errorElement) {
            input.style.borderColor = 'var(--accent-color)';
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    </script>
</body>
</html>
