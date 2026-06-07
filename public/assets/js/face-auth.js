/**
 * face-auth.js
 * Gestion de la reconnaissance faciale avec face-api.js
 */

// Configuration
const MODELS_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
const FACE_MATCH_THRESHOLD = 0.6;

// État
let modelsLoaded = false;
let stream = null;

// Fonction helper pour obtenir faceapi
function getFaceApi() {
    if (typeof window.faceapi !== 'undefined') {
        return window.faceapi;
    } else if (typeof faceapi !== 'undefined') {
        window.faceapi = faceapi;
        return faceapi;
    }
    throw new Error('face-api.js n\'est pas chargé');
}

/**
 * Charger les modèles de face-api.js
 */
async function loadFaceModels() {
    if (modelsLoaded) return true;
    
    try {
        console.log('Chargement des modèles de reconnaissance faciale...');
        
        // Attendre que faceapi soit disponible (jusqu'à 10 secondes)
        let faceapiLib = null;
        for (let i = 0; i < 100; i++) {
            try {
                faceapiLib = getFaceApi();
                if (faceapiLib && faceapiLib.nets) {
                    break;
                }
            } catch (e) {
                // Pas encore chargé, attendre
            }
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        if (!faceapiLib || !faceapiLib.nets) {
            console.error('face-api.js n\'est pas disponible après 10 secondes');
            throw new Error('face-api.js n\'est pas chargé correctement. Vérifiez votre connexion internet.');
        }
        
        console.log('face-api.js trouvé, chargement des modèles...');
        await Promise.all([
            faceapiLib.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
            faceapiLib.nets.faceLandmark68Net.loadFromUri(MODELS_URL),
            faceapiLib.nets.faceRecognitionNet.loadFromUri(MODELS_URL)
        ]);
        
        modelsLoaded = true;
        console.log('Modèles chargés avec succès ✓');
        return true;
    } catch (error) {
        console.error('Erreur lors du chargement des modèles:', error);
        return false;
    }
}

/**
 * Démarrer la webcam
 */
async function startWebcam(videoElement) {
    try {
        // Vérifier que getUserMedia est disponible
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const errorMsg = 'Votre navigateur ne supporte pas l\'accès à la caméra. Veuillez utiliser un navigateur moderne (Chrome, Firefox, Edge, Safari).';
            console.error(errorMsg);
            if (typeof FaceAuth !== 'undefined' && FaceAuth.showError) {
                FaceAuth.showError(errorMsg);
            } else if (typeof showError !== 'undefined') {
                showError(errorMsg);
            }
            return false;
        }
        
        // Arrêter tout stream existant
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        // Réinitialiser la vidéo
        if (videoElement.srcObject) {
            videoElement.srcObject = null;
        }
        
        console.log('Demande d\'accès à getUserMedia...');
        
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false
        });
        
        console.log('Stream obtenu, attribution à la vidéo...');
        
        // S'assurer que les attributs nécessaires sont présents
        videoElement.setAttribute('autoplay', '');
        videoElement.setAttribute('muted', '');
        videoElement.setAttribute('playsinline', '');
        
        // Attribuer le stream
        videoElement.srcObject = stream;
        
        // Attendre que la vidéo soit prête et démarrer la lecture
        return new Promise((resolve) => {
            let resolved = false;
            
            const startPlayback = async () => {
                if (resolved) return;
                
                try {
                    console.log('Tentative de lecture de la vidéo...');
                    console.log('Video readyState:', videoElement.readyState);
                    console.log('Video videoWidth:', videoElement.videoWidth);
                    console.log('Video videoHeight:', videoElement.videoHeight);
                    
                    if (videoElement.readyState >= 2) {
                        await videoElement.play();
                        console.log('Vidéo en cours de lecture ✓');
                        if (!resolved) {
                            resolved = true;
                            resolve(true);
                        }
                    } else {
                        // Attendre un peu plus
                        setTimeout(startPlayback, 100);
                    }
                } catch (err) {
                    console.error('Erreur lors de la lecture de la vidéo:', err);
                    // Essayer quand même de continuer si le stream fonctionne
                    if (stream && stream.active) {
                        console.log('Stream actif, continuation...');
                        if (!resolved) {
                            resolved = true;
                            resolve(true);
                        }
                    } else {
                        if (!resolved) {
                            resolved = true;
                            resolve(false);
                        }
                    }
                }
            };
            
            // Gérer l'événement loadedmetadata
            videoElement.onloadedmetadata = () => {
                console.log('Métadonnées vidéo chargées');
                startPlayback();
            };
            
            // Si les métadonnées sont déjà chargées, démarrer immédiatement
            if (videoElement.readyState >= 1) {
                console.log('Métadonnées déjà disponibles');
                startPlayback();
            }
            
            // Timeout de sécurité (5 secondes)
            setTimeout(() => {
                if (!resolved) {
                    console.warn('Timeout lors du démarrage de la vidéo');
                    // Vérifier si le stream est actif
                    if (stream && stream.active && stream.getVideoTracks().length > 0) {
                        const track = stream.getVideoTracks()[0];
                        if (track.readyState === 'live') {
                            console.log('Stream actif, continuation malgré le timeout');
                            resolved = true;
                            resolve(true);
                        } else {
                            resolved = true;
                            resolve(false);
                        }
                    } else {
                        resolved = true;
                        resolve(false);
                    }
                }
            }, 5000);
        });
    } catch (error) {
        console.error('Erreur lors de l\'accès à la webcam:', error);
        
        let errorMsg = 'Impossible d\'accéder à la webcam.';
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
            errorMsg = 'Veuillez autoriser l\'accès à la webcam dans les paramètres de votre navigateur.';
        } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
            errorMsg = 'Aucune webcam détectée sur votre appareil.';
        } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
            errorMsg = 'La webcam est déjà utilisée par une autre application.';
        } else {
            errorMsg = 'Impossible d\'accéder à la webcam: ' + (error.message || error.name);
        }
        
        if (typeof FaceAuth !== 'undefined' && FaceAuth.showError) {
            FaceAuth.showError(errorMsg);
        } else if (typeof showError !== 'undefined') {
            showError(errorMsg);
        }
        
        return false;
    }
}

