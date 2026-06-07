/**
 * Système Audio pour Game Masters
 * Gère la musique de fond et les effets sonores
 */

class AudioSystem {
    constructor() {
        this.backgroundMusic = null;
        this.sounds = {};
        this.isMuted = localStorage.getItem('audioMuted') === 'true';
        this.volume = parseFloat(localStorage.getItem('audioVolume') || '0.3');
        this.musicEnabled = localStorage.getItem('musicEnabled') !== 'false';
        this.wasPlaying = sessionStorage.getItem('musicWasPlaying') === 'true';
        
        this.init();
    }
    
    init() {
        // Vérifier si les contrôles existent déjà (éviter les doublons)
        if (document.getElementById('audio-controls')) {
            return; // Contrôles déjà créés
        }
        
        // Créer l'audio de fond avec preload pour chargement rapide
        this.backgroundMusic = new Audio('public/assets/audio/background.mp3');
        this.backgroundMusic.loop = true;
        this.backgroundMusic.volume = this.volume;
        this.backgroundMusic.preload = 'auto';
        
        // Charger les effets sonores
        this.loadSounds();
        
        // Créer les contrôles UI
        this.createAudioControls();
        
        // Sauvegarder l'état avant de quitter la page
        this.setupPageUnload();
        
        // Si la musique était en cours, essayer de reprendre immédiatement
        if (this.wasPlaying && this.musicEnabled && !this.isMuted) {
            // Essayer de reprendre à la position sauvegardée
            const savedTime = parseFloat(sessionStorage.getItem('musicCurrentTime') || '0');
            
            // Fonction pour reprendre la musique
            const resumeMusic = () => {
                if (savedTime > 0 && savedTime < this.backgroundMusic.duration) {
                    this.backgroundMusic.currentTime = savedTime;
                }
                this.playMusic().catch(() => {
                    // Si échec (politique autoplay), attendre interaction
                    this.setupAutoPlay();
                });
            };
            
            // Essayer immédiatement si déjà chargé
            if (this.backgroundMusic.readyState >= 3) { // HAVE_FUTURE_DATA
                resumeMusic();
            } else {
                // Essayer dès que possible
                this.backgroundMusic.addEventListener('canplay', resumeMusic, { once: true });
                this.backgroundMusic.addEventListener('canplaythrough', resumeMusic, { once: true });
                
                // Fallback: essayer après un court délai
                setTimeout(() => {
                    if (this.backgroundMusic.readyState >= 2) { // HAVE_CURRENT_DATA
                        resumeMusic();
                    }
                }, 50);
                
                // Fallback supplémentaire: essayer après interaction utilisateur
                const tryResumeOnInteraction = () => {
                    resumeMusic();
                    document.removeEventListener('click', tryResumeOnInteraction);
                    document.removeEventListener('keydown', tryResumeOnInteraction);
                    document.removeEventListener('touchstart', tryResumeOnInteraction);
                };
                
                document.addEventListener('click', tryResumeOnInteraction, { once: true });
                document.addEventListener('keydown', tryResumeOnInteraction, { once: true });
                document.addEventListener('touchstart', tryResumeOnInteraction, { once: true });
            }
        } else {
            // Auto-play si activé (avec interaction utilisateur)
            this.setupAutoPlay();
        }
    }
    
