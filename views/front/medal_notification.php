<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Félicitations! Médaille Obtenue - Game Master</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a2e 50%, #16213e 100%);
        }

        .medal-notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .medal-notification {
            text-align: center;
            padding: 60px 40px;
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(232, 121, 249, 0.1));
            border: 3px solid;
            border-radius: 30px;
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(147, 51, 234, 0.3),
                inset 0 0 50px rgba(255, 255, 255, 0.05);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100px) scale(0.8);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .medal-notification.bronze {
            border-color: #cd7f32;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(205, 127, 50, 0.4),
                inset 0 0 50px rgba(205, 127, 50, 0.1);
        }

        .medal-notification.silver {
            border-color: #c0c0c0;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(192, 192, 192, 0.4),
                inset 0 0 50px rgba(192, 192, 192, 0.1);
        }

        .medal-notification.gold {
            border-color: #ffd700;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(255, 215, 0, 0.5),
                inset 0 0 50px rgba(255, 215, 0, 0.15);
        }

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: currentColor;
            position: absolute;
            animation: confettiFall 3s linear infinite;
        }

        @keyframes confettiFall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .medal-icon {
            font-size: 120px;
            display: block;
            margin: 0 auto 30px;
            animation: medalPulse 2s ease-in-out infinite, medalRotate 3s ease-in-out infinite;
            filter: drop-shadow(0 0 30px currentColor);
        }

        @keyframes medalPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @keyframes medalRotate {
            0%, 100% {
                transform: rotate(-5deg);
            }
            50% {
                transform: rotate(5deg);
            }
        }

        .congratulations {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff, #e879f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: textGlow 2s ease-in-out infinite;
        }

        @keyframes textGlow {
            0%, 100% {
                filter: drop-shadow(0 0 10px rgba(232, 121, 249, 0.5));
            }
            50% {
                filter: drop-shadow(0 0 20px rgba(232, 121, 249, 0.8));
            }
        }

        .medal-name {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .medal-name.bronze {
            color: #cd7f32;
            text-shadow: 0 0 20px rgba(205, 127, 50, 0.6);
        }

        .medal-name.silver {
            color: #c0c0c0;
            text-shadow: 0 0 20px rgba(192, 192, 192, 0.6);
        }

        .medal-name.gold {
            color: #ffd700;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
        }

        .message {
            font-size: 18px;
            color: #e0e0e0;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .close-button {
            padding: 15px 40px;
            background: linear-gradient(135deg, #9333ea, #c084fc);
            border: none;
            border-radius: 25px;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(147, 51, 234, 0.4);
        }

        .close-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(147, 51, 234, 0.6);
        }

        .close-button:active {
            transform: translateY(0);
        }

        .sparkles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
            animation: sparkle 2s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: 0;
                transform: scale(0);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="medal-notification-overlay">
        <div class="medal-notification <?php echo htmlspecialchars($medal); ?>">
            <div class="sparkles" id="sparkles"></div>
            <span class="medal-icon"><?php echo $medalIcon; ?></span>
            <h1 class="congratulations">🎉 Félicitations ! 🎉</h1>
            <h2 class="medal-name <?php echo htmlspecialchars($medal); ?>">Médaille <?php echo htmlspecialchars($medalName); ?></h2>
            <p class="message">
                Vous avez remporté la médaille <?php echo htmlspecialchars($medalName); ?> !<br>
                Votre persévérance et vos efforts ont porté leurs fruits. Continuez ainsi ! 🚀
            </p>
            <button class="close-button" onclick="closeNotification()">Continuer</button>
        </div>
    </div>

    <script>
        // Create sparkles
        function createSparkles() {
            const sparklesContainer = document.getElementById('sparkles');
            const medalColors = {
                'bronze': '#cd7f32',
                'silver': '#c0c0c0',
                'gold': '#ffd700'
            };
            const color = medalColors['<?php echo $medal; ?>'] || '#e879f9';
            
            for (let i = 0; i < 30; i++) {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                sparkle.style.left = Math.random() * 100 + '%';
                sparkle.style.top = Math.random() * 100 + '%';
                sparkle.style.background = color;
                sparkle.style.animationDelay = Math.random() * 2 + 's';
                sparkle.style.animationDuration = (1 + Math.random() * 2) + 's';
                sparklesContainer.appendChild(sparkle);
            }
        }

        function closeNotification() {
            const overlay = document.querySelector('.medal-notification-overlay');
            overlay.style.animation = 'fadeOut 0.5s ease-out';
            setTimeout(() => {
                // Mark notification as seen
                fetch('?controller=test&action=markMedalNotificationSeen', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'medal=<?php echo $medal; ?>'
                }).then(() => {
                    window.location.href = '?controller=formation&action=userDashboard';
                });
            }, 500);
        }

        // Create sparkles on load
        window.addEventListener('DOMContentLoaded', createSparkles);

        // Add fadeOut animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>






