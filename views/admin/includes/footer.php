    <!-- Font Awesome pour les icônes audio -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Système Audio (sur toutes les pages admin) -->
    <script>
        // Prevent duplicate loading
        if (typeof AudioSystem === 'undefined') {
            const audioScript = document.createElement('script');
            audioScript.src = 'public/js/audio-system.js';
            document.head.appendChild(audioScript);
        }
    </script>
    
    <script>
        // Prevent duplicate loading - check both if variable exists AND if script tag exists
        (function() {
            // Check if script is already in DOM
            const existingScript = document.querySelector('script[src="public/js/templatemo-prism-scripts.js"]') || 
                                   document.querySelector('script[src*="templatemo-prism-scripts.js"]');
            
            if (!existingScript && typeof portfolioData === 'undefined') {
                const prismScript = document.createElement('script');
                prismScript.src = 'public/js/templatemo-prism-scripts.js';
                prismScript.setAttribute('data-loaded', 'true');
                prismScript.onerror = function() {
                    console.warn('templatemo-prism-scripts.js not found or already loaded');
                };
                document.head.appendChild(prismScript);
            }
        })();
    </script>
    <?php if (file_exists('public/js/validation.js')) { ?>
        <script src="public/js/validation.js"></script>
    <?php } ?>
    <script>
        // Initialize admin particles (stars) - RESTORED
        function initAdminParticles() {
            const particlesContainer = document.getElementById('adminParticles');
            if (!particlesContainer) return;
            
            const particleCount = 20; // More particles for admin pages
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'admin-particle';
                
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
            document.addEventListener('DOMContentLoaded', initAdminParticles);
        } else {
            initAdminParticles();
        }
        
        // Admin Page Transition Loading Animation - Fast & Discreet
        (function() {
            const transitionLoader = document.getElementById('page-transition-loader');
            
            if (!transitionLoader) {
                // Create loader if it doesn't exist
                const loader = document.createElement('div');
                loader.id = 'page-transition-loader';
                loader.className = 'page-transition-loader';
                loader.innerHTML = '<div class="admin-transition-spinner"></div>';
                document.body.appendChild(loader);
            }
            
            function showLoader() {
                const loader = document.getElementById('page-transition-loader');
                if (loader) {
                    loader.classList.remove('hide');
                    loader.classList.add('show');
                }
            }
            
            function hideLoader() {
                const loader = document.getElementById('page-transition-loader');
                if (loader) {
                    loader.classList.add('hide');
                    setTimeout(() => {
                        loader.classList.remove('show', 'hide');
                    }, 300);
                }
            }
            
            // Show loader on page navigation
            document.addEventListener('click', function(e) {
                const target = e.target.closest('a');
                
                // Check if it's a navigation link (not external, not anchor, not special links)
                if (target && target.href && 
                    !target.href.startsWith('javascript:') &&
                    !target.href.startsWith('mailto:') &&
                    !target.href.startsWith('tel:') &&
                    !target.hasAttribute('download') &&
                    !target.hasAttribute('target') &&
                    !target.classList.contains('no-loader') &&
                    !target.hasAttribute('onclick') &&
                    // Check if it's an internal link (same domain)
                    (target.href.includes(window.location.hostname) || 
                     target.href.startsWith('/') || 
                     target.href.startsWith('?'))) {
                    
                    // Don't show loader for hash links
                    if (target.hash && target.pathname === window.location.pathname) {
                        return;
                    }
                    
                    // Show loader immediately
                    showLoader();
                }
            }, true); // Use capture phase to catch early
            
            // Hide loader when page is fully loaded
            if (document.readyState === 'complete') {
                setTimeout(hideLoader, 200);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(hideLoader, 200);
                });
                document.addEventListener('DOMContentLoaded', function() {
                    // Hide loader after a short delay on initial load
                    setTimeout(hideLoader, 200);
                });
            }
            
            // Also handle form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.tagName === 'FORM' && !form.classList.contains('no-loader')) {
                    showLoader();
                }
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                showLoader();
                setTimeout(hideLoader, 300);
            });
            
        })();
    </script>
    </div> <!-- Close admin-wrapper -->
</body>
</html>




