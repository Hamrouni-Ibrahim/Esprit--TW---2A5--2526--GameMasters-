<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié | Gaming & Impact Social</title>
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

        .info {
            color: var(--accent-color);
            background: rgba(0,209,255,0.1);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Mot de passe oublié</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if(isset($success) && $success): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
            <p style="margin-top: 15px;"><a href="index.php?action=login" style="color: var(--accent-color);">Retour à la connexion</a></p>
        <?php else: ?>
            <div class="info">
                Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </div>
            
            <form method="POST" action="index.php?action=forgot_password" id="forgotForm">
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" name="email" id="email" class="form-control" required 
                           placeholder="Entrez votre email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <button type="submit" class="btn">Envoyer le lien de réinitialisation</button>
            </form>
            
            <p style="margin-top: 15px;">
                <a href="index.php?action=login" style="color: var(--accent-color);">Retour à la connexion</a>
            </p>
        <?php endif; ?>
    </div>

    <script>
        const form = document.getElementById('forgotForm');
        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        email.addEventListener('input', validateEmail);

        form.addEventListener('submit', function(e) {
            if(!validateEmail()) {
                e.preventDefault();
            }
        });

        function validateEmail() {
            const value = email.value.trim();
            
            if(value === '') {
                showError(email, emailError, "L'email est requis");
                return false;
            }
            
            if(!emailRegex.test(value)) {
                showError(email, emailError, "Format d'email invalide");
                return false;
            }
            
            showSuccess(email, emailError);
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