/**
 * Arrêter la webcam
 */
function stopWebcam() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

/**
 * Détecter et obtenir le descripteur facial
 */
async function detectFace(videoElement, canvasElement = null) {
    try {
        // Obtenir faceapi
        const faceapiLib = getFaceApi();
        if (!faceapiLib) {
            console.error('face-api.js n\'est pas disponible pour la détection');
            return null;
        }
        
        const detection = await faceapiLib
            .detectSingleFace(videoElement, new faceapiLib.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if (!detection) {
            return null;
        }
        
        // Dessiner sur le canvas si fourni
        if (canvasElement) {
            const displaySize = {
                width: videoElement.videoWidth,
                height: videoElement.videoHeight
            };
            faceapiLib.matchDimensions(canvasElement, displaySize);
            
            const resizedDetection = faceapiLib.resizeResults(detection, displaySize);
            const ctx = canvasElement.getContext('2d');
            
            // Nettoyer le canvas
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            
            // Dessiner le rectangle de détection
            ctx.strokeStyle = '#00ff88';
            ctx.lineWidth = 2;
            ctx.strokeRect(
                resizedDetection.detection.box.x,
                resizedDetection.detection.box.y,
                resizedDetection.detection.box.width,
                resizedDetection.detection.box.height
            );
            
            // Dessiner les landmarks
            ctx.fillStyle = '#00ff88';
            resizedDetection.landmarks.positions.forEach(point => {
                ctx.beginPath();
                ctx.arc(point.x, point.y, 2, 0, 2 * Math.PI);
                ctx.fill();
            });
        }
        
        return detection.descriptor;
    } catch (error) {
        console.error('Erreur lors de la détection:', error);
        return null;
    }
}

/**
 * Enregistrer le visage
 */
async function registerFace(descriptor) {
    try {
        const response = await fetch('index.php?action=save_face', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ descriptor: Array.from(descriptor) })
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Erreur lors de l\'enregistrement:', error);
        return {
            success: false,
            error: 'Erreur lors de l\'enregistrement du visage'
        };
    }
}

/**
 * Vérifier le visage pour la connexion
 */
async function verifyFace(descriptor) {
    try {
        console.log('Envoi de la requête de vérification...');
        const response = await fetch('index.php?action=verify_face', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ descriptor: Array.from(descriptor) })
        });
        
        console.log('Réponse reçue, statut:', response.status);
        
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        
        const result = await response.json();
        console.log('Résultat de la vérification:', result);
        return result;
    } catch (error) {
        console.error('Erreur lors de la vérification:', error);
        return {
            success: false,
            error: 'Erreur lors de la vérification du visage: ' + error.message
        };
    }
}

/**
 * Objet global pour faciliter l'utilisation
 */
window.FaceAuth = {
    loadModels: loadFaceModels,
    startWebcam: startWebcam,
    stopWebcam: stopWebcam,
    detectFace: detectFace,
    registerFace: registerFace,
    verifyFace: verifyFace,
    
    showError: function(message) {
        const errorDiv = document.getElementById('faceError');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
        console.error(message);
    },
    
    showSuccess: function(message) {
        const successDiv = document.getElementById('faceSuccess');
        if (successDiv) {
            successDiv.textContent = message;
            successDiv.style.display = 'block';
        }
        console.log(message);
    },
    
    hideMessages: function() {
        const errorDiv = document.getElementById('faceError');
        const successDiv = document.getElementById('faceSuccess');
        if (errorDiv) errorDiv.style.display = 'none';
        if (successDiv) successDiv.style.display = 'none';
    },
    
    showLoader: function(show) {
        const loader = document.getElementById('faceLoader');
        if (loader) {
            loader.style.display = show ? 'block' : 'none';
        }
    }
};

