<?php
// Set page variables for header
$pageTitle = 'Choix de Reconnaissance Faciale - Game Master';
$currentPage = 'face_choice';

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
            .face-choice-wrapper {
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

            .face-choice-container {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                padding: 50px 40px;
                border-radius: 20px;
                width: 100%;
                max-width: 600px;
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

            .face-choice-container h2 {
                margin-bottom: 20px;
                color: #00ffcc;
                font-size: 2em;
                font-weight: 700;
                text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);
                position: relative;
                z-index: 1;
            }

            .face-choice-container p {
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 40px;
                font-size: 1.1em;
                line-height: 1.6;
                position: relative;
                z-index: 1;
            }

            .face-choice-buttons {
                display: flex;
                flex-direction: column;
                gap: 20px;
                margin-top: 30px;
            }

            .face-choice-btn {
                padding: 18px 40px;
                border-radius: 12px;
                font-size: 1.1em;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 2px solid transparent;
                cursor: pointer;
                display: inline-block;
                position: relative;
                z-index: 1;
            }

            .face-choice-btn.primary {
                background: linear-gradient(135deg, #00ffcc, #00ccff);
                color: #0a0a0a;
                border-color: rgba(0, 255, 204, 0.5);
            }

            .face-choice-btn.primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(0, 255, 204, 0.4);
                border-color: #00ffcc;
            }

            .face-choice-btn.secondary {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                border-color: rgba(255, 255, 255, 0.2);
            }

            .face-choice-btn.secondary:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.4);
            }

            .face-icon {
                font-size: 4em;
                margin-bottom: 20px;
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.1);
                }
            }

            .benefits {
                text-align: left;
                margin: 30px 0;
                padding: 20px;
                background: rgba(0, 255, 204, 0.05);
                border-radius: 12px;
                border: 1px solid rgba(0, 255, 204, 0.2);
            }

            .benefits h3 {
                color: #00ffcc;
                margin-bottom: 15px;
                font-size: 1.2em;
            }

            .benefits ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .benefits li {
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 10px;
                padding-left: 25px;
                position: relative;
            }

            .benefits li:before {
                content: "✓";
                position: absolute;
                left: 0;
                color: #00ffcc;
                font-weight: bold;
            }
        </style>

        <div class="face-choice-wrapper">
            <div class="face-choice-container">
                <div class="face-icon">🎭</div>
                <h2>Vérification Email Réussie !</h2>
                <p>Votre email a été vérifié avec succès. Voulez-vous activer la reconnaissance faciale pour une connexion rapide et sécurisée ?</p>

                <div class="benefits">
                    <h3>Avantages de la reconnaissance faciale :</h3>
                    <ul>
                        <li>Connexion rapide en un clic</li>
                        <li>Plus besoin de saisir votre mot de passe</li>
                        <li>Sécurité renforcée</li>
                        <li>Vous pouvez toujours utiliser votre mot de passe si besoin</li>
                    </ul>
                </div>

                <div class="face-choice-buttons">
                    <a href="?action=face_registration" class="face-choice-btn primary">
                        🎭 Oui, activer la reconnaissance faciale
                    </a>
                    <a href="?controller=formation&action=userDashboard" class="face-choice-btn secondary">
                        Non, continuer sans reconnaissance faciale
                    </a>
                </div>

                <p style="margin-top: 30px; font-size: 0.9em; color: rgba(255, 255, 255, 0.6);">
                    💡 Vous pourrez activer la reconnaissance faciale plus tard depuis votre profil
                </p>
            </div>
        </div>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>




