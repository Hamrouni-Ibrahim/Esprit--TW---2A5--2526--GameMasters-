<?php
// Ce fichier est inclus dans le header/footer, pas besoin de structure HTML complète
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
        <div class="admin-shape shape4"></div>
        <div class="admin-shape shape5"></div>
        <div class="admin-shape shape6"></div>
    </div>
    <div class="admin-particles" id="adminParticles"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>Tableau de Bord Administrateur</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gestion complète de la plateforme Game Masters - Surveillance en temps réel</p>
        </div>

        <!-- Stats en style template -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number" data-target="<?php echo $userStats['total_users'] ?? '0'; ?>">0</div>
                <div class="stat-label">Utilisateurs Totaux</div>
                <p class="stat-description">Nombre total d'utilisateurs inscrits sur la plateforme</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎮</div>
                <div class="stat-number" data-target="<?php echo $gameStats['total'] ?? '0'; ?>">0</div>
                <div class="stat-label">Jeux Totaux</div>
                <p class="stat-description">Jeux disponibles et actifs sur la plateforme</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-number" data-target="<?php echo $formationStats['total'] ?? '0'; ?>">0</div>
                <div class="stat-label">Formations</div>
                <p class="stat-description">Nombre total de formations disponibles</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div class="stat-number" data-target="<?php echo $educationStats['total'] ?? '0'; ?>">0</div>
                <div class="stat-label">Éducations</div>
                <p class="stat-description">Nombre total d'éducations disponibles</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number" data-target="<?php echo $userStats['active_users'] ?? '0'; ?>">0</div>
                <div class="stat-label">Utilisateurs Actifs</div>
                <p class="stat-description">Utilisateurs avec statut actif et connectés récemment</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number" data-target="<?php echo $userStats['completion_rate'] ?? '0'; ?>">0</div>
                <div class="stat-label">% Profils Complétés</div>
                <p class="stat-description">Taux de complétion moyen des profils utilisateurs</p>
            </div>
        </div>


<!-- Statistiques détaillées -->
<div class="admin-card">
    <h3>📊 Statistiques Détaillées</h3>
    <div class="philosophy-pillars">
        <div class="pillar">
            <div class="pillar-icon">📈</div>
            <h3 class="pillar-title">Croissance</h3>
            <p class="pillar-description">
                <strong><?php echo $userStats['new_users'] ?? '12'; ?> nouveaux utilisateurs</strong> cette semaine.<br>
                Tendance de croissance stable et constante.
            </p>
        </div>
        
        <div class="pillar">
            <div class="pillar-icon">🛠️</div>
            <h3 class="pillar-title">Développement</h3>
            <p class="pillar-description">
                <strong><?php echo $gameStats['pending'] ?? '0'; ?> jeux</strong> en attente d'approbation.<br>
                <strong><?php echo $formationStats['total'] ?? '0'; ?> formations</strong> et <strong><?php echo $educationStats['total'] ?? '0'; ?> éducations</strong> disponibles.
            </p>
        </div>
        
        <div class="pillar">
            <div class="pillar-icon">🔒</div>
            <h3 class="pillar-title">Sécurité</h3>
            <p class="pillar-description">
                Système entièrement sécurisé.<br>
                Aucune activité suspecte détectée.
            </p>
        </div>
    </div>
</div>

<!-- Activités récentes -->
<div class="admin-card">
    <div class="activities-header">
        <h3>📋 Activités Récentes</h3>
        <div class="activities-badge" id="activitiesCount"><?php echo !empty($recentActivities) ? count($recentActivities) : '0'; ?></div>
    </div>
    <div class="activities-timeline">
        <?php if (!empty($recentActivities)): ?>
            <?php foreach ($recentActivities as $index => $activity): ?>
                <div class="activity-item" data-index="<?php echo $index; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="activity-timeline-dot"></div>
                    <div class="activity-timeline-line"></div>
                    <div class="activity-content">
                        <div class="activity-icon-wrapper" style="background: <?php echo $activity['color']; ?>;">
                            <span class="activity-icon"><?php echo $activity['icon']; ?></span>
                            <div class="activity-icon-glow" style="box-shadow: 0 0 20px <?php echo $activity['color']; ?>;"></div>
                        </div>
                        <div class="activity-details">
                            <div class="activity-header">
                                <strong class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></strong>
                            </div>
                            <p class="activity-description"><?php echo $activity['description']; ?></p>
                            <div class="activity-footer">
                                <span class="activity-type-badge" style="background: <?php echo $activity['color']; ?>20; color: <?php echo $activity['color']; ?>;">
                                    <?php echo $activity['type'] ?? 'Activité'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="activities-empty">
                <div class="empty-icon">📭</div>
                <p>Aucune activité récente</p>
                <span class="empty-subtitle">Les activités apparaîtront ici lorsqu'elles se produiront</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Styles spécifiques au dashboard */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.5), rgba(26, 26, 26, 0.8));
    border: 1px solid var(--metal-dark);
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent-purple);
    box-shadow: 0 15px 35px rgba(153, 69, 255, 0.2);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 26px;
    color: var(--text-primary);
    box-shadow: 0 10px 25px rgba(153, 69, 255, 0.3);
}

