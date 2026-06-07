<?php
// Ensure variables are set
if (!isset($events)) {
    $events = [];
}
if (!isset($userParticipations)) {
    $userParticipations = [];
}
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
            .events-page-title {
                text-align: center;
                margin-bottom: 40px;
            }

            .events-page-title h1 {
                font-size: 2.8em;
                background: linear-gradient(135deg, #00ffcc, #00ccff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 10px;
                font-weight: 700;
            }

            .events-page-title p {
                color: #a0a0a0;
                font-size: 1.1em;
                line-height: 1.6;
            }

            .events-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 30px;
                padding: 20px 0;
            }

            .event-card {
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

            .event-card-image {
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

            .event-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 40px rgba(0, 255, 204, 0.3);
                border-color: #00ffcc;
            }

            .event-card-content {
                padding: 25px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .event-card-title {
                font-size: 1.8em;
                font-weight: 700;
                color: #fff;
                margin-bottom: 15px;
                line-height: 1.3;
            }

            .event-card-meta {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 20px;
            }

            .event-meta-item {
                display: flex;
                align-items: center;
                gap: 10px;
                color: #00ffcc;
                font-size: 0.95em;
            }

            .event-meta-item i,
            .event-meta-item span {
                font-size: 18px;
            }

            .event-card-description {
                color: #b0bec5;
                font-size: 1em;
                line-height: 1.6;
                margin-bottom: 20px;
                flex: 1;
            }

            .event-card-actions {
                display: flex;
                gap: 10px;
                margin-top: auto;
                padding-top: 20px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .btn-participate,
            .btn-participated {
                padding: 12px 25px;
                border: none;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                flex: 1;
                position: relative;
                overflow: hidden;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            .btn-participate {
                background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #0099ff 100%);
                background-size: 200% 200%;
                color: #0a0a0a;
                box-shadow: 
                    0 4px 15px rgba(0, 255, 204, 0.4),
                    0 0 0 1px rgba(0, 204, 255, 0.3) inset;
                animation: gradientShift 3s ease infinite;
            }

            .btn-participate::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                transition: left 0.5s ease;
            }

            .btn-participate:hover::before {
                left: 100%;
            }

            .btn-participate:hover {
                transform: translateY(-3px) scale(1.02);
                box-shadow: 
                    0 8px 25px rgba(0, 255, 204, 0.6),
                    0 0 0 1px rgba(0, 204, 255, 0.5) inset,
                    0 0 30px rgba(0, 255, 204, 0.4);
                background-position: right center;
            }

            .btn-participated {
                background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
                color: #ffffff;
                cursor: not-allowed;
                opacity: 0.8;
            }

            @keyframes gradientShift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            .no-events-message {
                text-align: center;
                color: #a0a0a0;
                font-size: 1.2em;
                padding: 50px 20px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 15px;
                margin-top: 30px;
            }

            .success-message,
            .error-message {
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
                font-weight: 600;
            }

            .success-message {
                background: rgba(76, 175, 80, 0.2);
                border: 1px solid #4CAF50;
                color: #4CAF50;
            }

            .error-message {
                background: rgba(255, 77, 77, 0.2);
                border: 1px solid #ff4d4d;
                color: #ff4d4d;
            }
        </style>

        <div class="events-page-title">
            <h1>📅 Événements à Venir</h1>
            <p>Découvrez nos prochains événements et participez pour ne rien manquer !</p>
        </div>

        <!-- Search Bar -->
        <div style="max-width: 600px; margin: 0 auto 40px; position: relative;">
            <form method="GET" action="?action=events" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="action" value="events">
                <input 
                    type="text" 
                    name="search" 
                    id="event_search"
                    placeholder="🔍 Rechercher un événement par nom ou description..." 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    style="
                        flex: 1;
                        padding: 15px 20px;
                        background: rgba(255, 255, 255, 0.05);
                        border: 2px solid rgba(0, 255, 204, 0.3);
                        border-radius: 12px;
                        color: #fff;
                        font-size: 16px;
                        outline: none;
                        transition: all 0.3s ease;
                        backdrop-filter: blur(10px);
                    "
                    onfocus="this.style.borderColor='#00ffcc'; this.style.boxShadow='0 0 20px rgba(0, 255, 204, 0.3)';"
                    onblur="this.style.borderColor='rgba(0, 255, 204, 0.3)'; this.style.boxShadow='none';"
                >
                <button 
                    type="submit" 
                    class="btn-search-event"
                    style="
                        padding: 15px 30px;
                        background: linear-gradient(135deg, #00ffcc 0%, #00ccff 100%);
                        color: #0a0a0a;
                        border: none;
                        border-radius: 12px;
                        font-size: 16px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        white-space: nowrap;
                        box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3);
                    "
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 255, 204, 0.5)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 255, 204, 0.3)';"
                >
                    Rechercher
                </button>
                <?php if (!empty($_GET['search'])): ?>
                    <a 
                        href="?action=events" 
                        style="
                            padding: 15px 20px;
                            background: rgba(255, 77, 77, 0.2);
                            color: #ff4d4d;
                            border: 2px solid rgba(255, 77, 77, 0.4);
                            border-radius: 12px;
                            text-decoration: none;
                            font-weight: 600;
                            transition: all 0.3s ease;
                        "
                        onmouseover="this.style.background='rgba(255, 77, 77, 0.3)'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.background='rgba(255, 77, 77, 0.2)'; this.style.transform='translateY(0)';"
                    >
                        ✕ Effacer
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (isset($_SESSION['event_success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['event_success']) ?>
                <?php if (isset($_SESSION['participation_id'])): ?>
                    <div style="margin-top: 15px;">
                        <a href="?action=download_ticket&participation_id=<?= $_SESSION['participation_id'] ?>" 
                           class="btn-download-ticket"
                           style="
                               display: inline-block;
                               padding: 12px 24px;
                               background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
                               color: #ffffff;
                               border: none;
                               border-radius: 10px;
                               text-decoration: none;
                               font-weight: 700;
                               font-size: 14px;
                               transition: all 0.3s ease;
                               box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);
                           "
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(147, 51, 234, 0.6)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(147, 51, 234, 0.4)';">
                            📥 Télécharger le ticket PDF
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php 
            unset($_SESSION['event_success']); 
            if (isset($_SESSION['participation_id'])) {
                unset($_SESSION['participation_id']);
            }
            ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['event_error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['event_error']) ?>
            </div>
            <?php unset($_SESSION['event_error']); ?>
        <?php endif; ?>

        <?php if (empty($events)): ?>
            <div class="no-events-message">
                <?php if (!empty($_GET['search'])): ?>
                    Aucun événement trouvé pour "<?= htmlspecialchars($_GET['search']) ?>". 
                    <a href="?action=events" style="color: #00ffcc; text-decoration: none; font-weight: bold;">Voir tous les événements</a>
                <?php else: ?>
                    Aucun événement disponible pour le moment. Revenez bientôt !
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (!empty($_GET['search'])): ?>
                <div style="text-align: center; margin-bottom: 20px; color: #00ffcc; font-size: 1.1em;">
                    <?= count($events) ?> événement(s) trouvé(s) pour "<?= htmlspecialchars($_GET['search']) ?>"
                </div>
            <?php endif; ?>
            <div class="events-grid">
                <?php foreach($events as $event): 
                    $isParticipated = isset($userParticipations[$event['idevent']]);
                    
                    // Handle both new structure (date_debut, date_fin) and old structure (dateevent, duree)
                    if (isset($event['date_debut']) && isset($event['date_fin'])) {
                        $date_debut = new DateTime($event['date_debut']);
                        $date_fin = new DateTime($event['date_fin']);
                        $isPast = $date_fin < new DateTime();
                        $date_debut_str = $date_debut->format('d/m/Y');
                        $heure_debut_str = $date_debut->format('H:i');
                        $date_fin_str = $date_fin->format('d/m/Y');
                        $heure_fin_str = $date_fin->format('H:i');
                        $duree_calculee = $date_debut->diff($date_fin);
                        $duree_str = '';
                        if ($duree_calculee->days > 0) {
                            $duree_str = $duree_calculee->days . ' jour(s)';
                        } else {
                            $duree_str = $duree_calculee->h . 'h' . ($duree_calculee->i > 0 ? $duree_calculee->i . 'min' : '');
                        }
                    } else {
                        // Old structure fallback
                        $date_debut = new DateTime($event['dateevent']);
                        $date_fin = $date_debut;
                        $isPast = $date_debut < new DateTime();
                        $date_debut_str = $date_debut->format('d/m/Y');
                        $heure_debut_str = '00:00';
                        $date_fin_str = 'N/A';
                        $heure_fin_str = '';
                        $duree_str = htmlspecialchars($event['duree'] ?? 'N/A');
                    }
                ?>
                    <div class="event-card">
                        <?php 
                        // Get event image path
                        $eventImage = !empty($event['image']) ? $event['image'] : null;
                        ?>
                        <?php if ($eventImage): ?>
                            <img src="<?= htmlspecialchars($eventImage) ?>" 
                                 alt="<?= htmlspecialchars($event['nom_evenet']) ?>" 
                                 class="event-card-image"
                                 onerror="this.style.background='linear-gradient(135deg, rgba(0, 255, 204, 0.1) 0%, rgba(0, 204, 255, 0.1) 100%)'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.style.fontSize='48px'; this.src=''; this.outerHTML='<div class=\'event-card-image\'><span>📅</span></div>';">
                        <?php else: ?>
                            <div class="event-card-image">
                                <span>📅</span>
                            </div>
                        <?php endif; ?>
                        <div class="event-card-content">
                            <h2 class="event-card-title"><?= htmlspecialchars($event['nom_evenet']) ?></h2>
                            
                            <div class="event-card-meta">
                                <div class="event-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Début: <?= $date_debut_str ?> à <?= $heure_debut_str ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Fin: <?= $date_fin_str ?><?= !empty($heure_fin_str) ? ' à ' . $heure_fin_str : '' ?></span>
                                </div>
                                <?php if (!empty($duree_str)): ?>
                                <div class="event-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Durée: <?= $duree_str ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <p class="event-card-description">
                                <?= htmlspecialchars($event['description'] ?? 'Aucune description disponible.') ?>
                            </p>
                            
                            <div class="event-card-actions">
                                <?php if ($isPast): ?>
                                    <button class="btn-participated" disabled>
                                        <i class="fas fa-check-circle"></i> Événement passé
                                    </button>
                                <?php elseif ($isParticipated): ?>
                                    <button class="btn-participated" disabled>
                                        <i class="fas fa-check-circle"></i> Vous participez déjà
                                    </button>
                                <?php else: ?>
                                    <button class="btn-participate" onclick="participateEvent(<?= $event['idevent'] ?>)">
                                        <i class="fas fa-user-plus"></i> Participer
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

<!-- Participation Modal -->
<div id="participationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: rgba(26, 26, 46, 0.95); padding: 30px; border-radius: 15px; width: 90%; max-width: 500px; position: relative; border: 1px solid rgba(0, 255, 204, 0.3); box-shadow: 0 20px 60px rgba(0, 255, 204, 0.2);">
        <span onclick="closeParticipationModal()" style="position: absolute; top: 15px; right: 20px; color: #00ffcc; cursor: pointer; font-size: 28px; font-weight: bold; transition: all 0.3s;" onmouseover="this.style.color='#ff4d4d'; this.style.transform='rotate(90deg)'" onmouseout="this.style.color='#00ffcc'; this.style.transform='rotate(0deg)'">&times;</span>
        <h3 style="color: #00ffcc; text-align: center; margin-bottom: 20px; font-size: 1.5em; text-shadow: 0 0 10px rgba(0, 255, 204, 0.5);">Participer à l'événement</h3>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <form id="participationForm" method="POST" action="?action=event_participate">
                <input type="hidden" id="participation_event_id" name="event_id">
                
                <div style="margin-bottom: 20px;">
                    <label for="participant_name" style="color: #00ffcc; display: block; margin-bottom: 8px; font-weight: 600;">Votre Nom *</label>
                    <input type="text" id="participant_name" name="name" required style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 8px; color: #fff; font-size: 1em;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="participant_email" style="color: #00ffcc; display: block; margin-bottom: 8px; font-weight: 600;">Votre Email *</label>
                    <input type="email" id="participant_email" name="email" required style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 8px; color: #fff; font-size: 1em;">
                </div>
                
                <button type="submit" style="width: 100%; margin-top: 20px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; padding: 15px; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 1em; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0, 255, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-check"></i> Confirmer ma participation
                </button>
            </form>
        <?php else: ?>
            <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #00ffcc; margin: 0 0 10px 0; font-weight: 600;">👤 Votre compte :</p>
                <p style="color: #fff; margin: 5px 0; font-size: 1em;"><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                <p style="color: #fff; margin: 5px 0; font-size: 1em;"><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
            </div>
            
            <form id="participationForm" method="POST" action="?action=event_participate">
                <input type="hidden" id="participation_event_id" name="event_id">
                
                <button type="submit" style="width: 100%; margin-top: 20px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; padding: 15px; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 1em; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0, 255, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-check"></i> Confirmer ma participation
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php // Footer already included by controller ?>

<script>
    function participateEvent(eventId) {
        document.getElementById('participation_event_id').value = eventId;
        document.getElementById('participationModal').style.display = 'flex';
    }

    function closeParticipationModal() {
        document.getElementById('participationModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('participationModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
