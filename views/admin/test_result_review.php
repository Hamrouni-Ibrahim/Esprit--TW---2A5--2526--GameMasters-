<?php 
$pageTitle = 'Examiner le Résultat du Test - Admin';
$currentPage = 'test_attempts';
include "views/admin/includes/header.php"; 
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        
        <div class="admin-header-section">
            <div>
                <h2>👁️ Examiner le Résultat du Test</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Utilisateur: <strong><?php echo htmlspecialchars($attempt['username']); ?></strong></p>
            </div>
            <a href="?controller=adminTest&action=listAttempts" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-form-container">
            
            <!-- Test Summary Card -->
            <div class="card" style="margin-bottom: 25px;">
                <h3 style="color: #e879f9; margin-bottom: 20px;">📋 Résumé du Test</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Utilisateur</div>
                        <div style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($attempt['username']); ?></div>
                        <div style="color: #a0a0a0; font-size: 0.85em;"><?php echo htmlspecialchars($attempt['email']); ?></div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Score</div>
                        <div style="color: <?php echo $attempt['score'] >= 75 ? '#00ff88' : ($attempt['score'] >= 50 ? '#ffc107' : '#ff6b6b'); ?>; font-weight: 700; font-size: 1.5em;">
                            <?php echo number_format($attempt['score'], 1); ?>%
                        </div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Réponses Correctes</div>
                        <div style="color: #fff; font-weight: 600; font-size: 1.2em;">
                            <?php echo $attempt['correct_answers']; ?> / <?php echo $attempt['total_questions']; ?>
                        </div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Date</div>
                        <div style="color: #fff; font-weight: 600;">
                            <?php echo date('d/m/Y H:i', strtotime($attempt['started_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medal Assignment Info -->
            <?php 
            require_once "models/Medal.php";
            require_once "models/Game.php";
            require_once "config/database.php";
            $database = new Database();
            $conn = $database->getConnection();
            $gameModel = new Game($conn);
            $publishedGames = $gameModel->getPublishedGamesByUserId($attempt['user_id']);
            $gamesCount = count($publishedGames);
            
            $medalModel = new Medal();
            $currentMedal = $medalModel->getMedal($attempt['user_id']);
            
            // Calculate what medal will be assigned
            $assignedMedal = 'none';
            if ($attempt['score'] >= 30 && $attempt['score'] < 50 && $gamesCount >= 5) {
                $assignedMedal = 'bronze';
            } elseif ($attempt['score'] >= 50 && $attempt['score'] < 100 && $gamesCount >= 10) {
                $assignedMedal = 'silver';
            } elseif ($attempt['score'] == 100 && $gamesCount >= 10) {
                $assignedMedal = 'gold';
            }
            
            $medalIcons = ['bronze' => '🥉', 'silver' => '🥈', 'gold' => '🥇', 'none' => '❌'];
            $medalColors = [
                'bronze' => ['bg' => 'rgba(205, 127, 50, 0.2)', 'text' => '#cd7f32'],
                'silver' => ['bg' => 'rgba(192, 192, 192, 0.2)', 'text' => '#c0c0c0'],
                'gold' => ['bg' => 'rgba(255, 215, 0, 0.2)', 'text' => '#ffd700'],
                'none' => ['bg' => 'rgba(128, 128, 128, 0.2)', 'text' => '#808080']
            ];
            ?>
            
            <div class="card" style="margin-bottom: 25px; background: <?php echo $medalColors[$assignedMedal]['bg']; ?>; border: 1px solid <?php echo $medalColors[$assignedMedal]['text']; ?>;">
                <h3 style="color: <?php echo $medalColors[$assignedMedal]['text']; ?>; margin-bottom: 20px;">
                    🏅 Attribution de Médaille
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Score du Test</div>
                        <div style="color: #fff; font-weight: 600; font-size: 1.2em;"><?php echo number_format($attempt['score'], 1); ?>%</div>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Jeux Publiés</div>
                        <div style="color: #fff; font-weight: 600; font-size: 1.2em;"><?php echo $gamesCount; ?></div>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Médaille Actuelle</div>
                        <div style="color: #fff; font-weight: 600; font-size: 1.2em;">
                            <?php echo $medalIcons[$currentMedal] ?? '❌'; ?> <?php echo ucfirst($currentMedal); ?>
                        </div>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px;">
                        <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Médaille à Attribuer</div>
                        <div style="color: <?php echo $medalColors[$assignedMedal]['text']; ?>; font-weight: 700; font-size: 1.5em;">
                            <?php echo $medalIcons[$assignedMedal]; ?> <?php echo ucfirst($assignedMedal); ?>
                        </div>
                    </div>
                </div>
                
                <div style="background: rgba(0, 0, 0, 0.3); border-radius: 10px; padding: 15px;">
                    <strong style="color: #e879f9; display: block; margin-bottom: 10px;">Critères des Médailles:</strong>
                    <ul style="color: #e0e0e0; line-height: 2; margin: 0; padding-left: 20px;">
                        <li><strong style="color: #cd7f32;">🥉 Bronze:</strong> Score 30-50% ET 5+ jeux publiés</li>
                        <li><strong style="color: #c0c0c0;">🥈 Silver:</strong> Score 50-99% ET 10+ jeux publiés</li>
                        <li><strong style="color: #ffd700;">🥇 Gold:</strong> Score 100% ET 10+ jeux publiés</li>
                    </ul>
                    
                    <?php if ($assignedMedal === 'none'): ?>
                        <div style="margin-top: 15px; padding: 12px; background: rgba(255, 51, 51, 0.1); border-left: 4px solid #ff6b6b; border-radius: 5px;">
                            <strong style="color: #ff6b6b;">⚠️ Aucune médaille ne sera attribuée</strong>
                            <p style="color: #e0e0e0; margin: 5px 0 0 0; font-size: 0.9em;">
                                L'utilisateur ne remplit pas les critères requis pour recevoir une médaille. 
                                <?php if ($attempt['score'] < 30): ?>
                                    Score trop bas (minimum 30% requis).
                                <?php elseif ($gamesCount < 5): ?>
                                    Nombre de jeux publiés insuffisant (minimum 5 requis).
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div style="margin-top: 15px; padding: 12px; background: rgba(0, 255, 88, 0.1); border-left: 4px solid #00ff88; border-radius: 5px;">
                            <strong style="color: #00ff88;">✅ Cette médaille sera attribuée automatiquement lors de l'approbation</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Answers Details -->
            <div class="card" style="margin-bottom: 25px;">
                <h3 style="color: #e879f9; margin-bottom: 20px;">📋 Détails des Réponses</h3>
                
                <?php if (empty($answers)): ?>
                    <p style="color: #a0a0a0; text-align: center; padding: 20px;">
                        Aucune réponse enregistrée.
                    </p>
                <?php else: ?>
                    <?php foreach ($answers as $index => $answer): 
                        $questionNum = $index + 1;
                        $isCorrect = $answer['is_correct'];
                    ?>
                        <div style="background: rgba(0, 0, 0, 0.2); border-left: 4px solid <?php echo $isCorrect ? '#00ff88' : '#ff6b6b'; ?>; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
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
                                <div style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 5px;">Réponse de l'utilisateur:</div>
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

            <!-- Manual Medal Assignment -->
            <div class="card" style="margin-bottom: 25px; background: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.3);">
                <h3 style="color: #ffc107; margin-bottom: 20px;">🏅 Attribution Manuelle de Médaille</h3>
                <p style="color: #a0a0a0; margin-bottom: 15px; font-size: 0.9em;">
                    Vous pouvez attribuer manuellement une médaille à cet utilisateur, indépendamment des critères automatiques.
                </p>
                
                <form method="POST" action="?controller=adminTest&action=assignMedal&attempt_id=<?php echo $attemptId; ?>" style="display: flex; gap: 15px; align-items: end;">
                    <div style="flex: 1;">
                        <label for="manual_medal" style="color: #e0e0e0; display: block; margin-bottom: 8px; font-size: 0.9em;">Médaille à attribuer</label>
                        <select id="manual_medal" name="medal" class="form-control" style="background: rgba(26, 10, 46, 0.95); color: #ffffff; border: 1px solid rgba(255, 193, 7, 0.3);">
                            <option value="none" <?php echo $currentMedal === 'none' ? 'selected' : ''; ?>>❌ Aucune</option>
                            <option value="bronze" <?php echo $currentMedal === 'bronze' ? 'selected' : ''; ?>>🥉 Bronze</option>
                            <option value="silver" <?php echo $currentMedal === 'silver' ? 'selected' : ''; ?>>🥈 Silver</option>
                            <option value="gold" <?php echo $currentMedal === 'gold' ? 'selected' : ''; ?>>🥇 Gold</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                        💾 Attribuer la Médaille
                    </button>
                </form>
            </div>

            <!-- Approval Form -->
            <div class="card">
                <h3 style="color: #e879f9; margin-bottom: 20px;">✅ Approuver/Rejeter le Test</h3>
                
                <?php 
                $currentStatus = $approval ? $approval['status'] : 'pending';
                ?>
                
                <form method="POST" action="?controller=adminTest&action=reviewResult&attempt_id=<?php echo $attemptId; ?>">
                    <div class="admin-form-group">
                        <label for="status">Décision *</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?php echo $currentStatus === 'pending' ? 'selected' : ''; ?>>⏳ En Attente</option>
                            <option value="approved" <?php echo $currentStatus === 'approved' ? 'selected' : ''; ?>>✅ Approuver</option>
                            <option value="rejected" <?php echo $currentStatus === 'rejected' ? 'selected' : ''; ?>>❌ Rejeter</option>
                        </select>
                        <small style="color: #a0a0a0; display: block; margin-top: 5px;">
                            En approuvant, la médaille sera automatiquement attribuée selon les critères ci-dessus (sauf si vous avez déjà attribué une médaille manuellement).
                        </small>
                    </div>

                    <div class="admin-form-group">
                        <label for="admin_notes">Notes / Commentaires (optionnel)</label>
                        <textarea id="admin_notes" name="admin_notes" rows="6" 
                                  placeholder="Ajoutez des commentaires pour l'utilisateur..."><?php echo htmlspecialchars($approval['admin_notes'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="?controller=adminTest&action=listAttempts" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">💾 Enregistrer la Décision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>


