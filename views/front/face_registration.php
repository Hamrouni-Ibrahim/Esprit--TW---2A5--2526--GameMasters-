<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrer votre visage | Gaming & Impact Social</title>
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js" onload="console.log('face-api.js chargé'); if(typeof faceapi !== 'undefined') { window.faceapi = faceapi; console.log('faceapi assigné à window.faceapi'); }" onerror="console.error('Erreur de chargement de face-api.js');"></script>
    <script>
        // S'assurer que face-api est accessible globalement
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (typeof window.faceapi === 'undefined' && typeof faceapi !== 'undefined') {
                    window.faceapi = faceapi;
                    console.log('faceapi assigné après chargement');
                }
                if (typeof window.faceapi !== 'undefined') {
                    console.log('✓ face-api.js est disponible');
                } else {
                    console.error('✗ face-api.js n\'est pas disponible');
                }
            }, 1000);
        });
    </script>
    <script src="public/assets/js/face-auth.js"></script>
    <style>
        :root {
            --primary-bg: #0e0e16;
            --secondary-bg: #141429;
            --card-bg: #1a1a2e;
            --accent-color: #00d1ff;
            --success-color: #00ff88;
            --error-color: #ff4444;
            --text-color: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-bg);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        h1 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
        }

        .video-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto 20px;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }

        #video {
            width: 100%;
            height: auto;
            display: block;
        }

        #canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            pointer-events: none;
        }

        .status-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            display: none;
        }

        .status-indicator.detecting {
            background: rgba(0,209,255,0.9);
            color: #000;
            display: block;
        }

        .status-indicator.detected {
            background: rgba(0,255,136,0.9);
            color: #000;
            display: block;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--accent-color);
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            color: #000;
            font-size: 1em;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn:hover:not(:disabled) {
            background: #00b8e6;
            transform: translateY(-2px);
        }

        .btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-color);
        }

        .btn-secondary:hover:not(:disabled) {
            background: rgba(255,255,255,0.2);
        }

        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .message.error {
            background: rgba(255,68,68,0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
            display: block;
        }

        .message.success {
            background: rgba(0,255,136,0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
            display: block;
        }

        .loader {
            border: 3px solid rgba(255,255,255,0.1);
            border-top: 3px solid var(--accent-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .instructions {
            background: rgba(0,209,255,0.1);
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .instructions h3 {
            margin-top: 0;
            color: var(--accent-color);
        }

        .instructions ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }

        .instructions li {
            margin-bottom: 8px;
            color: rgba(255,255,255,0.9);
        }

        .back-link {
            color: var(--accent-color);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if(!isset($_GET['onboarding'])): ?>
        <a href="index.php?action=profile" class="back-link">← Retour au profil</a>
        <?php endif; ?>
        
        <div class="card">
            <?php if(isset($_GET['onboarding'])): ?>
                <h1>👋 Bienvenue !</h1>
                <p class="subtitle">Configurez votre Face ID pour une connexion rapide et sécurisée</p>
            <?php else: ?>
                <h1>🎭 Enregistrer votre visage</h1>
                <p class="subtitle">Configuration de la reconnaissance faciale pour votre compte</p>
            <?php endif; ?>
            
            <div class="instructions">
                <h3>Instructions :</h3>
                <ul>
                    <li>Positionnez votre visage face à la caméra</li>
                    <li>Assurez-vous d'avoir un bon éclairage</li>
                    <li>Retirez les lunettes de soleil ou masques</li>
                    <li>Attendez que votre visage soit détecté (cadre vert)</li>
                    <li>Cliquez sur "Capturer" pour enregistrer</li>
                </ul>
            </div>

            <div id="faceError" class="message error" style="display: none;"></div>
            <div id="faceSuccess" class="message success" style="display: none;"></div>
            <div id="faceLoader" class="loader"></div>

            <div id="videoSection">
                <div class="video-container">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas"></canvas>
                    <div id="statusIndicator" class="status-indicator">Détection en cours...</div>
                </div>

                <button id="captureBtn" class="btn" disabled>Capturer mon visage</button>
                <?php if(isset($_GET['onboarding'])): ?>
                    <button id="cancelBtn" class="btn btn-secondary">Passer cette étape pour le moment</button>
                <?php else: ?>
                    <button id="cancelBtn" class="btn btn-secondary">Annuler</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let video, canvas, statusIndicator;
        let detectionInterval;
        let currentDescriptor = null;

        // Fonction pour attendre qu'un script soit chargé
        function waitForScript(globalVar, maxWait = 20000) {
            return new Promise((resolve, reject) => {
                if (typeof window[globalVar] !== 'undefined') {
                    console.log(`${globalVar} est déjà chargé`);
                    resolve(true);
                    return;
                }
                
                const startTime = Date.now();
                const checkInterval = setInterval(() => {
                    if (typeof window[globalVar] !== 'undefined') {
                        clearInterval(checkInterval);
                        console.log(globalVar + ' chargé avec succès');
                        resolve(true);
                    } else if (Date.now() - startTime > maxWait) {
                        clearInterval(checkInterval);
                        console.error(globalVar + ' n\'a pas été chargé dans les délais');
                        reject(new Error(globalVar + ' n\'a pas été chargé'));
                    }
                }, 100);
            });
        }

        // Attendre que tous les scripts soient chargés
        async function initializeFaceRecognition() {
            try {
                console.log('Début de l\'initialisation de la reconnaissance faciale...');
                
                // Attendre que face-api.js soit chargé
                let faceapiLib = null;
                let attempts = 0;
                const maxAttempts = 200; // 20 secondes max
                
                while (attempts < maxAttempts) {
                    if (typeof window.faceapi !== 'undefined') {
                        faceapiLib = window.faceapi;
                        break;
                    } else if (typeof faceapi !== 'undefined') {
                        window.faceapi = faceapi;
                        faceapiLib = faceapi;
                        break;
                    }
                    await new Promise(resolve => setTimeout(resolve, 100));
                    attempts++;
                }
                
                if (!faceapiLib || !faceapiLib.nets) {
                    throw new Error('face-api.js n\'est pas chargé. Vérifiez votre connexion internet.');
                }
                
                console.log('face-api.js chargé ✓');
                
                // Attendre que FaceAuth soit chargé
                attempts = 0;
                while (attempts < maxAttempts) {
                    if (typeof window.FaceAuth !== 'undefined' && typeof window.FaceAuth.loadModels === 'function') {
                        console.log('FaceAuth chargé ✓');
                        break;
                    }
                    await new Promise(resolve => setTimeout(resolve, 100));
                    attempts++;
                }
                
                if (typeof window.FaceAuth === 'undefined' || typeof window.FaceAuth.loadModels !== 'function') {
                    throw new Error('face-auth.js n\'est pas chargé correctement. Vérifiez que le fichier existe: public/assets/js/face-auth.js');
                }
                
                console.log('Tous les scripts sont chargés ✓');
            } catch (error) {
                console.error('Erreur de chargement:', error);
                const errorDiv = document.getElementById('faceError');
                if (errorDiv) {
                    errorDiv.innerHTML = 'Erreur: ' + error.message + '<br><br>Vérifiez la console du navigateur (F12) pour plus de détails.';
                    errorDiv.style.display = 'block';
                }
                throw error;
            }
        }
        
        // Lancer l'initialisation
        window.addEventListener('load', async () => {
            try {
                await initializeFaceRecognition();
            } catch (error) {
                return; // L'erreur est déjà affichée
            }

            video = document.getElementById('video');
            canvas = document.getElementById('canvas');
            statusIndicator = document.getElementById('statusIndicator');
            const captureBtn = document.getElementById('captureBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            // Utiliser window.FaceAuth pour être sûr
            const FaceAuth = window.FaceAuth;
            if (!FaceAuth) {
                throw new Error('FaceAuth n\'est pas disponible');
            }

            // Charger les modèles
            if (FaceAuth.showLoader) {
                FaceAuth.showLoader(true);
            }
            const loaded = await FaceAuth.loadModels();
            if (FaceAuth.showLoader) {
                FaceAuth.showLoader(false);
            }

            if (!loaded) {
                if (FaceAuth.showError) {
                    FaceAuth.showError('Impossible de charger les modèles de reconnaissance faciale.');
                }
                return;
            }

            // Démarrer la webcam
            console.log('Demande d\'accès à la caméra...');
            statusIndicator.textContent = '📷 Activation de la caméra...';
            const started = await FaceAuth.startWebcam(video);
            if (!started) {
                console.error('Échec du démarrage de la webcam');
                const errorDiv = document.getElementById('faceError');
                if (errorDiv) {
                    errorDiv.innerHTML = 'Impossible d\'accéder à la caméra. Vérifiez les permissions de votre navigateur et réessayez.';
                    errorDiv.style.display = 'block';
                }
                statusIndicator.textContent = '❌ Erreur d\'accès à la caméra';
                return;
            }
            console.log('Caméra démarrée avec succès ✓');
            console.log('Video playing:', !video.paused);
            console.log('Video readyState:', video.readyState);
            console.log('Video videoWidth:', video.videoWidth);
            console.log('Video videoHeight:', video.videoHeight);

            // Vérifier que la vidéo fonctionne
            const checkVideoReady = () => {
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    console.log('Vidéo opérationnelle ✓');
                    statusIndicator.textContent = 'Détection en cours...';
                    statusIndicator.className = 'status-indicator detecting';
                    startContinuousDetection();
                } else {
                    console.warn('Vidéo pas encore prête, nouvelle tentative...');
                    setTimeout(checkVideoReady, 500);
                }
            };

            // Attendre que la vidéo soit prête
            if (video.readyState >= 2 && video.videoWidth > 0) {
                checkVideoReady();
            } else {
                video.addEventListener('loadedmetadata', () => {
                    console.log('Métadonnées chargées');
                    checkVideoReady();
                });
                video.addEventListener('playing', () => {
                    console.log('Vidéo en cours de lecture');
                    checkVideoReady();
                });
                
                // Vérification de sécurité
                setTimeout(() => {
                    if (video.videoWidth === 0) {
                        console.error('La vidéo ne semble pas fonctionner');
                        statusIndicator.textContent = '❌ Problème avec la caméra';
                    } else {
                        checkVideoReady();
                    }
                }, 2000);
            }

            // Événement capture
            captureBtn.addEventListener('click', async () => {
                const FaceAuth = window.FaceAuth;
                if (!currentDescriptor) {
                    FaceAuth.showError('Aucun visage détecté. Veuillez réessayer.');
                    return;
                }

                captureBtn.disabled = true;
                FaceAuth.hideMessages();
                FaceAuth.showLoader(true);

                // Enregistrer le visage
                const result = await FaceAuth.registerFace(currentDescriptor);
                
                FaceAuth.showLoader(false);

                if (result.success) {
                    FaceAuth.showSuccess(result.message || 'Visage enregistré avec succès !');
                    
                    // Arrêter la détection
                    if (detectionInterval) {
                        clearInterval(detectionInterval);
                    }
                    FaceAuth.stopWebcam();

                    // Rediriger après 2 secondes
                    setTimeout(() => {
                        window.location.href = 'index.php?action=profile';
                    }, 2000);
                } else {
                    FaceAuth.showError(result.error || 'Erreur lors de l\'enregistrement.');
                    captureBtn.disabled = false;
                }
            });

            // Événement annulation
            cancelBtn.addEventListener('click', () => {
                const FaceAuth = window.FaceAuth;
                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }
                if (FaceAuth) {
                    FaceAuth.stopWebcam();
                }
                window.location.href = 'index.php?action=profile';
            });
        });

        // Détection continue
        function startContinuousDetection() {
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
            
            detectionInterval = setInterval(async () => {
                if (!video || video.readyState < 2) {
                    return;
                }
                
                try {
                    const FaceAuth = window.FaceAuth;
                    if (!FaceAuth) {
                        console.error('FaceAuth non disponible pour la détection');
                        return;
                    }
                    
                    const descriptor = await FaceAuth.detectFace(video, canvas);
                    
                    if (descriptor && descriptor.length > 0) {
                        currentDescriptor = descriptor;
                        if (statusIndicator) {
                            statusIndicator.textContent = '✓ Visage détecté';
                            statusIndicator.className = 'status-indicator detected';
                        }
                        const captureBtn = document.getElementById('captureBtn');
                        if (captureBtn) {
                            captureBtn.disabled = false;
                        }
                    } else {
                        currentDescriptor = null;
                        if (statusIndicator) {
                            statusIndicator.textContent = 'Détection en cours...';
                            statusIndicator.className = 'status-indicator detecting';
                        }
                        const captureBtn = document.getElementById('captureBtn');
                        if (captureBtn) {
                            captureBtn.disabled = true;
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la détection:', error);
                }
            }, 100); // Détection toutes les 100ms
        }

        // Nettoyage à la fermeture
        window.addEventListener('beforeunload', () => {
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
            if (window.FaceAuth && window.FaceAuth.stopWebcam) {
                window.FaceAuth.stopWebcam();
            }
        });
    </script>
</body>
</html>