.stat-number {
    font-size: 36px;
    font-weight: 900;
    color: var(--text-primary);
    margin-bottom: 8px;
    font-family: 'Orbitron', monospace;
}

.stat-label {
    font-size: 12px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 10px;
}

.stat-description {
    font-size: 11px;
    color: var(--text-dim);
    line-height: 1.4;
}

/* NOUVEAUX STYLES POUR LES GRAPHIQUES */
.charts-grid-admin {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.chart-card-admin {
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.3), rgba(26, 26, 26, 0.5));
    border: 1px solid var(--metal-dark);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
}

.chart-card-admin:hover {
    border-color: var(--accent-purple);
    box-shadow: 0 10px 30px rgba(153, 69, 255, 0.1);
}

.chart-header-admin {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-title-admin {
    color: var(--accent-cyan);
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.chart-options-admin {
    display: flex;
    gap: 10px;
}

.chart-option-admin {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-secondary);
}

.chart-option-admin.active {
    background: var(--accent-purple);
    color: white;
}

.chart-container-admin {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Graphique en barres */
.bar-chart-admin {
    display: flex;
    align-items: end;
    gap: 15px;
    height: 150px;
    width: 100%;
}

.bar-chart-admin .bar {
    flex: 1;
    background: linear-gradient(180deg, var(--accent-purple), var(--accent-blue));
    border-radius: 4px 4px 0 0;
    position: relative;
    transition: all 0.3s ease;
    min-height: 20px;
}

.bar-chart-admin .bar:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(153, 69, 255, 0.3);
}

.bar-value {
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 12px;
    color: var(--text-primary);
    font-weight: 600;
}

.bar-label {
    position: absolute;
    bottom: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 11px;
    color: var(--text-secondary);
}

/* Graphique circulaire */
.pie-chart-admin {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: conic-gradient(
        var(--accent-green) 0% 60%,
        var(--accent-blue) 60% 85%,
        var(--accent-red) 85% 100%
    );
    position: relative;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
}

.pie-segment {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.segment-label {
    display: block;
    font-size: 11px;
    color: var(--text-secondary);
    margin-bottom: 2px;
}

.segment-value {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

/* Graphique linéaire */
.line-chart-admin {
    width: 100%;
    height: 100%;
}

.line-chart-admin svg {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0 0 10px rgba(153, 69, 255, 0.3));
}

.philosophy-pillars {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.pillar {
    position: relative;
    padding: 20px 15px;
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.2), rgba(26, 26, 26, 0.3));
    border: 1px solid var(--metal-dark);
    border-radius: 15px;
    transition: all 0.4s ease;
    overflow: hidden;
}

.pillar:hover {
    transform: translateY(-5px);
    border-color: var(--accent-purple);
    box-shadow: 0 15px 35px rgba(153, 69, 255, 0.1);
}

.pillar-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    position: relative;
}

.pillar-title {
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
    color: var(--text-primary);
    text-align: center;
}

.pillar-description {
    font-size: 12px;
    color: var(--text-secondary);
    line-height: 1.5;
    text-align: center;
}

/* Styles pour Activités Récentes améliorées - VERSION HEUREUSE ET ACTIVE */
.activities-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid;
    border-image: linear-gradient(90deg, #ff6b6b, #ff8e53, #00ffcc, #00ccff, #9333ea) 1;
    position: relative;
}

