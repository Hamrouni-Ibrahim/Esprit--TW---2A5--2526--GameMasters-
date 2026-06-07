<?php 
$pageTitle = 'Statut de ma Demande de Test - Game Master';
$currentPage = 'test';
include "views/front/includes/header.php"; 
?>

<!-- Content Section -->
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
        
        <h2 class="section-title">📊 Statut de ma Demande de Test</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="max-width: 800px; margin: 20px auto; padding: 15px; background: rgba(255, 51, 51, 0.1); border: 1px solid rgba(255, 51, 51, 0.3); border-radius: 10px; color: #ff6b6b;">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="max-width: 800px; margin: 20px auto; padding: 15px; background: rgba(0, 255, 88, 0.1); border: 1px solid rgba(0, 255, 88, 0.3); border-radius: 10px; color: #00ff88;">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div style="max-width: 800px; margin: 30px auto;">
            
            <?php 
            // Check if user has completed a test (only 1 test per account allowed)
            require_once "models/TestAttempt.php";
            $testAttemptModel = new TestAttempt();
            $completedAttempt = $testAttemptModel->getCompletedByUserId($userId);
            $hasCompletedTest = ($completedAttempt !== false);
            ?>
            
            <?php if (!$request): ?>
                <!-- No request yet -->
                <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 40px; text-align: center;">
                    <?php if ($hasCompletedTest): ?>
                        <div style="font-size: 4em; margin-bottom: 20px;">✅</div>
                        <h3 style="color: #00ff88; margin-bottom: 15px;">Test Déjà Complété</h3>
                        <p style="color: #a0a0a0; margin-bottom: 30px;">
                            Vous avez déjà passé le test QCM. Un seul test est autorisé par compte.
                        </p>
                        <a href="?controller=test&action=results&attempt_id=<?php echo $completedAttempt['id']; ?>" 
                           style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                            📊 Voir Mes Résultats
                        </a>
                    <?php else: ?>
                        <div style="font-size: 4em; margin-bottom: 20px;">📝</div>
                        <h3 style="color: #e879f9; margin-bottom: 15px;">Aucune demande enregistrée</h3>
                        <p style="color: #a0a0a0; margin-bottom: 30px;">
                            Vous n'avez pas encore fait de demande pour passer le test QCM. 
                            Créez une demande avec une lettre de motivation pour commencer.
                        </p>
                        <a href="?controller=test&action=requestAccess" 
                           style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(147, 51, 234, 0.4)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            ✉️ Créer une Demande
                        </a>
                    <?php endif; ?>
                </div>
                
            <?php else: ?>
                <!-- Request exists -->
                <?php 
                $statusColors = [
                    'pending' => ['bg' => 'rgba(255, 193, 7, 0.1)', 'border' => 'rgba(255, 193, 7, 0.3)', 'text' => '#ffc107', 'icon' => '⏳'],
                    'approved' => ['bg' => 'rgba(0, 255, 88, 0.1)', 'border' => 'rgba(0, 255, 88, 0.3)', 'text' => '#00ff88', 'icon' => '✅'],
                    'rejected' => ['bg' => 'rgba(255, 51, 51, 0.1)', 'border' => 'rgba(255, 51, 51, 0.3)', 'text' => '#ff6b6b', 'icon' => '❌']
                ];
                $statusLabels = [
                    'pending' => 'En Attente',
                    'approved' => 'Approuvée',
                    'rejected' => 'Rejetée'
                ];
                $statusInfo = $statusColors[$request['status']];
                ?>
                
                <div style="background: <?php echo $statusInfo['bg']; ?>; border: 1px solid <?php echo $statusInfo['border']; ?>; border-radius: 15px; padding: 30px; margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <span style="font-size: 2.5em;"><?php echo $statusInfo['icon']; ?></span>
                        <div>
                            <h3 style="color: <?php echo $statusInfo['text']; ?>; margin: 0; font-size: 1.5em;">
                                Statut: <?php echo $statusLabels[$request['status']]; ?>
                            </h3>
                            <p style="color: #a0a0a0; margin: 5px 0 0 0; font-size: 0.9em;">
                                Demande créée le <?php echo date('d/m/Y à H:i', strtotime($request['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 20px; margin-top: 20px;">
                        <h4 style="color: #e879f9; margin-top: 0; margin-bottom: 15px;">📄 Votre Lettre de Motivation</h4>
                        <p style="color: #e0e0e0; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($request['motivational_letter']); ?></p>
                    </div>
                    
                    <?php if ($request['admin_response']): ?>
                        <div style="background: rgba(232, 121, 249, 0.1); border-left: 4px solid #e879f9; border-radius: 8px; padding: 15px; margin-top: 20px;">
                            <h4 style="color: #e879f9; margin-top: 0; margin-bottom: 10px;">💬 Réponse de l'Administrateur</h4>
                            <p style="color: #e0e0e0; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($request['admin_response']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($request['status'] === 'approved'): 
                    // Check if user has completed the test
                    require_once "models/TestAttempt.php";
                    $testAttemptModel = new TestAttempt();
                    $userAttempt = $testAttemptModel->getByUserId($userId);
                    $hasCompletedThisTest = ($userAttempt && in_array($userAttempt['status'], ['completed', 'expired']));
                ?>
                    <?php if ($hasCompletedThisTest): ?>
                        <!-- Test completed -->
                        <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 10px; padding: 20px; margin-top: 30px;">
                            <p style="color: #00ffcc; margin-bottom: 15px;">
                                ✅ Vous avez terminé le test. Un seul test est autorisé par compte.
                            </p>
                            <a href="?controller=test&action=results&attempt_id=<?php echo $userAttempt['id']; ?>" 
                               style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                                📊 Voir Mes Résultats
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Test not completed yet, show take test button -->
                        <div style="text-align: center; margin-top: 30px;">
                            <a href="?controller=test&action=takeTest" 
                               style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #00ff88, #00cc88); border: none; border-radius: 10px; color: #0a0a0a; text-decoration: none; font-weight: 700; font-size: 1.1em; transition: all 0.3s;"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 255, 136, 0.4)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                🎯 Passer le Test Maintenant
                            </a>
                        </div>
                    <?php endif; ?>
                <?php elseif ($request['status'] === 'rejected'): ?>
                    <div style="text-align: center; margin-top: 30px;">
                        <p style="color: #a0a0a0; margin-bottom: 20px;">
                            Votre demande a été rejetée. Vous pouvez créer une nouvelle demande.
                        </p>
                        <a href="?controller=test&action=requestAccess" 
                           style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                            📝 Créer une Nouvelle Demande
                        </a>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; margin-top: 30px;">
                        <p style="color: #a0a0a0;">
                            Votre demande est en cours d'examen par l'administrateur. Vous serez notifié une fois qu'une décision aura été prise.
                        </p>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="?controller=formation&action=userDashboard" 
                   style="color: #a0a0a0; text-decoration: none; font-size: 0.9em;">
                    ← Retour au Tableau de Bord
                </a>
            </div>
        </div>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>

