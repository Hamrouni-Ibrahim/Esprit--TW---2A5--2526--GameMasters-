<?php
$pageTitle = 'Mes Participations - Game Master';
$currentPage = 'participations';
require_once __DIR__ . '/../includes/header.php';
?>

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
            .participations-page-title {
                text-align: center;
                margin-bottom: 40px;
            }

            .participations-page-title h1 {
                font-size: 2.8em;
                background: linear-gradient(135deg, #00ffcc, #00ccff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 10px;
                font-weight: 700;
            }

            .participations-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 30px;
                padding: 20px 0;
            }

            .participation-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(0, 255, 204, 0.2);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
            }

            .participation-card-image {
                width: 100%;
                height: 200px;
                object-fit: cover;
                background: linear-gradient(135deg, rgba(0, 255, 204, 0.1) 0%, rgba(0, 204, 255, 0.1) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #00ffcc;
                font-size: 48px;
            }

            .participation-card-actions {
                display: flex;
                gap: 10px;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .btn-cancel-participation {
                padding: 10px 20px;
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                color: #ffffff;
                border: none;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                flex: 1;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-cancel-participation:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
            }

            .btn-download-ticket-participation {
                padding: 10px 20px;
                background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
                color: #ffffff;
                border: none;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-download-ticket-participation:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(147, 51, 234, 0.5);
            }

            .participation-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 40px rgba(0, 255, 204, 0.3);
                border-color: #00ffcc;
            }

            .participation-card-content {
                padding: 25px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .participation-card-title {
                font-size: 1.8em;
                font-weight: 700;
                color: #fff;
                margin-bottom: 15px;
                line-height: 1.3;
            }

            .participation-card-meta {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 20px;
            }

            .participation-meta-item {
                display: flex;
                align-items: center;
                gap: 10px;
                color: #00ffcc;
                font-size: 0.95em;
            }

            .participation-meta-item i {
                font-size: 18px;
            }

            .participation-card-description {
                color: #b0bec5;
                font-size: 1em;
                line-height: 1.6;
                margin-bottom: 20px;
                flex: 1;
            }

            .participation-badge {
                display: inline-block;
                background: rgba(76, 175, 80, 0.2);
                color: #4caf50;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 0.9em;
                font-weight: 600;
                border: 1px solid rgba(76, 175, 80, 0.4);
                margin-top: 10px;
            }

            .no-participations-message {
                text-align: center;
                color: #a0a0a0;
                font-size: 1.2em;
                padding: 50px 20px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 15px;
                margin-top: 30px;
            }
        </style>

        <div class="participations-page-title">
            <h1>🎫 Mes Participations</h1>
            <p>Consultez tous les événements auxquels vous avez participé.</p>
        </div>

        <?php if (empty($userParticipations)): ?>
            <div class="no-participations-message">
                <p>Vous n'avez pas encore participé à aucun événement.</p>
                <a href="?action=events" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0, 255, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    Découvrir les événements
                </a>
            </div>
        <?php else: ?>
            <div class="participations-grid">
                <?php foreach($userParticipations as $participation): 
                    $participationDate = new DateTime($participation['date_participation']);
                    
                    // Handle both new structure (date_debut, date_fin) and old structure (dateevent, duree)
                    if (isset($participation['date_debut']) && isset($participation['date_fin'])) {
                        $date_debut = new DateTime($participation['date_debut']);
                        $date_fin = new DateTime($participation['date_fin']);
                        $isPast = $date_fin < new DateTime();
                        $date_debut_str = $date_debut->format('d/m/Y H:i');
                        $date_fin_str = $date_fin->format('d/m/Y H:i');
                    } else {
                        // Old structure fallback
                        $date_debut = new DateTime($participation['dateevent']);
                        $date_fin = $date_debut;
                        $isPast = $date_debut < new DateTime();
                        $date_debut_str = $date_debut->format('d/m/Y');
                        $date_fin_str = 'N/A';
                    }
                ?>
                    <div class="participation-card">
                        <?php 
                        // Get event image
                        $eventImage = !empty($participation['image']) ? $participation['image'] : null;
                        ?>
                        <?php if ($eventImage): ?>
                            <img src="<?= htmlspecialchars($eventImage) ?>" 
                                 alt="<?= htmlspecialchars($participation['nom_evenet']) ?>" 
                                 class="participation-card-image"
                                 onerror="this.style.background='linear-gradient(135deg, rgba(0, 255, 204, 0.1) 0%, rgba(0, 204, 255, 0.1) 100%)'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.style.fontSize='48px'; this.src=''; this.outerHTML='<div class=\'participation-card-image\'><span>📅</span></div>';">
                        <?php else: ?>
                            <div class="participation-card-image">
                                <span>📅</span>
                            </div>
                        <?php endif; ?>
                        <div class="participation-card-content">
                            <h2 class="participation-card-title"><?= htmlspecialchars($participation['nom_evenet']) ?></h2>
                            
                            <div class="participation-card-meta">
                                <div class="participation-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Début : <?= $date_debut_str ?></span>
                                </div>
                                <div class="participation-meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Fin : <?= $date_fin_str ?></span>
                                </div>
                                <div class="participation-meta-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Inscrit le : <?= $participationDate->format('d/m/Y à H:i') ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($participation['description'])): ?>
                                <p class="participation-card-description">
                                    <?= htmlspecialchars($participation['description']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div>
                                <?php if ($isPast): ?>
                                    <span class="participation-badge">
                                        <i class="fas fa-history"></i> Événement terminé
                                    </span>
                                <?php else: ?>
                                    <span class="participation-badge">
                                        <i class="fas fa-calendar-check"></i> Événement à venir
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="participation-card-actions">
                                <a href="?action=download_ticket&participation_id=<?= $participation['id'] ?>" 
                                   class="btn-download-ticket-participation"
                                   style="flex: 1; text-align: center;">
                                    📥 Télécharger le ticket
                                </a>
                                <?php if (!$isPast): ?>
                                    <button class="btn-cancel-participation" 
                                            onclick="cancelParticipation(<?= $participation['id'] ?>, '<?= htmlspecialchars($participation['nom_evenet'], ENT_QUOTES) ?>')">
                                        ❌ Annuler
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function cancelParticipation(participationId, eventName) {
    if (confirm('Êtes-vous sûr de vouloir annuler votre participation à l\'événement "' + eventName + '" ?')) {
        // Create a form to submit the cancellation
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?action=cancel_participation';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'participation_id';
        input.value = participationId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