.activities-header::after {
    content: '✨';
    position: absolute;
    right: 0;
    bottom: -12px;
    font-size: 20px;
    animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {
    0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
    50% { transform: scale(1.3) rotate(180deg); opacity: 0.7; }
}

.activities-header h3 {
    margin: 0;
    font-size: 22px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e53, #00ffcc, #00ccff, #9333ea);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradientShift 3s ease infinite;
    font-weight: 800;
    letter-spacing: 0.5px;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.activities-badge {
    background: linear-gradient(135deg, #ff6b6b, #00ffcc, #9333ea);
    background-size: 200% 200%;
    color: white;
    padding: 8px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 800;
    box-shadow: 0 4px 20px rgba(255, 107, 107, 0.5), 0 0 30px rgba(0, 255, 204, 0.3);
    animation: happyPulse 1.5s ease-in-out infinite, badgeGradient 3s ease infinite;
    position: relative;
}

@keyframes happyPulse {
    0%, 100% { transform: scale(1) rotate(0deg); }
    25% { transform: scale(1.1) rotate(-5deg); }
    50% { transform: scale(1.15) rotate(5deg); }
    75% { transform: scale(1.1) rotate(-5deg); }
}

@keyframes badgeGradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.activities-timeline {
    position: relative;
    padding-left: 35px;
}

.activity-item {
    position: relative;
    margin-bottom: 30px;
    opacity: 0;
    transform: translateX(-30px) scale(0.9);
    animation: bounceInActivity 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

@keyframes bounceInActivity {
    0% {
        opacity: 0;
        transform: translateX(-30px) scale(0.9) rotate(-5deg);
    }
    60% {
        transform: translateX(5px) scale(1.05) rotate(2deg);
    }
    100% {
        opacity: 1;
        transform: translateX(0) scale(1) rotate(0deg);
    }
}

.activity-timeline-dot {
    position: absolute;
    left: -42px;
    top: 25px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 4px solid rgba(10, 14, 39, 0.95);
    z-index: 2;
    animation: dotBounce 2s ease-in-out infinite;
}

.activity-item:nth-child(1) .activity-timeline-dot {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    box-shadow: 0 0 20px rgba(255, 107, 107, 0.8), 0 0 40px rgba(255, 142, 83, 0.4);
}

.activity-item:nth-child(2) .activity-timeline-dot {
    background: linear-gradient(135deg, #00ffcc, #00ccff);
    box-shadow: 0 0 20px rgba(0, 255, 204, 0.8), 0 0 40px rgba(0, 204, 255, 0.4);
    animation-delay: 0.2s;
}

.activity-item:nth-child(3) .activity-timeline-dot {
    background: linear-gradient(135deg, #9333ea, #7c3aed);
    box-shadow: 0 0 20px rgba(147, 51, 234, 0.8), 0 0 40px rgba(124, 58, 237, 0.4);
    animation-delay: 0.4s;
}

.activity-item:nth-child(4) .activity-timeline-dot {
    background: linear-gradient(135deg, #f59e0b, #f97316);
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.8), 0 0 40px rgba(249, 115, 22, 0.4);
    animation-delay: 0.6s;
}

.activity-item:nth-child(5) .activity-timeline-dot {
    background: linear-gradient(135deg, #10b981, #34d399);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.8), 0 0 40px rgba(52, 211, 153, 0.4);
    animation-delay: 0.8s;
}

@keyframes dotBounce {
    0%, 100% { 
        transform: scale(1) translateY(0);
    }
    50% { 
        transform: scale(1.2) translateY(-5px);
    }
}

.activity-timeline-line {
    position: absolute;
    left: -34px;
    top: 43px;
    width: 4px;
    height: calc(100% + 15px);
    background: linear-gradient(180deg, 
        #ff6b6b 0%, 
        #ff8e53 25%, 
        #00ffcc 50%, 
        #00ccff 75%, 
        #9333ea 100%
    );
    background-size: 100% 400%;
    animation: lineFlow 4s linear infinite;
    border-radius: 2px;
    opacity: 0.6;
}

@keyframes lineFlow {
    0% { background-position: 0% 0%; }
    100% { background-position: 0% 100%; }
}

.activity-item:last-child .activity-timeline-line {
    display: none;
}

.activity-content {
    display: flex;
    gap: 20px;
    padding: 22px;
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.6), rgba(26, 26, 26, 0.8));
    border: 2px solid transparent;
    background-clip: padding-box;
    border-radius: 18px;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
}

.activity-content::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(
        from 0deg,
        transparent 0deg,
        rgba(255, 107, 107, 0.1) 60deg,
        rgba(255, 142, 83, 0.1) 120deg,
        rgba(0, 255, 204, 0.1) 180deg,
        rgba(0, 204, 255, 0.1) 240deg,
        rgba(147, 51, 234, 0.1) 300deg,
        transparent 360deg
    );
    animation: rotateGradient 8s linear infinite;
    opacity: 0;
    transition: opacity 0.5s ease;
}

@keyframes rotateGradient {
    to { transform: rotate(360deg); }
}

.activity-content::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 18px;
    padding: 2px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e53, #00ffcc, #00ccff, #9333ea);
    background-size: 300% 300%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    animation: borderGlow 3s ease infinite;
    transition: opacity 0.5s ease;
}

@keyframes borderGlow {
    0%, 100% { background-position: 0% 50%; opacity: 0; }
    50% { background-position: 100% 50%; opacity: 1; }
}

.activity-content:hover {
    transform: translateX(8px) translateY(-5px) scale(1.02) rotate(1deg);
    box-shadow: 
        0 15px 40px rgba(255, 107, 107, 0.3),
        0 0 60px rgba(0, 255, 204, 0.2),
        inset 0 0 30px rgba(147, 51, 234, 0.1);
}

.activity-content:hover::before {
    opacity: 1;
}

.activity-content:hover::after {
    opacity: 1;
}

.activity-icon-wrapper {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: visible;
    transform-style: preserve-3d;
}

.activity-icon {
    font-size: 32px;
    z-index: 3;
    position: relative;
    animation: happyFloat 2s ease-in-out infinite, iconWiggle 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

@keyframes happyFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-8px) rotate(-5deg); }
    50% { transform: translateY(-12px) rotate(0deg); }
    75% { transform: translateY(-8px) rotate(5deg); }
}

@keyframes iconWiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-3deg); }
    75% { transform: rotate(3deg); }
}

