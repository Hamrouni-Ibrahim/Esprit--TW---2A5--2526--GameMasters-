<?php 
$pageTitle = 'Test QCM - Game Master';
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
        
        <div style="max-width: 900px; margin: 0 auto;">
            
            <!-- Timer and Header -->
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 25px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h2 style="color: #e879f9; margin: 0 0 10px 0;">📝 Test QCM</h2>
                        <p style="color: #a0a0a0; margin: 0;">Répondez aux questions ci-dessous</p>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2em; color: <?php echo $remainingTime <= 60 ? '#ff6b6b' : '#00ffcc'; ?>; font-weight: bold; margin-bottom: 5px;" id="timerDisplay">
                            <?php echo gmdate("i:s", $remainingTime); ?>
                        </div>
                        <div style="color: #a0a0a0; font-size: 0.9em;">Temps restant</div>
                    </div>
                </div>
                
                <div style="margin-top: 20px; background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="color: #a0a0a0;">Questions: </span>
                            <strong style="color: #fff;"><?php echo count($questions); ?></strong>
                        </div>
                        <div>
                            <span style="color: #a0a0a0;">Répondues: </span>
                            <strong style="color: #00ffcc;" id="answeredCount">0</strong> / <?php echo count($questions); ?>
                        </div>
                        <div>
                            <span style="color: #a0a0a0;">Limite: </span>
                            <strong style="color: #fff;">30 minutes</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Form -->
            <form id="testForm" method="POST" action="?controller=test&action=submitTest">
                <input type="hidden" name="attempt_id" value="<?php echo $attemptId; ?>">
                
                <?php if (empty($questions)): ?>
                    <div style="background: rgba(255, 51, 51, 0.1); border: 1px solid rgba(255, 51, 51, 0.3); border-radius: 10px; padding: 40px; text-align: center;">
                        <p style="color: #ff6b6b; font-size: 1.1em;">Aucune question disponible pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($questions as $index => $question): 
                        $questionNum = $index + 1;
                        $questionId = $question['id'];
                        $savedAnswer = isset($savedAnswers[$questionId]) ? $savedAnswers[$questionId] : null;
                    ?>
                        <div class="question-card" data-question-id="<?php echo $questionId; ?>" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 30px; margin-bottom: 25px;">
                            
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                                <h3 style="color: #e879f9; margin: 0; font-size: 1.2em;">
                                    Question <?php echo $questionNum; ?> / <?php echo count($questions); ?>
                                </h3>
                                <span class="answer-indicator" id="indicator-<?php echo $questionId; ?>" style="display: none; padding: 5px 12px; background: rgba(0, 255, 136, 0.2); color: #00ff88; border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                    ✓ Répondu
                                </span>
                            </div>
                            
                            <p style="color: #e0e0e0; font-size: 1.1em; margin-bottom: 25px; line-height: 1.6;">
                                <?php echo htmlspecialchars($question['question']); ?>
                            </p>
                            
                            <div style="display: grid; gap: 12px;">
                                <?php 
                                $options = [
                                    'a' => $question['option_a'],
                                    'b' => $question['option_b'],
                                    'c' => $question['option_c'],
                                    'd' => $question['option_d']
                                ];
                                foreach ($options as $key => $option): 
                                ?>
                                    <label style="display: flex; align-items: center; padding: 15px; background: rgba(255, 255, 255, 0.03); border: 2px solid rgba(232, 121, 249, 0.2); border-radius: 10px; cursor: pointer; transition: all 0.3s; <?php echo ($savedAnswer === $key) ? 'background: rgba(232, 121, 249, 0.1); border-color: #e879f9;' : ''; ?>"
                                           onmouseover="this.style.background='rgba(232, 121, 249, 0.08)'; this.style.borderColor='rgba(232, 121, 249, 0.5)';"
                                           onmouseout="this.style.background='<?php echo ($savedAnswer === $key) ? 'rgba(232, 121, 249, 0.1)' : 'rgba(255, 255, 255, 0.03)'; ?>'; this.style.borderColor='<?php echo ($savedAnswer === $key) ? '#e879f9' : 'rgba(232, 121, 249, 0.2)'; ?>';">
                                        <input type="radio" 
                                               name="answers[<?php echo $questionId; ?>]" 
                                               value="<?php echo $key; ?>" 
                                               <?php echo ($savedAnswer === $key) ? 'checked' : ''; ?>
                                               class="answer-radio"
                                               data-question-id="<?php echo $questionId; ?>"
                                               data-attempt-id="<?php echo $attemptId; ?>"
                                               style="margin-right: 15px; width: 20px; height: 20px; cursor: pointer; accent-color: #e879f9;">
                                        <span style="flex: 1; color: #e0e0e0; font-size: 1em;">
                                            <strong style="color: #e879f9; margin-right: 8px;"><?php echo strtoupper($key); ?>.</strong>
                                            <?php echo htmlspecialchars($option); ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Submit Button -->
                    <div style="text-align: center; margin-top: 40px; padding: 30px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px;">
                        <p style="color: #a0a0a0; margin-bottom: 20px;">
                            Assurez-vous d'avoir répondu à toutes les questions avant de soumettre.
                        </p>
                        <button type="submit" id="submitBtn" 
                                style="padding: 15px 40px; background: linear-gradient(135deg, #00ff88, #00cc88); border: none; border-radius: 10px; color: #0a0a0a; font-weight: 700; font-size: 1.1em; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 255, 136, 0.4)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                                onclick="return confirm('Êtes-vous sûr de vouloir soumettre votre test ? Vous ne pourrez plus le modifier après.');">
                            ✅ Soumettre le Test
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const attemptId = <?php echo $attemptId; ?>;
    const timeLimit = <?php echo $attempt['time_limit']; ?>;
    let remainingSeconds = <?php echo $remainingTime; ?>;
    const timerDisplay = document.getElementById('timerDisplay');
    const submitBtn = document.getElementById('submitBtn');
    const testForm = document.getElementById('testForm');
    
    // Count answered questions
    function updateAnsweredCount() {
        const answered = document.querySelectorAll('.answer-radio:checked').length;
        document.getElementById('answeredCount').textContent = answered;
    }
    
    // Update answer indicator
    document.querySelectorAll('.answer-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const questionId = this.dataset.questionId;
            const indicator = document.getElementById('indicator-' + questionId);
            
            if (this.checked) {
                indicator.style.display = 'inline-block';
                // Save answer via AJAX
                saveAnswer(attemptId, questionId, this.value);
            }
            
            updateAnsweredCount();
        });
        
        // Update indicator on load
        if (radio.checked) {
            const questionId = radio.dataset.questionId;
            const indicator = document.getElementById('indicator-' + questionId);
            indicator.style.display = 'inline-block';
        }
    });
    
    // Initial count
    updateAnsweredCount();
    
    // Timer countdown
    const timerInterval = setInterval(function() {
        remainingSeconds--;
        
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = '00:00';
            timerDisplay.style.color = '#ff6b6b';
            
            // Auto-submit when time expires
            alert('Le temps est écoulé ! Le test sera soumis automatiquement.');
            testForm.submit();
            return;
        }
        
        // Update display
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        timerDisplay.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        // Change color when less than 1 minute
        if (remainingSeconds <= 60) {
            timerDisplay.style.color = '#ff6b6b';
            timerDisplay.style.animation = 'pulse 1s infinite';
        }
    }, 1000);
    
    // Prevent accidental page leave
    let formSubmitted = false;
    testForm.addEventListener('submit', function() {
        formSubmitted = true;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (!formSubmitted) {
            e.preventDefault();
            e.returnValue = 'Êtes-vous sûr de vouloir quitter ? Votre progression sera sauvegardée, mais le timer continuera.';
            return e.returnValue;
        }
    });
    
    // Save answer function
    function saveAnswer(attemptId, questionId, answer) {
        const formData = new FormData();
        formData.append('attempt_id', attemptId);
        formData.append('question_id', questionId);
        formData.append('answer', answer);
        
        fetch('?controller=test&action=saveAnswer', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Erreur lors de la sauvegarde:', data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }
});

// Add pulse animation for urgent timer
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
`;
document.head.appendChild(style);
</script>

<?php include "views/front/includes/footer.php"; ?>






