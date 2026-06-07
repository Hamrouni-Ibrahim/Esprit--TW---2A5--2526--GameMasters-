<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion par reconnaissance faciale | Gaming & Impact Social</title>
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js" onload="console.log('face-api.js chargé'); if(typeof faceapi !== 'undefined') { window.faceapi = faceapi; console.log('faceapi assigné à window.faceapi'); }" onerror="console.error('Erreur de chargement de face-api.js');"></script>
    <script>
        // Attendre que face-api.js soit chargé avant de charger face-auth.js
        function loadFaceAuthScript() {
            const checkFaceApi = setInterval(function() {
                if (typeof window.faceapi !== 'undefined' || typeof faceapi !== 'undefined') {
                    clearInterval(checkFaceApi);
                    if (typeof window.faceapi === 'undefined' && typeof faceapi !== 'undefined') {
                        window.faceapi = faceapi;
                    }
                    console.log('✓ face-api.js est disponible, chargement de face-auth.js...');
                    
                    // Charger face-auth.js
                    const script = document.createElement('script');
                    script.src = 'public/assets/js/face-auth.js';
                    script.onload = function() {
                        console.log('✓ face-auth.js chargé');
                    };
                    script.onerror = function() {
                        console.error('✗ Erreur de chargement de face-auth.js');
                    };
                    document.head.appendChild(script);
                }
            }, 100);
            
            // Timeout après 10 secondes
            setTimeout(function() {
                clearInterval(checkFaceApi);
                if (typeof window.faceapi === 'undefined' && typeof faceapi === 'undefined') {
                    console.error('✗ face-api.js n\'a pas été chargé dans les délais');
                }
            }, 10000);
        }
        
        // Démarrer le chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadFaceAuthScript);
        } else {
            loadFaceAuthScript();
        }
    </script>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            width: 100%;
        }

        .card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        h1 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 10px;
            font-size: 2em;
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
            border: 2px solid var(--accent-color);
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
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1em;
            font-weight: 600;
            background: rgba(0,209,255,0.9);
            color: #000;
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
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 30px auto;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .info-box {
            background: rgba(0,209,255,0.1);
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .icon {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .back-link {
            color: var(--accent-color);
            text-decoration: none;
            text-align: center;
            display: block;
            margin-top: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">🎭</div>
            <h1>Connexion Faciale</h1>
            <p class="subtitle">Positionnez votre visage devant la caméra</p>
            
            <div class="info-box">
                <p>Votre visage sera automatiquement reconnu une fois détecté</p>
            </div>

            <div id="faceError" class="message error" style="display: none;"></div>
            <div id="faceSuccess" class="message success" style="display: none;"></div>
            <div id="faceLoader" class="loader"></div>

            <div id="videoSection">
                <div class="video-container">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas"></canvas>
                    <div id="statusIndicator" class="status-indicator">🔍 Recherche de visage...</div>
                </div>

                <button id="cancelBtn" class="btn btn-secondary">Utiliser le mot de passe</button>
            </div>

            <a href="index.php?action=login" class="back-link">← Retour à la connexion</a>
        </div>
    </div>

    <script>
        let video, canvas, statusIndicator;
        let detectionInterval;
        let isVerifying = false;

        // Fonction pour attendre qu'un script soit chargé
        function waitForFaceAuth(maxWait = 20000) {
            return new Promise((resolve, reject) => {
                if (typeof window.FaceAuth !== 'undefined' && typeof window.FaceAuth.loadModels === 'function') {
                    console.log('FaceAuth est déjà chargé');
                    resolve(true);
                    return;
                }
                
                const startTime = Date.now();
                const checkInterval = setInterval(() => {
                    if (typeof window.FaceAuth !== 'undefined' && typeof window.FaceAuth.loadModels === 'function') {
                        clearInterval(checkInterval);
                        console.log('FaceAuth chargé avec succès');
                        resolve(true);
                    } else if (Date.now() - startTime > maxWait) {
                        clearInterval(checkInterval);
                        console.error('FaceAuth n\'a pas été chargé dans les délais');
                        reject(new Error('FaceAuth n\'a pas été chargé'));
                    }
                }, 100);
            });
        }

        window.addEventListener('load', async () => {
            try {
                // Attendre que FaceAuth soit chargé
                await waitForFaceAuth();
                
                if (typeof window.FaceAuth === 'undefined') {
                    throw new Error('FaceAuth n\'est pas disponible. Vérifiez que public/assets/js/face-auth.js est accessible.');
                }

                video = document.getElementById('video');
                canvas = document.getElementById('canvas');
                statusIndicator = document.getElementById('statusIndicator');
                const cancelBtn = document.getElementById('cancelBtn');

                // Charger les modèles
                if (window.FaceAuth.showLoader) {
                    window.FaceAuth.showLoader(true);
                }
                statusIndicator.textContent = '⏳ Chargement des modèles...';
                
                const loaded = await window.FaceAuth.loadModels();
                if (window.FaceAuth.showLoader) {
                    window.FaceAuth.showLoader(false);
                }

                if (!loaded) {
                    if (window.FaceAuth.showError) {
                        window.FaceAuth.showError('Impossible de charger les modèles de reconnaissance faciale.');
                    }
                    return;
                }
            } catch (error) {
                console.error('Erreur lors de l\'initialisation:', error);
                const errorDiv = document.getElementById('faceError');
                if (errorDiv) {
                    errorDiv.innerHTML = 'Erreur: ' + error.message + '<br><br>Vérifiez la console du navigateur (F12) pour plus de détails.';
                    errorDiv.style.display = 'block';
                }
                return;
            }

            // Démarrer la webcam
            statusIndicator.textContent = '📷 Activation de la caméra...';
            const FaceAuth = window.FaceAuth;
            const started = await FaceAuth.startWebcam(video);
            
            if (!started) {
                statusIndicator.textContent = '❌ Erreur d\'accès à la caméra';
                if (FaceAuth.showError) {
                    FaceAuth.showError('Impossible d\'accéder à la caméra. Vérifiez les permissions de votre navigateur.');
                }
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
                    statusIndicator.textContent = '🔍 Recherche de visage...';
                    // Démarrer la détection et vérification automatique
                    startAutoVerification();
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

            // Événement annulation
            cancelBtn.addEventListener('click', () => {
                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }
                const FaceAuth = window.FaceAuth;
                if (FaceAuth && FaceAuth.stopWebcam) {
                    FaceAuth.stopWebcam();
                }
                window.location.href = 'index.php?action=login';
            });
        });

        // Détection et vérification automatique
        function startAutoVerification() {
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
            
            detectionInterval = setInterval(async () => {
                if (isVerifying) return; // Éviter les vérifications multiples

                const FaceAuth = window.FaceAuth;
                if (!FaceAuth || !FaceAuth.detectFace) {
                    console.error('FaceAuth.detectFace n\'est pas disponible');
                    return;
                }

                try {
                    const descriptor = await FaceAuth.detectFace(video, canvas);
                    
                    if (descriptor && descriptor.length > 0) {
                        console.log('Visage détecté, début de la vérification...');
                        statusIndicator.textContent = '✓ Visage détecté - Vérification...';
                        isVerifying = true;

                        // Arrêter la détection temporairement
                        clearInterval(detectionInterval);

                        // Vérifier le visage
                        if (FaceAuth.showLoader) {
                            FaceAuth.showLoader(true);
                        }
                        
                        console.log('Appel de verifyFace avec descripteur de taille:', descriptor.length);
                        const result = await FaceAuth.verifyFace(descriptor);
                        console.log('Résultat de verifyFace:', result);
                        
                        if (FaceAuth.showLoader) {
                            FaceAuth.showLoader(false);
                        }

                        if (result && result.success) {
                            const username = result.user?.username || '';
                            if (FaceAuth.showSuccess) {
                                FaceAuth.showSuccess('✓ Connexion réussie ! Bienvenue ' + username + ' !');
                            }
                            statusIndicator.textContent = '✓ Connexion réussie !';
                            statusIndicator.style.background = 'rgba(0,255,136,0.9)';
                            
                            // Arrêter la webcam
                            if (FaceAuth.stopWebcam) {
                                FaceAuth.stopWebcam();
                            }

                            // Rediriger
                            setTimeout(() => {
                                const redirectUrl = result.redirect || 'index.php?controller=formation&action=userDashboard';
                                console.log('Redirection vers:', redirectUrl);
                                window.location.href = redirectUrl;
                            }, 1500);
                        } else {
                            const errorMsg = result?.error || result?.message || 'Visage non reconnu.';
                            console.log('Échec de la vérification:', errorMsg);
                            if (FaceAuth.showError) {
                                FaceAuth.showError(errorMsg);
                            }
                            statusIndicator.textContent = '❌ Non reconnu - Réessayez';
                            statusIndicator.style.background = 'rgba(255,68,68,0.9)';
                            
                            // Reprendre la détection après 2 secondes
                            setTimeout(() => {
                                if (FaceAuth.hideMessages) {
                                    FaceAuth.hideMessages();
                                }
                                statusIndicator.textContent = '🔍 Recherche de visage...';
                                statusIndicator.style.background = 'rgba(0,209,255,0.9)';
                                isVerifying = false;
                                startAutoVerification();
                            }, 2000);
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la détection/vérification:', error);
                    // Ne pas bloquer si une erreur survient, continuer à détecter
                }
            }, 500); // Détection toutes les 500ms (moins fréquent pour économiser ressources)
        }

        // Nettoyage à la fermeture
        window.addEventListener('beforeunload', () => {
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
            const FaceAuth = window.FaceAuth;
            if (FaceAuth && FaceAuth.stopWebcam) {
                FaceAuth.stopWebcam();
            }
        });
    </script>
</body>
</html>
