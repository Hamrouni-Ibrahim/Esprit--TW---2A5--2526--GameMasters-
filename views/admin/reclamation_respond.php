<?php
// This file is included by the controller, so header/footer are already included
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>✉ Répondre à la Réclamation</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Répondre à la réclamation #<?php echo $this->reclamation->id; ?></p>
        </div>

        <div class="dashboard-grid">
            <!-- Reclamation Details -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Détails de la Réclamation</h2>
                </div>
                <div class="admin-form-group">
                    <label><strong>Titre:</strong></label>
                    <p style="color: #ffffff; padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
                        <?php echo htmlspecialchars($this->reclamation->titre); ?>
                    </p>
                </div>
                <div class="admin-form-group">
                    <label><strong>Description:</strong></label>
                    <p style="color: #ffffff; padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; white-space: pre-wrap;">
                        <?php echo nl2br(htmlspecialchars($this->reclamation->description)); ?>
                    </p>
                </div>
                <div class="admin-form-group">
                    <label><strong>Statut:</strong></label>
                    <p>
                        <span style="display: inline-block; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; <?php echo $this->reclamation->statut == 'traité' ? 'background: rgba(0, 255, 136, 0.2); border: 1px solid rgba(0, 255, 136, 0.5); color: #00ff88;' : 'background: rgba(255, 204, 0, 0.2); border: 1px solid rgba(255, 204, 0, 0.5); color: #ffcc00;'; ?>">
                            <?php echo ucfirst($this->reclamation->statut); ?>
                        </span>
                    </p>
                </div>
                <div class="admin-form-group">
                    <label><strong>Date de création:</strong></label>
                    <p style="color: #a0a0a0;">
                        <?php echo date('d/m/Y H:i', strtotime($this->reclamation->created_at)); ?>
                    </p>
                </div>
            </div>

            <!-- Response Form -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Réponse</h2>
                </div>
                <form method="POST" action="?action=admin_reclamation_respond&id=<?php echo $this->reclamation->id; ?>">
                    <div class="admin-form-group">
                        <label for="reponse">Votre réponse *</label>
                        <textarea id="reponse" name="reponse" rows="10" required 
                                  placeholder="Tapez votre réponse ici..."><?php echo htmlspecialchars($this->reclamation->reponse ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn-reclamation-send">
                        <span style="margin-right: 8px;">✉</span>
                        Envoyer la Réponse
                    </button>
                    <a href="?action=admin_reclamations" class="btn-reclamation-cancel" style="display: block; text-align: center; text-decoration: none; margin-top: 15px;">
                        ← Retour à la liste
                    </a>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    /* Beautiful Admin Reclamation Send Button */
    .btn-reclamation-send {
        width: 100%;
        padding: 16px 30px;
        background: linear-gradient(135deg, #9333ea 0%, #c084fc 50%, #e879f9 100%);
        background-size: 200% 200%;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 4px 15px rgba(147, 51, 234, 0.4),
            0 0 0 1px rgba(232, 121, 249, 0.2) inset;
        text-transform: uppercase;
        animation: gradientShift 3s ease infinite;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-reclamation-send::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-reclamation-send:hover::before {
        left: 100%;
    }
    
    .btn-reclamation-send:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 25px rgba(147, 51, 234, 0.6),
            0 0 0 1px rgba(232, 121, 249, 0.4) inset,
            0 0 30px rgba(232, 121, 249, 0.4);
        background-position: right center;
    }
    
    .btn-reclamation-send:active {
        transform: translateY(-1px);
        box-shadow: 
            0 4px 15px rgba(147, 51, 234, 0.5),
            0 0 0 1px rgba(232, 121, 249, 0.3) inset;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .btn-reclamation-cancel {
        padding: 12px 25px;
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.15) 0%, rgba(156, 163, 175, 0.15) 100%);
        border: 2px solid rgba(107, 114, 128, 0.4);
        border-radius: 10px;
        color: #9ca3af;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(107, 114, 128, 0.2);
        text-transform: uppercase;
    }
    
    .btn-reclamation-cancel::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(107, 114, 128, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    
    .btn-reclamation-cancel:hover::before {
        width: 200px;
        height: 200px;
    }
    
    .btn-reclamation-cancel:hover {
        transform: translateY(-2px);
        border-color: rgba(107, 114, 128, 0.8);
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.3) 0%, rgba(156, 163, 175, 0.3) 100%);
        box-shadow: 
            0 5px 20px rgba(107, 114, 128, 0.4),
            0 0 20px rgba(107, 114, 128, 0.3);
        color: #d1d5db;
    }
</style>

