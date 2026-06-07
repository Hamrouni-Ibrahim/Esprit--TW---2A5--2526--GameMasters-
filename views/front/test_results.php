<?php 
$pageTitle = 'Résultats du Test - Game Master';
$currentPage = 'test';
include "views/front/includes/header.php"; 
?>
<style>
    @keyframes medalGlow {
        0% {
            filter: drop-shadow(0 0 8px rgba(232, 121, 249, 0.6));
            transform: scale(1);
        }
        100% {
            filter: drop-shadow(0 0 20px rgba(232, 121, 249, 1));
            transform: scale(1.05);
        }
    }
</style>

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
        
        <h2 class="section-title">📊 Résultats du Test</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="max-width: 900px; margin: 20px auto; padding: 15px; background: rgba(0, 255, 88, 0.1); border: 1px solid rgba(0, 255, 88, 0.3); border-radius: 10px; color: #00ff88;">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div style="max-width: 900px; margin: 0 auto;">
            
            <!-- Score Summary Card -->
            <div style="background: linear-gradient(135deg, rgba(232, 121, 249, 0.1), rgba(147, 51, 234, 0.1)); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 30px; margin-bottom: 30px; text-align: center;">
                
                <div style="font-size: 4em; margin-bottom: 15px;">
                    <?php 
                    $score = $attempt['score'];
                    if ($score >= 100) {
                        echo '🏆';
                    } elseif ($score >= 75) {
                        echo '🎉';
                    } elseif ($score >= 50) {
                        echo '👍';
                    } elseif ($score >= 30) {
                        echo '📚';
                    } else {
                        echo '💪';
                    }
                    ?>
                </div>
                
                <h2 style="color: #e879f9; margin: 0 0 10px 0; font-size: 2.5em;">
                    <?php echo number_format($score, 1); ?>%
                </h2>
                
                <p style="color: #a0a0a0; margin: 0 0 20px 0; font-size: 1.1em;">
                    <?php echo $attempt['correct_answers']; ?> réponses correctes sur <?php echo $attempt['total_questions']; ?> questions
                </p>
                
                <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-top: 20px;">
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px; min-width: 150px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Temps utilisé</div>
                        <div style="color: #00ffcc; font-size: 1.2em; font-weight: bold;">
                            <?php 
                            $minutes = floor($attempt['time_taken'] / 60);
                            $seconds = $attempt['time_taken'] % 60;
                            echo $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
                            ?>
                        </div>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px; min-width: 150px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Date</div>
                        <div style="color: #00ffcc; font-size: 1.2em; font-weight: bold;">
                            <?php echo date('d/m/Y H:i', strtotime($attempt['submitted_at'] ?? $attempt['started_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medal Info (fetch early so we can use it in approval message) -->
            <?php 
            require_once "models/Medal.php";
            require_once "models/Game.php";
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            $gameModel = new Game($conn);
            $publishedGames = $gameModel->getPublishedGamesByUserId($userId);
            $gamesCount = count($publishedGames);
            
            // Always fetch medal fresh from database (admin may have updated it)
            // DO NOT use session cache - always query database directly
            $medalModel = new Medal();
            $userMedal = $medalModel->getMedal($userId);
            
            // Also try direct database query as fallback to verify
            try {
                $directQuery = "SELECT medal FROM users WHERE id = ?";
                $directStmt = $conn->prepare($directQuery);
                $directStmt->execute([$userId]);
                $directResult = $directStmt->fetch(PDO::FETCH_ASSOC);
                $directMedal = $directResult ? ($directResult['medal'] ?? 'none') : 'none';
                
                error_log("🏆 Test Results - Medal from Medal model: " . var_export($userMedal, true));
                error_log("🏆 Test Results - Medal from direct query: " . var_export($directMedal, true));
                
                // Use direct query result if Medal model returned 'none' but direct query has a value
                if (($userMedal === 'none' || empty($userMedal)) && $directMedal !== 'none' && !empty($directMedal)) {
                    $userMedal = $directMedal;
                    error_log("🏆 Test Results - Using direct query result: " . $userMedal);
                }
            } catch (Exception $e) {
                error_log("🏆 Test Results - Error in direct query: " . $e->getMessage());
            }
            
            // Debug logging
            error_log("🏆 Test Results Page - User ID: " . $userId . ", Final Medal: " . var_export($userMedal, true));
            error_log("🏆 Test Results Page - Session medal (before update): " . ($_SESSION['medal'] ?? 'not set'));
            
            // Update session medal to keep it in sync
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['medal'] = $userMedal;
                error_log("🏆 Test Results Page - Session medal updated to: " . $userMedal);
            }
            ?>

            <!-- Approval Status -->
            <?php if ($approval): 
                $approvalStatusColors = [
                    'pending' => ['bg' => 'rgba(255, 193, 7, 0.1)', 'border' => 'rgba(255, 193, 7, 0.3)', 'text' => '#ffc107', 'label' => '⏳ En Attente d\'Approbation'],
                    'approved' => ['bg' => 'rgba(0, 255, 88, 0.1)', 'border' => 'rgba(0, 255, 88, 0.3)', 'text' => '#00ff88', 'label' => '✅ Approuvé'],
                    'rejected' => ['bg' => 'rgba(255, 51, 51, 0.1)', 'border' => 'rgba(255, 51, 51, 0.3)', 'text' => '#ff6b6b', 'label' => '❌ Rejeté']
                ];
                $approvalInfo = $approvalStatusColors[$approval['status']];
            ?>
                <div style="background: <?php echo $approvalInfo['bg']; ?>; border: 1px solid <?php echo $approvalInfo['border']; ?>; border-radius: 15px; padding: 25px; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span style="font-size: 2em;"><?php echo $approval['status'] === 'approved' ? '✅' : ($approval['status'] === 'rejected' ? '❌' : '⏳'); ?></span>
                        <div>
                            <h3 style="color: <?php echo $approvalInfo['text']; ?>; margin: 0; font-size: 1.3em;">
                                <?php echo $approvalInfo['label']; ?>
                            </h3>
                            <p style="color: #a0a0a0; margin: 5px 0 0 0; font-size: 0.9em;">
                                <?php if ($approval['status'] === 'pending'): ?>
                                    Votre test est en cours d'examen par l'administrateur. Une médaille vous sera attribuée si votre score et vos jeux publiés répondent aux critères.
                                <?php elseif ($approval['status'] === 'approved'): ?>
                                    Félicitations ! Votre test a été approuvé. <?php echo ($userMedal !== 'none') ? 'Vous avez reçu la médaille ' . ucfirst($userMedal) . ' !' : 'Vérifiez ci-dessous pour voir votre médaille.'; ?>
                                <?php else: ?>
                                    Votre test a été rejeté. Contactez l'administrateur pour plus d'informations.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($approval['admin_notes']): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(0, 0, 0, 0.2); border-radius: 10px;">
                            <h4 style="color: #e879f9; margin-top: 0; margin-bottom: 10px;">💬 Notes de l'Administrateur</h4>
                            <p style="color: #e0e0e0; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($approval['admin_notes']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 15px; padding: 25px; margin-bottom: 30px;">
                    <p style="color: #ffc107; margin: 0;">
                        ⏳ En attente de création de l'enregistrement d'approbation par l'administrateur.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Detailed Answers -->
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 30px; margin-bottom: 30px;">
                <h3 style="color: #e879f9; margin-top: 0; margin-bottom: 25px;">📋 Détails des Réponses</h3>
                
                <?php if (empty($answers)): ?>
                    <p style="color: #a0a0a0; text-align: center; padding: 20px;">
                        Aucune réponse enregistrée.
                    </p>
                <?php else: ?>
                    <?php foreach ($answers as $index => $answer): 
                        $questionNum = $index + 1;
                        $isCorrect = $answer['is_correct'];
                    ?>
                        <div style="background: rgba(0, 0, 0, 0.2); border-left: 4px solid <?php echo $isCorrect ? '#00ff88' : '#ff6b6b'; ?>; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <h4 style="color: #e879f9; margin: 0; font-size: 1.1em;">
                                    Question <?php echo $questionNum; ?>
                                </h4>
                                <span style="padding: 5px 12px; background: <?php echo $isCorrect ? 'rgba(0, 255, 88, 0.2)' : 'rgba(255, 51, 51, 0.2)'; ?>; color: <?php echo $isCorrect ? '#00ff88' : '#ff6b6b'; ?>; border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                    <?php echo $isCorrect ? '✓ Correct' : '✗ Incorrect'; ?>
                                </span>
                            </div>
                            
                            <p style="color: #e0e0e0; margin-bottom: 15px; line-height: 1.6;">
                                <?php echo htmlspecialchars($answer['question']); ?>
                            </p>
                            
                            <div style="margin-bottom: 10px;">
                                <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Votre réponse:</div>
                                <div style="color: <?php echo $isCorrect ? '#00ff88' : '#ff6b6b'; ?>; font-weight: 600;">
                                    <?php echo strtoupper($answer['user_answer']); ?>. <?php echo htmlspecialchars($answer['option_' . $answer['user_answer']] ?? ''); ?>
                                </div>
                            </div>
                            
                            <?php if (!$isCorrect): ?>
                                <div style="margin-top: 10px;">
                                    <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Bonne réponse:</div>
                                    <div style="color: #00ff88; font-weight: 600;">
                                        <?php echo strtoupper($answer['correct_answer']); ?>. <?php echo htmlspecialchars($answer['option_' . $answer['correct_answer']] ?? ''); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($answer['explanation'])): ?>
                                <div style="margin-top: 15px; padding: 12px; background: rgba(232, 121, 249, 0.1); border-radius: 8px;">
                                    <div style="color: #e879f9; font-size: 0.9em; font-weight: 600; margin-bottom: 5px;">💡 Explication:</div>
                                    <div style="color: #e0e0e0; font-size: 0.95em; line-height: 1.6;">
                                        <?php echo htmlspecialchars($answer['explanation']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Medal Info Display -->
            <div style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 15px; padding: 25px; margin-bottom: 30px;">
                <h3 style="color: #ffc107; margin-top: 0; margin-bottom: 20px;">🏅 Informations sur les Médailles</h3>
                
                <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <p style="color: #e0e0e0; margin: 0 0 15px 0;">
                        <strong style="color: #ffc107;">Votre Score:</strong> <?php echo number_format($score, 1); ?>%<br>
                        <strong style="color: #ffc107;">Jeux Publiés:</strong> <?php echo $gamesCount; ?>
                    </p>
                    
                    <div style="margin-top: 15px;">
                        <strong style="color: #e879f9; display: block; margin-bottom: 10px;">Critères pour les médailles:</strong>
                        <ul style="color: #e0e0e0; line-height: 2; margin: 0; padding-left: 20px;">
                            <li><strong style="color: #cd7f32;">🥉 Bronze:</strong> 30-50% + 5+ jeux publiés</li>
                            <li><strong style="color: #c0c0c0;">🥈 Silver:</strong> 50-99% + 10+ jeux publiés</li>
                            <li><strong style="color: #ffd700;">🥇 Gold:</strong> 100% + 10+ jeux publiés</li>
                        </ul>
                    </div>
                </div>
                
                <?php 
                // Normalize medal value - handle NULL, empty, or invalid values
                // First, let's see what we actually have
                $rawMedal = $userMedal;
                $medalType = gettype($userMedal);
                $medalLength = is_string($userMedal) ? strlen($userMedal) : 'N/A';
                
                // Debug: Show raw value on page (temporary - remove after debugging)
                echo "<!-- DEBUG: Raw medal value: " . htmlspecialchars(var_export($rawMedal, true)) . " -->";
                echo "<!-- DEBUG: Medal type: " . htmlspecialchars($medalType) . " -->";
                echo "<!-- DEBUG: Medal length: " . htmlspecialchars($medalLength) . " -->";
                
                // Normalize: trim whitespace, convert to lowercase for comparison
                $normalizedMedal = is_string($userMedal) ? strtolower(trim($userMedal)) : '';
                
                // Check if it's a valid medal
                $validMedals = ['bronze', 'silver', 'gold'];
                $displayMedal = in_array($normalizedMedal, $validMedals) ? $normalizedMedal : null;
                
                // Debug logging
                error_log("🏆 Test Results Display Check - Raw userMedal: " . var_export($rawMedal, true));
                error_log("🏆 Test Results Display Check - Normalized medal: " . var_export($normalizedMedal, true));
                error_log("🏆 Test Results Display Check - displayMedal: " . var_export($displayMedal, true));
                error_log("🏆 Test Results Display Check - Valid medals check: " . (in_array($normalizedMedal, $validMedals) ? 'YES' : 'NO'));
                
                // ALWAYS show medal section if we have ANY value (for debugging)
                if ($displayMedal || (!empty($rawMedal) && $rawMedal !== 'none')): 
                ?>
                    <div style="text-align: center; padding: 20px; background: rgba(232, 121, 249, 0.1); border-radius: 10px; border: 2px solid rgba(232, 121, 249, 0.3);">
                        <div style="font-size: 4em; margin-bottom: 10px; animation: medalGlow 2s ease-in-out infinite alternate;">
                            <?php 
                            $medalIcons = ['bronze' => '🥉', 'silver' => '🥈', 'gold' => '🥇'];
                            echo $medalIcons[$displayMedal] ?? '🏅';
                            ?>
                        </div>
                        <p style="color: #e879f9; font-size: 1.2em; font-weight: 600; margin: 0;">
                            Félicitations ! Vous avez reçu la médaille <strong><?php echo ucfirst($displayMedal); ?></strong> !
                        </p>
                        <?php if ($approval && $approval['status'] === 'approved'): ?>
                            <p style="color: #a0a0a0; font-size: 0.9em; margin-top: 10px; margin-bottom: 0;">
                                Médaille attribuée suite à l'approbation de votre test.
                            </p>
                        <?php else: ?>
                            <p style="color: #a0a0a0; font-size: 0.9em; margin-top: 10px; margin-bottom: 0;">
                                Médaille attribuée par l'administrateur.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else: 
                    error_log("🏆 Test Results - No medal to display. Raw userMedal was: " . var_export($rawMedal, true));
                    error_log("🏆 Test Results - Normalized was: " . var_export($normalizedMedal, true));
                ?>
                    <!-- DEBUG INFO (remove after fixing) -->
                    <div style="background: rgba(255, 0, 0, 0.1); border: 1px solid red; padding: 10px; margin-bottom: 10px; font-size: 12px;">
                        <strong>DEBUG:</strong> Raw medal: <?php echo htmlspecialchars(var_export($rawMedal, true)); ?><br>
                        Type: <?php echo htmlspecialchars($medalType); ?><br>
                        Normalized: <?php echo htmlspecialchars(var_export($normalizedMedal, true)); ?><br>
                        Display medal: <?php echo htmlspecialchars(var_export($displayMedal, true)); ?>
                    </div>
                    
                    <p style="color: #a0a0a0; text-align: center; margin: 0;">
                        <?php if ($approval && $approval['status'] === 'approved'): ?>
                            Votre test a été approuvé, mais aucune médaille n'a été attribuée car les critères ne sont pas remplis (score et/ou nombre de jeux publiés insuffisants).
                        <?php else: ?>
                            Votre médaille sera attribuée une fois que l'administrateur approuvera votre test et vérifiera vos jeux publiés.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Certificate Download -->
            <?php 
            // Use normalized medal for certificate check
            $certMedal = $displayMedal ?? (in_array($normalizedMedal, ['bronze', 'silver', 'gold']) ? $normalizedMedal : null);
            if ($approval && $approval['status'] === 'approved' && $certMedal): 
            ?>
                <div style="background: linear-gradient(135deg, rgba(232, 121, 249, 0.15), rgba(147, 51, 234, 0.15)); border: 2px solid rgba(232, 121, 249, 0.4); border-radius: 15px; padding: 30px; margin-bottom: 30px; text-align: center;">
                    <div style="font-size: 3em; margin-bottom: 15px;">📜</div>
                    <h3 style="color: #e879f9; margin: 0 0 15px 0; font-size: 1.5em;">Télécharger votre Certificat</h3>
                    <p style="color: #a0a0a0; margin: 0 0 20px 0; font-size: 1em;">
                        Félicitations ! Vous pouvez télécharger votre certificat officiel de réussite au test QCM.
                    </p>
                    <a href="?controller=certificate&action=downloadCertificate&attempt_id=<?php echo $attemptId; ?>" 
                       style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; font-size: 1.1em; transition: all 0.3s; box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);">
                        📥 Télécharger le Certificat PDF
                    </a>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="?controller=formation&action=userDashboard" 
                   style="display: inline-block; padding: 12px 30px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s; margin-right: 10px;">
                    ← Retour au Tableau de Bord
                </a>
                <a href="?controller=test&action=status" 
                   style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                    📊 Voir le Statut
                </a>
            </div>
        </div>
    </div>
</section>

<?php include "views/front/includes/footer.php"; ?>

