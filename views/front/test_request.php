<?php 
$pageTitle = 'Demander l\'Accès au Test QCM - Game Master';
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
        
        <h2 class="section-title">📝 Demander l'Accès au Test QCM</h2>
        
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
        
        <div style="max-width: 800px; margin: 30px auto; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 15px; padding: 30px;">
            
            <div style="background: rgba(232, 121, 249, 0.1); border-left: 4px solid #e879f9; padding: 20px; margin-bottom: 25px; border-radius: 8px;">
                <h3 style="color: #e879f9; margin-top: 0; font-size: 1.3em;">🎯 À propos du Test QCM</h3>
                <ul style="color: #e0e0e0; line-height: 1.8; margin: 10px 0;">
                    <li>Le test dure <strong>30 minutes</strong> maximum</li>
                    <li>Il contient <strong>10 questions</strong> sur le développement de jeux et l'impact social</li>
                    <li>Vous devez obtenir une note minimale selon votre nombre de jeux publiés pour recevoir une médaille</li>
                    <li><strong>Bronze</strong> : 30-50% + 5+ jeux publiés</li>
                    <li><strong>Silver</strong> : 50-99% + 10+ jeux publiés</li>
                    <li><strong>Gold</strong> : 100% + 10+ jeux publiés</li>
                </ul>
            </div>
            
            <form method="POST" action="?controller=test&action=requestAccess" style="margin-top: 20px;">
                <div style="margin-bottom: 25px;">
                    <label for="motivational_letter" style="display: block; color: #e879f9; font-weight: 600; margin-bottom: 10px; font-size: 1.1em;">
                        📄 Lettre de Motivation *
                    </label>
                    <p style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 15px;">
                        Expliquez pourquoi vous souhaitez passer ce test et comment cela peut contribuer à votre développement dans le domaine du jeu vidéo et de l'impact social. (Minimum 50 caractères)
                    </p>
                    <textarea 
                        id="motivational_letter" 
                        name="motivational_letter" 
                        required 
                        minlength="50"
                        rows="10"
                        style="width: 100%; padding: 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 10px; color: #fff; font-size: 14px; font-family: inherit; resize: vertical;"
                        placeholder="J'aimerais passer ce test parce que..."
                        onfocus="this.style.borderColor='#e879f9'; this.style.background='rgba(255, 255, 255, 0.08)';"
                        onblur="this.style.borderColor='rgba(232, 121, 249, 0.3)'; this.style.background='rgba(255, 255, 255, 0.05)';"
                    ></textarea>
                    <div style="margin-top: 5px; color: #a0a0a0; font-size: 0.85em;">
                        <span id="charCount">0</span> / 50 caractères minimum
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
                    <a href="?controller=formation&action=userDashboard" style="padding: 12px 30px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; color: #fff; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                        Annuler
                    </a>
                    <button type="submit" style="padding: 12px 30px; background: linear-gradient(135deg, #9333ea, #c084fc); border: none; border-radius: 10px; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(147, 51, 234, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        ✉️ Envoyer la Demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Character counter
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('motivational_letter');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count < 50) {
            charCount.style.color = '#ff6b6b';
        } else {
            charCount.style.color = '#00ff88';
        }
    });
});
</script>

<?php include "views/front/includes/footer.php"; ?>






