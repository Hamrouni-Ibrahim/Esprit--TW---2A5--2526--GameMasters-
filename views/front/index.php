<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Game Masters | Gaming & Impact Social</title>
    <link rel="stylesheet" href="/game-masters/templates/front/templatefront.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/game-masters/public/assets/css/chatbot.css">
    <style>
        /* === CORRECTIONS SPÉCIFIQUES POUR GAME MASTERS === */
        
        /* Navigation corrigée */
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .logo-gaming {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            max-width: 200px;
            flex-shrink: 0;
        }

        .logo-image {
            width: 45px !important;
            height: 45px !important;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(0, 255, 204, 0.3);
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);
            flex-shrink: 0;
        }

        .logo-text-gaming {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #ff6b6b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
            position: relative;
            white-space: nowrap;
        }

        /* Boutons spéciaux */
        .admin-btn {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            padding: 8px 20px;
            border-radius: 8px;
            color: white !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(147, 51, 234, 0.4);
        }

        .logout-btn {
            color: #ff6b6b !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            color: #ff4757 !important;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
        }

        /* Styles pour le contenu */
        .hero-text h1 {
            background: linear-gradient(135deg, #ffffff 0%, #00ffcc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-top: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Section features améliorée */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffcc, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 255, 204, 0.3);
            box-shadow: 0 10px 30px rgba(0, 255, 204, 0.1);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color: #00ffcc;
            margin-bottom: 15px;
            font-size: 1.4em;
        }

        .feature-card p {
            color: #a0a0a0;
            line-height: 1.6;
        }

        /* Footer amélioré */
        .footer-gaming {
            background: #0a0e27;
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .footer-logo img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }

        .footer-logo span {
            font-size: 1.3em;
            font-weight: 700;
            background: linear-gradient(135deg, #00ffcc, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .nav-links {
                gap: 25px;
            }
            
            .logo-text-gaming {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .logo-image {
                width: 35px !important;
                height: 35px !important;
            }
            
            .logo-text-gaming {
                font-size: 18px;
            }
            
            .nav-links {
                display: none;
            }
            
            .hamburger {
                display: flex;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero-text h1 {
                font-size: 2.5em;
            }
        }

        @media (max-width: 480px) {
            .logo-text-gaming {
                font-size: 16px;
            }

            .hero-text h1 {
                font-size: 2em;
            }

            .user-info {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation avec Logo Corrigé -->
    <nav id="navbar">
        <div class="nav-container">
            <a href="index.php?action=home" class="logo-gaming">
                <img src="/game-masters/public/assets/img/logo.png" alt="Game Masters Logo" class="logo-image">
                <span class="logo-text-gaming">Game Masters</span>
            </a>
            
            <ul class="nav-links">
                <li><a href="index.php?action=home" class="active">Accueil</a></li>
                <li><a href="index.php?action=games">Jeux</a></li>
                <li><a href="index.php?action=search_games">🔍 Recherche</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?action=edit_profile">Mon Profil</a></li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="index.php?action=admin_dashboard" class="admin-btn">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?action=logout&redirect=home" class="logout-btn">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="index.php?action=login">Connexion</a></li>
                    <li><a href="index.php?action=register" class="cta-button" style="padding: 10px 25px; font-size: 14px; margin-left: 10px;">Inscription</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        
        <ul class="nav-links-mobile" id="navLinksMobile">
            <li><a href="index.php?action=home" class="active">Accueil</a></li>
            <li><a href="index.php?action=games">Jeux</a></li>
            <li><a href="index.php?action=search_games">🔍 Recherche</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="index.php?action=edit_profile">Mon Profil</a></li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="index.php?action=admin_dashboard">Administration</a></li>
                <?php endif; ?>
                <li><a href="index.php?action=logout&redirect=home" style="color: #ff6b6b;">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="index.php?action=login">Connexion</a></li>
                <li><a href="index.php?action=register">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        <div class="geometric-shapes">
            <div class="shape shape1"></div>
            <div class="shape shape2"></div>
            <div class="shape shape3"></div>
            <div class="shape shape4"></div>
            <div class="shape shape5"></div>
            <div class="shape shape6"></div>
        </div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1>Game Masters</h1>
                <p>Des jeux vidéo qui changent le monde. Rejoignez notre communauté de joueurs engagés pour un impact social positif.</p>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="index.php?action=register" class="cta-button">Commencer l'aventure</a>
                <?php else: ?>
                    <div class="user-info">
                        <p style="font-size: 1.3em; margin-bottom: 12px; color: #00ffcc;">Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> !</p>
                        <p style="margin-bottom: 8px;">🎯 Rôle: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                        <p style="margin-bottom: 20px;">📊 Profil: <?php echo (isset($_SESSION['profile_completed']) && $_SESSION['profile_completed']) ? '✅ Complété' : '❌ Incomplet'; ?></p>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <a href="index.php?action=games" class="cta-button">🎮 Découvrir les jeux</a>
                            <a href="index.php?action=edit_profile" class="cta-button" style="background: linear-gradient(135deg, #667eea, #764ba2);">👤 Mon Profil</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="hero-visual">
                <div class="city-container">
                    <div class="building building1" style="background: linear-gradient(180deg, #1a1f3a 0%, #0a0e27 100%); border-color: rgba(0, 255, 204, 0.4);">
                        <div class="building-fill" style="background: linear-gradient(180deg, transparent 0%, rgba(0, 255, 204, 0.7) 100%);"></div>
                        <div class="building-windows"></div>
                    </div>
                    <div class="building building2" style="background: linear-gradient(180deg, #1a1f3a 0%, #0a0e27 100%); border-color: rgba(255, 107, 107, 0.4);">
                        <div class="building-fill" style="background: linear-gradient(180deg, transparent 0%, rgba(255, 107, 107, 0.7) 100%);"></div>
                        <div class="building-windows"></div>
                    </div>
                    <div class="building building3" style="background: linear-gradient(180deg, #1a1f3a 0%, #0a0e27 100%); border-color: rgba(0, 204, 255, 0.4);">
                        <div class="building-fill" style="background: linear-gradient(180deg, transparent 0%, rgba(0, 204, 255, 0.7) 100%);"></div>
                        <div class="building-windows"></div>
                    </div>
                    <div class="building building4" style="background: linear-gradient(180deg, #1a1f3a 0%, #0a0e27 100%); border-color: rgba(147, 51, 234, 0.4); height: 180px; right: 15%;">
                        <div class="building-fill" style="background: linear-gradient(180deg, transparent 0%, rgba(147, 51, 234, 0.7) 100%);"></div>
                        <div class="building-windows"></div>
                    </div>
                    <div class="neon-line neon-line1"></div>
                    <div class="neon-line neon-line2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="dashboard-section" id="features">
        <div class="dashboard-container">
            <h2 class="section-title">Notre Mission</h2>
            <p style="text-align: center; max-width: 800px; margin: 0 auto 50px; color: #cccccc; font-size: 1.1em; line-height: 1.6;">
                Nous créons des expériences de jeu qui sensibilisent, éduquent et inspirent le changement social. 
                Chaque jeu aborde des enjeux importants comme la santé mentale, l'écologie, l'inclusion et l'éducation.
            </p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">🎮</div>
                        <div class="stat-title">Jeux Engagés</div>
                    </div>
                    <div class="stat-value">5+</div>
                    <div class="stat-description">Des expériences de jeu qui traitent de sujets sociaux et environnementaux importants avec une approche innovante.</div>
                    <div class="stat-chart">
                        <canvas class="mini-chart" id="miniChart1"></canvas>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">🌍</div>
                        <div class="stat-title">Impact Social</div>
                    </div>
                    <div class="stat-value">100%</div>
                    <div class="stat-description">Chaque jeu est conçu pour sensibiliser et inspirer l'action positive dans le monde réel grâce au pouvoir du gaming.</div>
                    <div class="stat-chart">
                        <canvas class="mini-chart" id="miniChart2"></canvas>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">👥</div>
                        <div class="stat-title">Communauté Active</div>
                    </div>
                    <div class="stat-value">1K+</div>
                    <div class="stat-description">Rejoignez une communauté de joueurs passionnés qui veulent faire la différence à travers le jeu vidéo.</div>
                    <div class="stat-chart">
                        <canvas class="mini-chart" id="miniChart3"></canvas>
                    </div>
                </div>
            </div>

            <!-- Features Grid Amélioré -->
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Éducation par le Jeu</h3>
                    <p>Apprenez des concepts complexes de manière ludique et engageante grâce à nos jeux éducatifs innovants.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💡</div>
                    <h3>Innovation Sociale</h3>
                    <p>Des mécaniques de jeu uniques conçues pour provoquer la réflexion et inspirer l'action positive.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🌟</div>
                    <h3>Communauté Engagée</h3>
                    <p>Rejoignez des milliers de joueurs qui partagent vos valeurs et votre passion pour le changement.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics Section -->
    <section class="analytics-section" id="analytics">
        <div class="dashboard-container">
            <h2 class="section-title">Notre Impact</h2>
            
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value">98%</div>
                    <div class="metric-label">Satisfaction Joueurs</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">25K+</div>
                    <div class="metric-label">Heures de Jeu</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">89%</div>
                    <div class="metric-label">Retention Mensuelle</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">4.9★</div>
                    <div class="metric-label">Note Moyenne</div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">📈 Engagement des Joueurs</h3>
                        <div class="chart-options">
                            <span class="chart-option active">2024</span>
                            <span class="chart-option">2023</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div class="bar-chart" id="barChart">
                            <div class="bar" style="height: 60%">
                                <span class="bar-value">75%</span>
                                <span class="bar-label">Jan</span>
                            </div>
                            <div class="bar" style="height: 80%">
                                <span class="bar-value">82%</span>
                                <span class="bar-label">Fév</span>
                            </div>
                            <div class="bar" style="height: 70%">
                                <span class="bar-value">78%</span>
                                <span class="bar-label">Mar</span>
                            </div>
                            <div class="bar" style="height: 90%">
                                <span class="bar-value">88%</span>
                                <span class="bar-label">Avr</span>
                            </div>
                            <div class="bar" style="height: 85%">
                                <span class="bar-value">85%</span>
                                <span class="bar-label">Mai</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">🌱 Impact Environnemental</h3>
                        <div class="chart-options">
                            <span class="chart-option active">Économies</span>
                            <span class="chart-option">Sensibilisation</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div class="bar-chart">
                            <div class="bar" style="height: 65%; background: linear-gradient(180deg, #4ecdc4 0%, #44a08d 100%);">
                                <span class="bar-value">65%</span>
                                <span class="bar-label">Énergie</span>
                            </div>
                            <div class="bar" style="height: 80%; background: linear-gradient(180deg, #ff6b6b 0%, #ff8e53 100%);">
                                <span class="bar-value">80%</span>
                                <span class="bar-label">Eau</span>
                            </div>
                            <div class="bar" style="height: 55%; background: linear-gradient(180deg, #45b7d1 0%, #96c93d 100%);">
                                <span class="bar-value">55%</span>
                                <span class="bar-label">Déchets</span>
                            </div>
                            <div class="bar" style="height: 72%; background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);">
                                <span class="bar-value">72%</span>
                                <span class="bar-label">CO2</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Final -->
    <section class="reports-section" id="cta">
        <div class="dashboard-container">
            <div style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: 3em; margin-bottom: 30px; background: linear-gradient(135deg, #00ffcc, #00ccff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Prêt à Changer le Monde ?
                </h2>
                <p style="font-size: 1.3em; color: #cccccc; margin-bottom: 40px; line-height: 1.6;">
                    Rejoignez notre communauté croissante de joueurs engagés. Ensemble, nous prouvons que le gaming peut être une force positive pour la société.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="index.php?action=register" class="cta-button" style="font-size: 1.2em; padding: 15px 40px;">Commencer Maintenant</a>
                        <a href="index.php?action=games" class="cta-button" style="font-size: 1.2em; padding: 15px 40px; background: linear-gradient(135deg, #667eea, #764ba2);">Découvrir les Jeux</a>
                    <?php else: ?>
                        <a href="index.php?action=games" class="cta-button" style="font-size: 1.2em; padding: 15px 40px;">Explorer les Jeux</a>
                        <a href="index.php?action=edit_profile" class="cta-button" style="font-size: 1.2em; padding: 15px 40px; background: linear-gradient(135deg, #667eea, #764ba2);">Modifier Mon Profil</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Amélioré -->
    <footer class="footer-gaming">
        <div class="footer-logo">
            <img src="/game-masters/public/assets/img/logo.png" alt="Game Masters">
            <span>Game Masters</span>
        </div>
        <p class="copyright">© 2024 Game Masters. Tous droits réservés. | Jeux vidéo avec impact social positif</p>
        <p style="color: #666; margin-top: 10px; font-size: 0.9em;">
            Construit avec passion pour un avenir meilleur grâce au gaming
        </p>
    </footer>

    <script src="/game-masters/templates/front/templatefront.js"></script>
    <!-- Système Audio & Chatbot -->
    <script src="/game-masters/public/assets/js/audio-system.js"></script>
    <script src="/game-masters/public/assets/js/chatbot.js"></script>
    <script>
        // Initialisation des mini-charts
        document.addEventListener('DOMContentLoaded', function() {
            // Simuler l'initialisation des charts
            setTimeout(() => {
                if(typeof drawMiniChart === 'function') {
                    drawMiniChart('miniChart1', '#00ffcc');
                    drawMiniChart('miniChart2', '#ff6b6b');
                    drawMiniChart('miniChart3', '#00ccff');
                }
            }, 100);

            // Animation des éléments au scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Animer les cartes de features
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `all 0.6s ease ${index * 0.2}s`;
                observer.observe(card);
            });

            // Animer les métriques
            const metrics = document.querySelectorAll('.metric-item');
            metrics.forEach((metric, index) => {
                metric.style.opacity = '0';
                metric.style.transform = 'translateY(20px)';
                metric.style.transition = `all 0.5s ease ${index * 0.1}s`;
                observer.observe(metric);
            });
        });
    </script>
</body>
</html>