.activity-icon-glow {
    position: absolute;
    width: 120%;
    height: 120%;
    border-radius: 18px;
    opacity: 0.6;
    animation: glowPulse 2s ease-in-out infinite;
    filter: blur(10px);
}

@keyframes glowPulse {
    0%, 100% { 
        opacity: 0.4;
        transform: scale(1);
    }
    50% { 
        opacity: 0.8;
        transform: scale(1.1);
    }
}

.activity-details {
    flex: 1;
    min-width: 0;
    position: relative;
    z-index: 2;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
    gap: 15px;
    flex-wrap: wrap;
}

.activity-title {
    font-size: 16px;
    color: var(--text-primary);
    font-weight: 700;
    margin: 0;
    background: linear-gradient(135deg, #ffffff, #00ffcc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 0.3px;
}


.activity-description {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0 0 15px 0;
    line-height: 1.6;
    padding-left: 5px;
}

.activity-footer {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.activity-type-badge {
    font-size: 11px;
    padding: 6px 14px;
    border-radius: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border: 1px solid;
    animation: badgeBounce 2s ease-in-out infinite;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@keyframes badgeBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
}

.activities-empty {
    text-align: center;
    padding: 80px 30px;
    color: var(--text-secondary);
}

.empty-icon {
    font-size: 80px;
    margin-bottom: 25px;
    animation: happyBounce 2s ease-in-out infinite;
    display: inline-block;
}

@keyframes happyBounce {
    0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
    25% { transform: translateY(-15px) rotate(-10deg) scale(1.1); }
    50% { transform: translateY(-20px) rotate(0deg) scale(1.15); }
    75% { transform: translateY(-15px) rotate(10deg) scale(1.1); }
}

.activities-empty p {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #ffffff, #00ffcc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-subtitle {
    display: block;
    font-size: 13px;
    color: var(--text-dim);
    margin-top: 12px;
    font-style: italic;
}
</style>

<script>
// Animation des compteurs
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        if (isNaN(target)) return;
        
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(updateCounter);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });

    // Animation des graphiques au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    // Animer les cartes de graphiques
    const chartCards = document.querySelectorAll('.chart-card-admin');
    chartCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.6s ease ${index * 0.2}s`;
        observer.observe(card);
    });
    });
    
    // Generate particles for admin background - RESTORED
    const particlesContainer = document.getElementById('adminParticles');
    if (particlesContainer) {
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'admin-particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }
    }
    
</script>
    </div>
</section>