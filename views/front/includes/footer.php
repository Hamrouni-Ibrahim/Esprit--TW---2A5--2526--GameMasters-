    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <p class="copyright">© <?php echo date('Y'); ?> Game Master. Tous droits réservés.
            | Conçu avec <a href="https://templatemo.com" rel="nofollow noopener" target="_blank">TemplateMo</a></p>
        </div>
    </footer>

    <!-- Unified Chatbot - Available on all pages -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="public/css/unified-chatbot.css">
    <script src="public/js/unified-chatbot.js" defer></script>

    <!-- Font Awesome pour les icônes audio -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Système Audio (sur toutes les pages) -->
    <script src="public/js/audio-system.js"></script>

    <script src="public/js/templatemo-graph-script.js"></script>
    <script>
        // Netflix-style loading screen animation with particles
        // Only show animation once per session (like Netflix)
        const hasSeenAnimation = sessionStorage.getItem('netflixAnimationShown');
        
        function playNetflixSound() {
            const audio = document.getElementById('netflix-intro-sound');
            if (audio) {
                // Reset audio to start
                audio.currentTime = 0;
                // Set volume (0.0 to 1.0) - adjust as needed
                audio.volume = 0.6;
                
                // Play the sound at the start of animation
                const playPromise = audio.play();
                
                if (playPromise !== undefined) {
                    playPromise.catch(function(error) {
                        // Handle autoplay restrictions
                        console.log('Audio autoplay prevented:', error);
                        // Try to play after user interaction if autoplay fails
                        document.addEventListener('click', function playOnce() {
                            audio.play().catch(() => {});
                            document.removeEventListener('click', playOnce);
                        }, { once: true });
                    });
                }
            }
        }
        
        function createAnimatedParticles() {
            const container = document.getElementById('animatedParticles');
            if (!container) return;
            
            const centerX = window.innerWidth / 2;
            const centerY = window.innerHeight / 2;
            const particleCount = 20;
            
            // Create particle lines moving toward center
            for (let i = 0; i < particleCount; i++) {
                const angle = (Math.PI * 2 * i) / particleCount;
                const distance = Math.random() * 400 + 300;
                const startX = centerX + Math.cos(angle) * distance;
                const startY = centerY + Math.sin(angle) * distance;
                
                // Random delay for staggered effect
                const delay = Math.random() * 0.5;
                
                // Create line particle
                const line = document.createElement('div');
                line.className = 'particle-line';
                line.style.left = startX + 'px';
                line.style.top = startY + 'px';
                line.style.animationDelay = delay + 's';
                line.style.setProperty('--end-x', (centerX - startX) + 'px');
                line.style.setProperty('--end-y', (centerY - startY) + 'px');
                line.style.transform = `rotate(${angle * 180 / Math.PI + 90}deg)`;
                container.appendChild(line);
            }
            
            // Create circular particles
            for (let i = 0; i < 15; i++) {
                const angle = (Math.PI * 2 * i) / 15;
                const distance = Math.random() * 350 + 250;
                const startX = centerX + Math.cos(angle) * distance;
                const startY = centerY + Math.sin(angle) * distance;
                
                const circle = document.createElement('div');
                circle.className = 'particle-circle';
                circle.style.left = startX + 'px';
                circle.style.top = startY + 'px';
                circle.style.animationDelay = (Math.random() * 0.6) + 's';
                circle.style.setProperty('--end-x', (centerX - startX) + 'px');
                circle.style.setProperty('--end-y', (centerY - startY) + 'px');
                container.appendChild(circle);
            }
            
            // Create spiral dots
            for (let i = 0; i < 12; i++) {
                const angle = (Math.PI * 2 * i) / 12;
                const radius = 200 + i * 15;
                const startX = Math.cos(angle) * radius;
                const startY = Math.sin(angle) * radius;
                
                const dot = document.createElement('div');
                dot.className = 'spiral-dot';
                dot.style.setProperty('--start-x', startX + 'px');
                dot.style.setProperty('--start-y', startY + 'px');
                dot.style.animationDelay = (i * 0.1) + 's';
                container.appendChild(dot);
            }
        }
        
        if (!hasSeenAnimation) {
            // Mark as seen immediately to prevent showing on subsequent page loads
            sessionStorage.setItem('netflixAnimationShown', 'true');
            
            // Audio should already be playing from inline script, but ensure it is
            const audio = document.getElementById('netflix-intro-sound');
            if (audio && audio.paused && audio.readyState >= 2) {
                // If somehow still paused and ready, play it
                audio.play().catch(function() {});
            }
            
            // Create particles when page loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    createAnimatedParticles();
                });
            } else {
                createAnimatedParticles();
            }
            
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const loader = document.getElementById('page-loader');
                    if (loader) {
                        loader.classList.add('hidden');
                        // Remove from DOM after animation completes
                        setTimeout(function() {
                            loader.style.display = 'none';
                        }, 800);
                    }
                }, 3800); // Show loader for ~3.8 seconds (particles 1.8s + logo 2s)
            });

            // SAFETY FALLBACK: Force hide initial loader after 6 seconds max
            setTimeout(function() {
                const loader = document.getElementById('page-loader');
                if (loader && !loader.classList.contains('hidden')) {
                    console.log('Force hiding initial loader via safety timeout');
                    loader.classList.add('hidden');
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 800);
                }
            }, 6000);
            
            
            // If page is already loaded (cached), still show animation but shorter
            if (document.readyState === 'complete') {
                createAnimatedParticles();
                setTimeout(function() {
                    const loader = document.getElementById('page-loader');
                    if (loader) {
                        loader.classList.add('hidden');
                        setTimeout(function() {
                            loader.style.display = 'none';
                        }, 800);
                    }
                }, 3500); // Show for ~3.5 seconds if cached
            }
        } else {
            // Hide loader immediately if animation was already shown
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.display = 'none';
            }
        }
        
        // Initialize front-end particles (stars)
        function initContentParticles() {
            const particlesContainer = document.getElementById('contentParticles');
            if (!particlesContainer) return;
            
            const particleCount = 20; // Number of particles
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'content-particle';
                
                // Random horizontal position
                particle.style.left = Math.random() * 100 + '%';
                
                // Start particles at random vertical positions
                particle.style.top = Math.random() * 100 + '%';
                
                // Random animation delay for natural movement
                particle.style.animationDelay = Math.random() * 20 + 's';
                
                // Random animation duration for variety (18-26 seconds)
                particle.style.animationDuration = (18 + Math.random() * 8) + 's';
                
                // Random size variation
                const size = 3 + Math.random() * 3;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Initialize particles when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initContentParticles);
        } else {
            initContentParticles();
        }
        
        // Show page transition loader when navigating between pages
        (function() {
            // Don't show transition loader on first page load (Netflix animation handles that)
            const isFirstLoad = !sessionStorage.getItem('pageNavigated');
            
            if (isFirstLoad) {
                // Mark that we've navigated at least once
                sessionStorage.setItem('pageNavigated', 'true');
            } else {
                // Show transition loader for subsequent page navigations
                const transitionLoader = document.getElementById('page-transition-loader');
                if (transitionLoader) {
                    transitionLoader.classList.add('show');
                    
                    // Hide when page is loaded
                    window.addEventListener('load', function() {
                        setTimeout(function() {
                            transitionLoader.classList.add('hide');
                            setTimeout(function() {
                                transitionLoader.classList.remove('show', 'hide');
                            }, 300);
                        }, 300); // Show for 300ms on navigation
                    });

                    // SAFETY FALLBACK: Force hide loader after 2 seconds if window.load fails to fire
                    setTimeout(function() {
                        if (transitionLoader.classList.contains('show')) {
                            console.log('Force hiding loader via safety timeout');
                            transitionLoader.classList.add('hide');
                            setTimeout(function() {
                                transitionLoader.classList.remove('show', 'hide');
                            }, 300);
                        }
                    }, 2000);
                    
                    // Also hide if page is already loaded
                    if (document.readyState === 'complete') {
                        setTimeout(function() {
                            transitionLoader.classList.add('hide');
                            setTimeout(function() {
                                transitionLoader.classList.remove('show', 'hide');
                            }, 300);
                        }, 200);
                    }
                }
            }
            
            // Show transition loader when clicking on internal links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    const href = link.getAttribute('href');
                    // Check if it's an internal link (not external, not mailto, not tel, etc.)
                    if (href && !href.startsWith('http') && !href.startsWith('//') && 
                        !href.startsWith('mailto:') && !href.startsWith('tel:') && 
                        !href.startsWith('#') && !link.hasAttribute('target') &&
                        !link.hasAttribute('onclick')) {
                        
                        // Check if it's actually navigating (not a same-page link)
                        try {
                            const currentUrl = new URL(window.location.href);
                            const linkUrl = new URL(link.href, window.location.href);
                            
                            // If it's a different page
                            if (linkUrl.pathname !== currentUrl.pathname || 
                                linkUrl.search !== currentUrl.search) {
                                
                                // Show transition loader immediately
                                const transitionLoader = document.getElementById('page-transition-loader');
                                if (transitionLoader) {
                                    transitionLoader.classList.remove('hide');
                                    transitionLoader.classList.add('show');
                                }
                            }
                        } catch(err) {
                            // Fallback for relative URLs
                            const currentPath = window.location.pathname + window.location.search;
                            if (href && (href !== currentPath || href.includes('?'))) {
                                const transitionLoader = document.getElementById('page-transition-loader');
                                if (transitionLoader) {
                                    transitionLoader.classList.remove('hide');
                                    transitionLoader.classList.add('show');
                                }
                            }
                        }
                    }
                }
            });
            
            // Also handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                const transitionLoader = document.getElementById('page-transition-loader');
                if (transitionLoader) {
                    transitionLoader.classList.remove('hide');
                    transitionLoader.classList.add('show');
                    setTimeout(function() {
                        transitionLoader.classList.add('hide');
                        setTimeout(function() {
                            transitionLoader.classList.remove('show', 'hide');
                        }, 300);
                    }, 300);
                }
            });
        })();
    </script>
</body>
</html>

