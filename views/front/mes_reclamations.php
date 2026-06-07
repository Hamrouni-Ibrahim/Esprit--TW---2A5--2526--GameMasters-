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
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: #00ffcc; font-size: 36px; margin-bottom: 15px; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);">
                    📋 Mes Réclamations
                </h2>
                <p style="color: rgba(255, 255, 255, 0.8); font-size: 16px;">
                    Consultez toutes vos réclamations et suivez leur statut
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

            <div style="text-align: center; margin-bottom: 30px;">
                <a href="?action=reclamation_create" 
                   style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #00ffcc, #00ccaa); color: #0a0e27; text-decoration: none; border-radius: 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 255, 204, 0.3);"
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 255, 204, 0.5)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 255, 204, 0.3)';">
                    ➕ Créer une nouvelle réclamation
                </a>
            </div>
            
            <?php if(!empty($reclamations)): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                    <?php foreach ($reclamations as $row): ?>
                        <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(0, 255, 204, 0.3)'; this.style.boxShadow='0 15px 35px rgba(0, 255, 204, 0.2)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.boxShadow='none';">
                            <h3 style="color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;">
                                <?php echo htmlspecialchars($row['titre']); ?>
                            </h3>
                            <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.6; margin-bottom: 20px; font-size: 14px;">
                                <?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 150))); ?><?php echo strlen($row['description']) > 150 ? '...' : ''; ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                                <span style="display: inline-block; padding: 8px 20px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; <?php echo $row['statut'] == 'traité' ? 'background: rgba(0, 255, 136, 0.2); border: 1px solid rgba(0, 255, 136, 0.5); color: #00ff88;' : 'background: rgba(255, 204, 0, 0.2); border: 1px solid rgba(255, 204, 0, 0.5); color: #ffcc00;'; ?>">
                                    <?php echo ucfirst($row['statut']); ?>
                                </span>
                                <span style="color: rgba(255, 255, 255, 0.6); font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                    📅 <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                </span>
                            </div>

                            <?php
                            $canEdit = $row['can_edit'];
                            $timeRemaining = $row['time_remaining'];
                            ?>

                            <?php if($row['statut'] == 'en_attente' && $canEdit): ?>
                                <div style="margin-top: 20px; text-align: center; display: flex; gap: 10px; flex-wrap: wrap;">
                                    <a href="?action=reclamation_edit&id=<?php echo $row['id']; ?>" 
                                       style="flex: 1; min-width: 120px; padding: 12px 25px; background: linear-gradient(135deg, #ff8e53, #ff6b6b); color: #ffffff; text-decoration: none; border-radius: 25px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255, 142, 83, 0.3); text-align: center;"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 142, 83, 0.5)';"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255, 142, 83, 0.3)';">
                                        ✏️ Modifier (<?php echo floor($timeRemaining / 60); ?>:<?php echo str_pad($timeRemaining % 60, 2, '0', STR_PAD_LEFT); ?>)
                                    </a>
                                    <a href="?action=reclamation_delete&id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');"
                                       style="flex: 1; min-width: 120px; padding: 12px 25px; background: linear-gradient(135deg, #ff4757, #ff3838); color: #ffffff; text-decoration: none; border-radius: 25px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3); text-align: center;"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 71, 87, 0.5)';"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255, 71, 87, 0.3)';">
                                        🗑️ Supprimer
                                    </a>
                                </div>
                            <?php elseif($row['statut'] == 'en_attente' && !$canEdit): ?>
                                <div style="margin-top: 20px; text-align: center;">
                                    <span style="color: #ff4d4d; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7;">
                                        ⏰ Délai de modification expiré
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($row['reponse']): ?>
                                <div style="margin-top: 25px; padding: 20px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                    <strong style="color: #00ffcc; display: block; margin-bottom: 10px; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">
                                        📬 Réponse de l'administration:
                                    </strong>
                                    <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.6; font-size: 14px;">
                                        <?php echo nl2br(htmlspecialchars($row['reponse'])); ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div style="padding: 15px; background: rgba(255, 204, 0, 0.05); border-left: 3px solid rgba(255, 204, 0, 0.5); border-radius: 8px; margin-top: 20px;">
                                    <p style="color: rgba(255, 255, 255, 0.6); font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                        ⏳ En attente de réponse
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 20px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📋</div>
                    <h3 style="color: #ffffff; font-size: 24px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 2px;">
                        Aucune Réclamation
                    </h3>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 16px; margin-bottom: 30px;">
                        Vous n'avez pas encore créé de réclamation.
                    </p>
                    <a href="?action=reclamation_create" 
                       style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #00ffcc, #00ccaa); color: #0a0e27; text-decoration: none; border-radius: 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 255, 204, 0.3);"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 255, 204, 0.5)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 255, 204, 0.3)';">
                        Créer votre première réclamation
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

