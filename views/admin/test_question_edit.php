<?php 
$pageTitle = 'Modifier une Question QCM - Admin';
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
                <h2>✏️ Modifier une Question QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Question #<?php echo htmlspecialchars($question['id']); ?></p>
            </div>
            <a href="?controller=adminTest&action=manageQuestions" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-form-container">
            <div class="card">
                <form method="POST" action="?controller=adminTest&action=editQuestion&id=<?php echo $question['id']; ?>">
                    <div class="admin-form-group">
                        <label for="question">Question *</label>
                        <textarea id="question" name="question" rows="3" required 
                                  placeholder="Entrez la question..."><?php echo htmlspecialchars($question['question']); ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                        <div class="admin-form-group">
                            <label for="option_a">Option A *</label>
                            <input type="text" id="option_a" name="option_a" required 
                                   placeholder="Première option" 
                                   value="<?php echo htmlspecialchars($question['option_a']); ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_b">Option B *</label>
                            <input type="text" id="option_b" name="option_b" required 
                                   placeholder="Deuxième option" 
                                   value="<?php echo htmlspecialchars($question['option_b']); ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_c">Option C *</label>
                            <input type="text" id="option_c" name="option_c" required 
                                   placeholder="Troisième option" 
                                   value="<?php echo htmlspecialchars($question['option_c']); ?>">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_d">Option D *</label>
                            <input type="text" id="option_d" name="option_d" required 
                                   placeholder="Quatrième option" 
                                   value="<?php echo htmlspecialchars($question['option_d']); ?>">
                        </div>
                    </div>

                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label for="correct_answer">Réponse Correcte *</label>
                        <select id="correct_answer" name="correct_answer" required>
                            <option value="a" <?php echo ($question['correct_answer'] === 'a') ? 'selected' : ''; ?>>A</option>
                            <option value="b" <?php echo ($question['correct_answer'] === 'b') ? 'selected' : ''; ?>>B</option>
                            <option value="c" <?php echo ($question['correct_answer'] === 'c') ? 'selected' : ''; ?>>C</option>
                            <option value="d" <?php echo ($question['correct_answer'] === 'd') ? 'selected' : ''; ?>>D</option>
                        </select>
                    </div>

                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label for="explanation">Explication (Optionnel)</label>
                        <textarea id="explanation" name="explanation" rows="3" 
                                  placeholder="Expliquez pourquoi cette réponse est correcte..."><?php echo htmlspecialchars($question['explanation'] ?? ''); ?></textarea>
                    </div>

                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" 
                                   <?php echo (isset($question['is_active']) && $question['is_active']) ? 'checked' : ''; ?>
                                   style="width: 20px; height: 20px; cursor: pointer;">
                            <span>Question active (visible dans les tests)</span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; flex: 1;">
                            ✅ Enregistrer les Modifications
                        </button>
                        <a href="?controller=adminTest&action=manageQuestions" class="btn btn-secondary" style="flex: 0 0 auto;">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include "views/admin/includes/footer.php"; ?>

