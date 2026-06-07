<?php
// This file is included by the controller, so header/footer are already included
?>

<section class="content-section">
    <div class="content-bg"></div>
    <div class="content-shapes">
        <div class="content-shape shape1"></div>
        <div class="content-shape shape2"></div>
        <div class="content-shape shape3"></div>
    </div>
    <div class="content-container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: #00ffcc; font-size: 36px; margin-bottom: 15px; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);">
                    📝 Nouvelle Réclamation
                </h2>
                <p style="color: rgba(255, 255, 255, 0.8); font-size: 16px;">
                    Décrivez votre problème en détail et nous le traiterons rapidement
                </p>
            </div>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div style="background: rgba(0, 255, 136, 0.1); border: 2px solid #00ff88; border-radius: 15px; padding: 20px; margin-bottom: 30px; text-align: center;">
                    <div style="color: #00ff88; font-size: 18px; font-weight: 700;">
                        ✓ <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div style="background: rgba(255, 77, 77, 0.1); border: 2px solid #ff4d4d; border-radius: 15px; padding: 20px; margin-bottom: 30px; text-align: center;">
                    <div style="color: #ff4d4d; font-size: 18px; font-weight: 700;">
                        ❌ <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    </div>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 40px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);">
                <form method="POST" class="contact-form">
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label for="titre" style="display: block; color: #00ffcc; font-weight: 600; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                            Titre de la Réclamation
                        </label>
                        <input type="text" id="titre" name="titre" required 
                               placeholder="Ex: Problème avec le service..."
                               style="width: 100%; padding: 15px 20px; background: rgba(0, 0, 0, 0.3); border: 2px solid rgba(255, 255, 255, 0.1); border-radius: 12px; color: #ffffff; font-size: 16px; transition: all 0.3s ease;"
                               onfocus="this.style.borderColor='#00ffcc'; this.style.boxShadow='0 0 0 3px rgba(0, 255, 204, 0.2)';"
                               onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.boxShadow='none';">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label for="description" style="display: block; color: #00ffcc; font-weight: 600; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                            Description Détaillée
                        </label>
                        <textarea id="description" name="description" rows="8" required
                                  placeholder="Décrivez votre problème en détail. Plus vous fournissez d'informations, plus nous pourrons vous aider rapidement..."
                                  style="width: 100%; padding: 15px 20px; background: rgba(0, 0, 0, 0.3); border: 2px solid rgba(255, 255, 255, 0.1); border-radius: 12px; color: #ffffff; font-size: 16px; transition: all 0.3s ease; resize: vertical; font-family: inherit;"
                                  onfocus="this.style.borderColor='#00ffcc'; this.style.boxShadow='0 0 0 3px rgba(0, 255, 204, 0.2)';"
                                  onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.boxShadow='none';"></textarea>
                    </div>

                    <button type="submit" 
                            style="width: 100%; padding: 18px 40px; background: linear-gradient(135deg, #00ffcc, #00ccaa); color: #0a0e27; border: none; border-radius: 50px; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(0, 255, 204, 0.4);"
                            onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(0, 255, 204, 0.6)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 255, 204, 0.4)';">
                        Envoyer la Réclamation
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="?action=mes_reclamations" 
                       style="color: #00ffcc; text-decoration: none; font-weight: 600; transition: all 0.3s ease;"
                       onmouseover="this.style.color='#00ccaa';"
                       onmouseout="this.style.color='#00ffcc';">
                        ← Retour à mes réclamations
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    const titreInput = document.getElementById('titre');
    const descriptionTextarea = document.getElementById('description');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (titreInput.value.trim().length < 5) {
            showError(titreInput, 'Le titre doit contenir au moins 5 caractères');
            isValid = false;
        } else {
            clearError(titreInput);
        }

        if (descriptionTextarea.value.trim().length < 20) {
            showError(descriptionTextarea, 'La description doit contenir au moins 20 caractères');
            isValid = false;
        } else {
            clearError(descriptionTextarea);
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(element, message) {
        clearError(element);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.cssText = 'color: #ff4d4d; font-size: 14px; margin-top: 8px; font-weight: 500;';
        element.parentElement.appendChild(errorDiv);
        element.style.borderColor = '#ff4d4d';
    }

    function clearError(element) {
        const errorMessage = element.parentElement.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        element.style.borderColor = '';
    }
});
</script>



