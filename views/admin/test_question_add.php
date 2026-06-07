<?php 
$pageTitle = 'Ajouter une Question QCM - Admin';
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
                <h2>➕ Ajouter une Question QCM</h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Créer une nouvelle question pour le test QCM</p>
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
                <form method="POST" action="?controller=adminTest&action=addQuestion">
                    <div class="admin-form-group">
                        <label for="question">Question *</label>
                        <textarea id="question" name="question" rows="3" required 
                                  placeholder="Entrez la question..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                        <div class="admin-form-group">
                            <label for="option_a">Option A *</label>
                            <input type="text" id="option_a" name="option_a" required 
                                   placeholder="Première option">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_b">Option B *</label>
                            <input type="text" id="option_b" name="option_b" required 
                                   placeholder="Deuxième option">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_c">Option C *</label>
                            <input type="text" id="option_c" name="option_c" required 
                                   placeholder="Troisième option">
                        </div>

                        <div class="admin-form-group">
                            <label for="option_d">Option D *</label>
                            <input type="text" id="option_d" name="option_d" required 
                                   placeholder="Quatrième option">
                        </div>
                    </div>

                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label for="correct_answer">Réponse Correcte *</label>
                        <select id="correct_answer" name="correct_answer" required>
                            <option value="">Sélectionnez la réponse correcte</option>
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label for="explanation">Explication (Optionnel)</label>
                        <textarea id="explanation" name="explanation" rows="3" 
                                  placeholder="Expliquez pourquoi cette réponse est correcte..."></textarea>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; flex: 1;">
                            ✅ Ajouter la Question
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

