<?php 
$pageTitle = 'Gestion des Questions QCM - Admin';
$currentPage = 'test_questions';
include "views/admin/includes/header.php"; 
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
    </div>
    <div class="admin-container">
        
        <div class="admin-header-section">
            <div>
                <h2>❓ Gestion des Questions QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Créer, modifier et supprimer les questions du test QCM</p>
            </div>
            <div>
                <a href="?controller=adminTest&action=addQuestion" class="btn" style="background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a;">
                    ➕ Ajouter une Question
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-form-container">
            <?php if (empty($questions)): ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <p style="color: #a0a0a0; font-size: 1.1em;">Aucune question trouvée.</p>
                    <a href="?controller=adminTest&action=addQuestion" class="btn" style="margin-top: 20px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a;">
                        ➕ Créer la première question
                    </a>
                </div>
            <?php else: ?>
                <div style="display: grid; gap: 20px;">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="card" style="padding: 25px; border-left: 4px solid #00ffcc;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                        <span style="background: rgba(0, 255, 204, 0.2); color: #00ffcc; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9em;">
                                            Question #<?php echo $question['id']; ?>
                                        </span>
                                        <?php if (isset($question['is_active']) && !$question['is_active']): ?>
                                            <span style="background: rgba(255, 51, 51, 0.2); color: #ff6b6b; padding: 5px 12px; border-radius: 20px; font-size: 0.85em;">
                                                ⚠️ Inactive
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 style="color: #fff; margin-bottom: 15px; font-size: 1.1em;">
                                        <?php echo htmlspecialchars($question['question']); ?>
                                    </h3>
                                    
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 15px;">
                                        <div style="padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px solid <?php echo ($question['correct_answer'] === 'a') ? '#00ff88' : 'rgba(255, 255, 255, 0.1)'; ?>;">
                                            <strong style="color: <?php echo ($question['correct_answer'] === 'a') ? '#00ff88' : '#a0a0a0'; ?>;">A)</strong>
                                            <span style="color: #fff; margin-left: 8px;"><?php echo htmlspecialchars($question['option_a']); ?></span>
                                            <?php if ($question['correct_answer'] === 'a'): ?>
                                                <span style="color: #00ff88; margin-left: 10px;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px solid <?php echo ($question['correct_answer'] === 'b') ? '#00ff88' : 'rgba(255, 255, 255, 0.1)'; ?>;">
                                            <strong style="color: <?php echo ($question['correct_answer'] === 'b') ? '#00ff88' : '#a0a0a0'; ?>;">B)</strong>
                                            <span style="color: #fff; margin-left: 8px;"><?php echo htmlspecialchars($question['option_b']); ?></span>
                                            <?php if ($question['correct_answer'] === 'b'): ?>
                                                <span style="color: #00ff88; margin-left: 10px;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px solid <?php echo ($question['correct_answer'] === 'c') ? '#00ff88' : 'rgba(255, 255, 255, 0.1)'; ?>;">
                                            <strong style="color: <?php echo ($question['correct_answer'] === 'c') ? '#00ff88' : '#a0a0a0'; ?>;">C)</strong>
                                            <span style="color: #fff; margin-left: 8px;"><?php echo htmlspecialchars($question['option_c']); ?></span>
                                            <?php if ($question['correct_answer'] === 'c'): ?>
                                                <span style="color: #00ff88; margin-left: 10px;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px solid <?php echo ($question['correct_answer'] === 'd') ? '#00ff88' : 'rgba(255, 255, 255, 0.1)'; ?>;">
                                            <strong style="color: <?php echo ($question['correct_answer'] === 'd') ? '#00ff88' : '#a0a0a0'; ?>;">D)</strong>
                                            <span style="color: #fff; margin-left: 8px;"><?php echo htmlspecialchars($question['option_d']); ?></span>
                                            <?php if ($question['correct_answer'] === 'd'): ?>
                                                <span style="color: #00ff88; margin-left: 10px;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($question['explanation'])): ?>
                                        <div style="padding: 12px; background: rgba(0, 255, 204, 0.1); border-radius: 8px; margin-top: 10px; border-left: 3px solid #00ffcc;">
                                            <strong style="color: #00ffcc;">💡 Explication:</strong>
                                            <p style="color: #a0a0a0; margin-top: 5px; margin-bottom: 0;"><?php echo htmlspecialchars($question['explanation']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div style="display: flex; gap: 10px; margin-left: 20px;">
                                    <a href="?controller=adminTest&action=editQuestion&id=<?php echo $question['id']; ?>" 
                                       class="btn btn-sm" 
                                       style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">
                                        ✏️ Modifier
                                    </a>
                                    <a href="?controller=adminTest&action=deleteQuestion&id=<?php echo $question['id']; ?>" 
                                       class="btn btn-sm" 
                                       style="background: rgba(255, 51, 51, 0.2); color: #ff6b6b;"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">
                                        🗑️ Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: 30px; padding: 20px; background: rgba(255, 255, 255, 0.05); border-radius: 10px; text-align: center;">
                    <p style="color: #a0a0a0; margin-bottom: 15px;">
                        <strong style="color: #00ffcc;">Total:</strong> <?php echo count($questions); ?> question(s)
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>