    loadSounds() {
        // Liste des fichiers audio disponibles
        const soundsList = {
            click: 'public/assets/audio/click.mp3',
            success: 'public/assets/audio/success.mp3',
            // Utiliser click.mp3 comme remplacement pour les fichiers manquants
            error: 'public/assets/audio/click.mp3',
            notification: 'public/assets/audio/click.mp3',
            hover: 'public/assets/audio/click.mp3'
        };
        
        for (const [name, path] of Object.entries(soundsList)) {
            try {
                this.sounds[name] = new Audio(path);
                this.sounds[name].volume = this.volume * 0.5; // Effets plus doux que la musique
                
                // Gérer les erreurs de chargement silencieusement
                this.sounds[name].addEventListener('error', () => {
                    // Si le fichier n'existe pas, utiliser click.mp3 comme fallback
                    if (name !== 'click') {
                        try {
                            this.sounds[name] = new Audio('public/assets/audio/click.mp3');
                            this.sounds[name].volume = this.volume * 0.5;
                        } catch (e) {
                            // Si click.mp3 n'existe pas non plus, créer un Audio vide
                            this.sounds[name] = new Audio();
                        }
                    } else {
                        // Si click.mp3 lui-même n'existe pas, créer un Audio vide
                        this.sounds[name] = new Audio();
                    }
                });
            } catch (e) {
                // Si click.mp3 existe, l'utiliser comme fallback
                if (name !== 'click' && this.sounds['click']) {
                    this.sounds[name] = this.sounds['click'];
                } else {
                    // Sinon, créer un Audio vide
                    this.sounds[name] = new Audio();
                }
            }
        }
    }
    
    createAudioControls() {
        // Vérifier si les contrôles existent déjà
        if (document.getElementById('audio-controls')) {
            // Mettre à jour les contrôles existants
            this.updateExistingControls();
            return;
        }
        
        const controls = document.createElement('div');
        controls.id = 'audio-controls';
        controls.innerHTML = `
            <button id="audio-toggle" class="audio-btn" title="${this.musicEnabled ? 'Pause' : 'Play'} musique">
                <i class="fas fa-${this.musicEnabled ? 'pause' : 'play'}"></i>
            </button>
            <button id="audio-mute" class="audio-btn" title="${this.isMuted ? 'Activer' : 'Couper'} le son">
                <i class="fas fa-volume-${this.isMuted ? 'mute' : 'up'}"></i>
            </button>
            <div class="volume-control">
                <input type="range" id="volume-slider" min="0" max="100" value="${this.volume * 100}" 
                       title="Volume">
            </div>
        `;
        
        document.body.appendChild(controls);
        
        // Ajouter les styles (seulement si pas déjà ajoutés)
        if (!document.getElementById('audio-system-styles')) {
            this.addStyles();
        }
        
        // Ajouter les événements
        this.attachEvents();
    }
    
