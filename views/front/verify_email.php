<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si pas de session, essayer de récupérer depuis l'URL ou permettre l'accès quand même
$hasPendingVerification = isset($_SESSION['pending_verification_user_id']) && isset($_SESSION['pending_verification_email']);
$verificationEmail = $_SESSION['pending_verification_email'] ?? '';
$pendingUserId = $_SESSION['pending_verification_user_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Email - Game Masters</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/templatemo-prism-flux.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #1a1a2e;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 20%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .verify-container {
            max-width: 500px;
            width: 90%;
            background: #16213e;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideUp 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .verify-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-title-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-title-section a {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .logo-box {
            width: 60px;
            height: 60px;
        }
        
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .title-text {
            text-align: left;
        }
        
        .title-main {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
        }
        
        .title-main span {
            color: #667eea;
        }
        
        .title-subtitle {
            font-size: 12px;
            color: #a0a0a0;
            letter-spacing: 1px;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .email-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
        }
        
        .verify-header h1 {
            color: #fff;
            margin: 0 0 10px 0;
            font-size: 26px;
        }
        
        .verify-header p {
            color: #b0b0b0;
            line-height: 1.6;
            margin: 0;
        }
        
        .verify-header strong {
            color: #667eea;
            font-weight: 500;
        }
        
        .code-input {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .code-input input {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #2a2a40;
            border-radius: 8px;
            background: #0f3460;
            color: #fff;
            transition: all 0.3s;
        }
        
        .code-input input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
            background: #1a1a2e;
        }
        
        .verify-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        
        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            filter: brightness(1.1);
        }
        
        .resend-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .resend-link p {
            color: #888;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .resend-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .resend-link a:hover {
            color: #9d8cd6;
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <!-- Logo and Title -->
        <div class="logo-title-section">
            <a href="?controller=formation&action=userDashboard">
                <div class="logo-box">
                    <img src="public/images/logo.png" alt="Game Master Logo">
                </div>
                <div class="title-text">
                    <div class="title-main">Game <span>Master</span></div>
                    <div class="title-subtitle">Gaming & Impact Social</div>
                </div>
            </a>
        </div>
        
        <div class="verify-header">
            <div class="email-icon">📧</div>
            <h1>Vérification Email</h1>
            <?php if ($hasPendingVerification): ?>
                <p>Un code à 6 chiffres a été envoyé à<br><strong><?= htmlspecialchars($verificationEmail) ?></strong></p>
                
                <?php 
                // Le code n'est plus affiché ici
                ?>
            <?php else: ?>
                <p style="color: #ff6b6b; margin-top: 15px;">
                    ⚠️ Aucune vérification en cours.
                </p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="?action=login" class="verify-btn" style="display:inline-block; text-decoration:none; width:auto; padding: 10px 30px;">
                        Connexion
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if ($hasPendingVerification): ?>
        <form method="POST" action="?action=verify_email" id="verifyForm">
            <div class="code-input">
                <input type="text" maxlength="1" pattern="[0-9]" name="code1" id="code1" required autocomplete="off">
                <input type="text" maxlength="1" pattern="[0-9]" name="code2" id="code2" required autocomplete="off">
                <input type="text" maxlength="1" pattern="[0-9]" name="code3" id="code3" required autocomplete="off">
                <input type="text" maxlength="1" pattern="[0-9]" name="code4" id="code4" required autocomplete="off">
                <input type="text" maxlength="1" pattern="[0-9]" name="code5" id="code5" required autocomplete="off">
                <input type="text" maxlength="1" pattern="[0-9]" name="code6" id="code6" required autocomplete="off">
            </div>
            
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($pendingUserId) ?>">
            <button type="submit" class="verify-btn">Vérifier mon email</button>
        </form>

        <div class="resend-link">
            <p>Vous n'avez pas reçu le code ?</p>
            <a href="?action=resend_verification">🔄 Renvoyer le code</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-focus et navigation entre les champs
        const inputs = document.querySelectorAll('.code-input input');
        
        inputs.forEach((input, index) => {
            // Passer au champ suivant quand un chiffre est entré
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            // Retourner au champ précédent avec Backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            // Permettre le collage du code complet
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                
                if (pastedData.length === 6) {
                    inputs.forEach((inp, idx) => {
                        inp.value = pastedData[idx] || '';
                    });
                    inputs[5].focus();
                }
            });
        });

        // Soumettre automatiquement quand tous les champs sont remplis
        inputs[inputs.length - 1].addEventListener('input', () => {
            const allFilled = Array.from(inputs).every(input => input.value.length === 1);
            if (allFilled) {
                setTimeout(() => {
                    document.getElementById('verifyForm').submit();
                }, 300);
            }
        });

        // Focus sur le premier champ au chargement
        window.addEventListener('load', () => {
            if(inputs.length > 0) inputs[0].focus();
        });
    </script>
</body>
</html>