    updateExistingControls() {
        // Mettre à jour les icônes et états des contrôles existants
        const toggleBtn = document.getElementById('audio-toggle');
        const muteBtn = document.getElementById('audio-mute');
        const volumeSlider = document.getElementById('volume-slider');
        
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.className = `fas fa-${this.musicEnabled ? 'pause' : 'play'}`;
                toggleBtn.title = `${this.musicEnabled ? 'Pause' : 'Play'} musique`;
            }
        }
        
        if (muteBtn) {
            const icon = muteBtn.querySelector('i');
            if (icon) {
                icon.className = `fas fa-volume-${this.isMuted ? 'mute' : 'up'}`;
                muteBtn.title = `${this.isMuted ? 'Activer' : 'Couper'} le son`;
            }
        }
        
        if (volumeSlider) {
            volumeSlider.value = this.volume * 100;
        }
    }
    
    addStyles() {
        // Vérifier si les styles existent déjà
        if (document.getElementById('audio-system-styles')) {
            return;
        }
        
        const style = document.createElement('style');
        style.id = 'audio-system-styles';
        style.textContent = `
            #audio-controls {
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                background: rgba(26, 26, 46, 0.95);
                padding: 10px 15px;
                border-radius: 50px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(0, 209, 255, 0.3);
                z-index: 9999;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
            }
            
            #audio-controls:hover {
                border-color: rgba(0, 209, 255, 0.6);
                box-shadow: 0 8px 32px rgba(0, 209, 255, 0.2);
            }
            
            .audio-btn {
                background: transparent;
                border: none;
                color: #00d1ff;
                font-size: 18px;
                cursor: pointer;
                padding: 8px 12px;
                border-radius: 50%;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .audio-btn:hover {
                background: rgba(0, 209, 255, 0.1);
                transform: scale(1.1);
            }
            
            .audio-btn:active {
                transform: scale(0.95);
            }
            
            .volume-control {
                display: flex;
                align-items: center;
            }
            
            #volume-slider {
                width: 80px;
                height: 4px;
                -webkit-appearance: none;
                appearance: none;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 2px;
                outline: none;
                cursor: pointer;
            }
            
            #volume-slider::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 14px;
                height: 14px;
                background: #00d1ff;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            #volume-slider::-webkit-slider-thumb:hover {
                transform: scale(1.2);
                box-shadow: 0 0 10px rgba(0, 209, 255, 0.5);
            }
            
            #volume-slider::-moz-range-thumb {
                width: 14px;
                height: 14px;
                background: #00d1ff;
                border: none;
                border-radius: 50%;
                cursor: pointer;
            }
            
            @media (max-width: 768px) {
                #audio-controls {
                    bottom: 80px;
                    right: 10px;
                    padding: 8px 12px;
                }
                
                .volume-control {
                    display: none;
                }
            }
        `;
        
        document.head.appendChild(style);
    }
    
    attachEvents() {
        // Toggle musique
        document.getElementById('audio-toggle').addEventListener('click', () => {
            this.toggleMusic();
        });
        
        // Mute/Unmute
        document.getElementById('audio-mute').addEventListener('click', () => {
            this.toggleMute();
        });
        
        // Volume slider
        document.getElementById('volume-slider').addEventListener('input', (e) => {
            this.setVolume(e.target.value / 100);
        });
        
        // Effets sonores sur les boutons
        this.addSoundEffects();
    }
    
    setupAutoPlay() {
        // La musique ne peut démarrer qu'après interaction utilisateur (politique des navigateurs)
        const startMusic = () => {
            if (this.musicEnabled && !this.isMuted) {
                this.playMusic();
            }
        };
        
        // Si la musique était en cours, essayer de reprendre dès qu'il y a une interaction
        if (this.wasPlaying) {
            // Écouter tous les types d'interactions pour reprendre rapidement
            const interactions = ['click', 'keydown', 'touchstart', 'mousedown', 'pointerdown'];
            interactions.forEach(eventType => {
                document.addEventListener(eventType, startMusic, { once: true });
            });
        } else {
            // Sinon, attendre la première interaction normale
            document.addEventListener('click', startMusic, { once: true });
            document.addEventListener('keydown', startMusic, { once: true });
        }
    }
    
    setupPageUnload() {
        // Sauvegarder l'état de lecture avant de quitter la page
        const saveMusicState = () => {
            if (this.backgroundMusic && !this.backgroundMusic.paused) {
                sessionStorage.setItem('musicWasPlaying', 'true');
                sessionStorage.setItem('musicCurrentTime', this.backgroundMusic.currentTime.toString());
            } else {
                sessionStorage.setItem('musicWasPlaying', 'false');
            }
        };
        
        // Sauvegarder sur beforeunload
        window.addEventListener('beforeunload', saveMusicState);
        
        // Sauvegarder quand la page devient invisible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                saveMusicState();
            }
        });
        
        // Sauvegarder sur tous les clics de liens (AVANT la navigation)
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href && 
                !link.href.startsWith('javascript:') &&
                !link.href.startsWith('mailto:') &&
                !link.href.startsWith('tel:') &&
                !link.hasAttribute('download') &&
                (link.href.includes(window.location.hostname) || 
                 link.href.startsWith('/') || 
                 link.href.startsWith('?'))) {
                // Sauvegarder immédiatement avant la navigation
                saveMusicState();
            }
        }, true); // Capture phase pour intercepter tôt
        
        // Sauvegarder périodiquement pendant la lecture
        setInterval(() => {
            if (this.backgroundMusic && !this.backgroundMusic.paused) {
                sessionStorage.setItem('musicWasPlaying', 'true');
                sessionStorage.setItem('musicCurrentTime', this.backgroundMusic.currentTime.toString());
            }
        }, 1000); // Sauvegarder chaque seconde
    }
    
    playMusic() {
        if (this.backgroundMusic && !this.isMuted) {
            // Marquer que la musique devrait être en cours
            sessionStorage.setItem('musicWasPlaying', 'true');
            
            return this.backgroundMusic.play().then(() => {
                // Sauvegarder l'état de lecture
                sessionStorage.setItem('musicWasPlaying', 'true');
                return Promise.resolve();
            }).catch(err => {
                console.log('Impossible de lire la musique:', err);
                // Ne pas marquer comme arrêté si c'est juste une restriction autoplay
                if (err.name !== 'NotAllowedError') {
                    sessionStorage.setItem('musicWasPlaying', 'false');
                }
                return Promise.reject(err);
            });
        }
        return Promise.resolve();
    }
    
    pauseMusic() {
        if (this.backgroundMusic) {
            this.backgroundMusic.pause();
            // Marquer que la musique est en pause
            sessionStorage.setItem('musicWasPlaying', 'false');
        }
    }
    
    toggleMusic() {
        this.musicEnabled = !this.musicEnabled;
        localStorage.setItem('musicEnabled', this.musicEnabled);
        
        const btn = document.getElementById('audio-toggle');
        const icon = btn.querySelector('i');
        
        if (this.musicEnabled) {
            this.playMusic();
            icon.className = 'fas fa-pause';
            btn.title = 'Pause musique';
        } else {
            this.pauseMusic();
            icon.className = 'fas fa-play';
            btn.title = 'Play musique';
        }
    }
    
    toggleMute() {
        this.isMuted = !this.isMuted;
        localStorage.setItem('audioMuted', this.isMuted);
        
        const btn = document.getElementById('audio-mute');
        const icon = btn.querySelector('i');
        
        if (this.isMuted) {
            this.backgroundMusic.volume = 0;
            Object.values(this.sounds).forEach(sound => sound.volume = 0);
            icon.className = 'fas fa-volume-mute';
            btn.title = 'Activer le son';
        } else {
            this.backgroundMusic.volume = this.volume;
            Object.values(this.sounds).forEach(sound => sound.volume = this.volume * 0.5);
            icon.className = 'fas fa-volume-up';
            btn.title = 'Couper le son';
            
            if (this.musicEnabled) {
                this.playMusic();
            }
        }
    }
    
    setVolume(value) {
        this.volume = value;
        localStorage.setItem('audioVolume', value);
        
        if (!this.isMuted) {
            this.backgroundMusic.volume = value;
            Object.values(this.sounds).forEach(sound => sound.volume = value * 0.5);
        }
    }
    
    playSound(soundName) {
        if (this.sounds[soundName] && !this.isMuted) {
            try {
                // Vérifier que le fichier audio est valide (pas juste un Audio vide)
                if (this.sounds[soundName].src && this.sounds[soundName].src !== window.location.href) {
                    this.sounds[soundName].currentTime = 0;
                    this.sounds[soundName].play().catch(() => {
                        // Ignorer silencieusement les erreurs de lecture
                    });
                }
            } catch (e) {
                // Ignorer silencieusement les erreurs
            }
        }
    }
    
    addSoundEffects() {
        // Sons sur les boutons
        document.querySelectorAll('button, .btn, a.btn').forEach(element => {
            element.addEventListener('click', () => this.playSound('click'));
        });
        
        // Son hover sur les liens et boutons
        document.querySelectorAll('button, .btn, a').forEach(element => {
            element.addEventListener('mouseenter', () => this.playSound('hover'));
        });
        
        // Sons sur les notifications de succès/erreur
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.classList) {
                        if (node.classList.contains('success') || node.classList.contains('message-success')) {
                            this.playSound('success');
                        } else if (node.classList.contains('error') || node.classList.contains('message-error')) {
                            this.playSound('error');
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    }
}

// Initialiser le système audio quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    // Toujours créer une nouvelle instance (navigation complète détruit l'ancienne)
    // Mais restaurer l'état depuis sessionStorage
    window.audioSystem = new AudioSystem();
});

