<?php 
$pageTitle = 'Mon Profil - Game Master';
$currentPage = 'profile';
?>
<style>
    @keyframes medalGlow {
        0% {
            filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.6));
        }
        100% {
            filter: drop-shadow(0 0 20px rgba(255, 215, 0, 1));
        }
    }
    @keyframes medalGlowBronze {
        0% {
            filter: drop-shadow(0 0 8px rgba(205, 127, 50, 0.6));
        }
        100% {
            filter: drop-shadow(0 0 18px rgba(205, 127, 50, 0.9));
        }
    }
    @keyframes medalGlowSilver {
        0% {
            filter: drop-shadow(0 0 8px rgba(192, 192, 192, 0.6));
        }
        100% {
            filter: drop-shadow(0 0 18px rgba(192, 192, 192, 0.9));
        }
    }
    
    .item-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4) !important;
    }
    
    .face-register-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 209, 255, 0.5) !important;
    }
    
    .face-update-btn:hover {
        background: rgba(0, 255, 204, 0.2) !important;
        border-color: rgba(0, 255, 204, 0.5) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3) !important;
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
        <h2 class="section-title">Mon Profil</h2>
        
        <?php if(isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; color: #00ff88; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Profil mis à jour avec succès!
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 40px;">
            <!-- Avatar et Informations de base -->
            <div class="item-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                <?php if($user): ?>
                    <!-- Header avec Avatar et Nom -->
                    <div style="background: linear-gradient(135deg, rgba(0, 255, 204, 0.15) 0%, rgba(0, 200, 255, 0.1) 100%); padding: 40px 30px; text-align: center; border-bottom: 1px solid rgba(0, 255, 204, 0.2);">
                        <?php 
                        $avatarRaw = $user['avatar'] ?? null;
                        $avatarUrl = null;
                        
                        if (!empty($avatarRaw)) {
                            $avatarPath = trim($avatarRaw);
                            $avatarPath = ltrim($avatarPath, '/');
                            $avatarPath = str_replace('projet01/', '', $avatarPath);
                            
                            if (strpos($avatarPath, 'public/') === 0) {
                                $avatarUrl = $avatarPath;
                            } elseif (strpos($avatarPath, 'uploads/') === 0 || strpos($avatarPath, 'assets/') === 0) {
                                $avatarUrl = 'public/' . $avatarPath;
                            } else {
                                $avatarUrl = 'public/' . ltrim($avatarPath, '/');
                            }
                        }
                        
                        $firstLetter = strtoupper(substr($user['username'], 0, 1));
                        $color = '#' . substr(md5($user['username']), 0, 6);
                        ?>
                        
                        <!-- Avatar unique -->
                        <div style="position: relative; display: inline-block; margin-bottom: 20px; width: 120px; height: 120px;">
                            <?php if (!empty($avatarUrl)): ?>
                                <img src="<?php echo htmlspecialchars($avatarUrl); ?>" 
                                     alt="Avatar de <?php echo htmlspecialchars($user['username']); ?>"
                                     id="userAvatarImg"
                                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #00ffcc; box-shadow: 0 0 30px rgba(0, 255, 204, 0.5), 0 0 60px rgba(0, 255, 204, 0.3); display: block;"
                                     onerror="this.style.display='none'; var fallback = document.getElementById('userAvatarFallback'); if(fallback) { fallback.style.display='flex'; }">
                                <div id="userAvatarFallback" style="display: none; width: 120px; height: 120px; border-radius: 50%; background: <?php echo $color; ?>; position: absolute; top: 0; left: 0; align-items: center; justify-content: center; border: 4px solid #00ffcc; box-shadow: 0 0 30px rgba(0, 255, 204, 0.5), 0 0 60px rgba(0, 255, 204, 0.3);">
                                    <span style="color: #ffffff; font-size: 50px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($firstLetter); ?></span>
                                </div>
                            <?php else: ?>
                                <div style="width: 120px; height: 120px; border-radius: 50%; background: <?php echo $color; ?>; display: flex; align-items: center; justify-content: center; border: 4px solid #00ffcc; box-shadow: 0 0 30px rgba(0, 255, 204, 0.5), 0 0 60px rgba(0, 255, 204, 0.3);">
                                    <span style="color: #ffffff; font-size: 50px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($firstLetter); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Médaille badge sur l'avatar -->
                            <?php 
                            $medal = $user['medal'] ?? 'none';
                            if ($medal !== 'none'): 
                                $medalIcons = [
                                    'bronze' => '🥉',
                                    'silver' => '🥈',
                                    'gold' => '🥇'
                                ];
                                $medalColors = [
                                    'bronze' => '#cd7f32',
                                    'silver' => '#c0c0c0',
                                    'gold' => '#ffd700'
                                ];
                                $medalShadows = [
                                    'bronze' => '0 0 15px rgba(205, 127, 50, 0.8)',
                                    'silver' => '0 0 15px rgba(192, 192, 192, 0.8)',
                                    'gold' => '0 0 20px rgba(255, 215, 0, 1)'
                                ];
                            ?>
                                <div style="position: absolute; bottom: 5px; right: 5px; width: 45px; height: 45px; border-radius: 50%; background: rgba(10, 10, 15, 0.9); border: 3px solid <?php echo $medalColors[$medal]; ?>; display: flex; align-items: center; justify-content: center; box-shadow: <?php echo $medalShadows[$medal]; ?>, 0 0 30px rgba(0, 0, 0, 0.5); z-index: 10;">
                                    <span style="font-size: 24px; filter: drop-shadow(0 0 5px <?php echo $medalColors[$medal]; ?>);">
                                        <?php echo $medalIcons[$medal]; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nom d'utilisateur et médaille -->
                        <div style="margin-top: 15px;">
                            <h2 style="color: #00ffcc; font-size: 28px; font-weight: 700; margin: 0 0 10px 0; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5); display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap;">
                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                                <?php if ($medal !== 'none'): ?>
                                    <span style="font-size: 32px; 
                                                 filter: drop-shadow(<?php echo $medalShadows[$medal]; ?>);
                                                 animation: medalGlow 2s ease-in-out infinite alternate;
                                                 transition: transform 0.3s ease;" 
                                          title="Médaille <?php echo $medal === 'bronze' ? 'Bronze' : ($medal === 'silver' ? 'Argent' : 'Or'); ?>"
                                          onmouseover="this.style.transform='scale(1.15)';"
                                          onmouseout="this.style.transform='scale(1)';">
                                        <?php echo $medalIcons[$medal]; ?>
                                    </span>
                                <?php endif; ?>
                            </h2>
                            <?php if ($medal !== 'none'): ?>
                                <p style="color: <?php echo $medalColors[$medal]; ?>; font-size: 14px; font-weight: 600; margin: 5px 0 0 0; text-shadow: 0 0 10px <?php echo $medalColors[$medal]; ?>60;">
                                    Médaille <?php echo $medal === 'bronze' ? 'Bronze' : ($medal === 'silver' ? 'Argent' : 'Or'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Informations -->
                    <div style="padding: 30px;">
                        <h3 style="color: #00ffcc; margin-bottom: 25px; font-size: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-user-circle"></i> Informations Personnelles
                        </h3>
                        
                        <div style="display: grid; gap: 20px;">
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Email</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Rôle</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;">
                                    <?php 
                                    $roleIcons = [
                                        'admin' => '👑',
                                        'moderator' => '🛡️',
                                        'player' => '🎮'
                                    ];
                                    $roleNames = [
                                        'admin' => 'Administrateur',
                                        'moderator' => 'Modérateur',
                                        'player' => 'Joueur'
                                    ];
                                    echo ($roleIcons[$user['role']] ?? '') . ' ' . ($roleNames[$user['role']] ?? ucfirst($user['role']));
                                    ?>
                                </div>
                            </div>
                            
                            <?php if($user['created_at']): ?>
                                <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                    <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Membre depuis</div>
                                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Profil détaillé -->
            <?php if($profile): ?>
                <div class="item-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 20px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                    <h3 style="color: #00ffcc; margin-bottom: 25px; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-id-card"></i> Détails du Profil
                    </h3>
                    <div style="display: grid; gap: 15px;">
                        <?php if(!empty($profile['first_name']) || !empty($profile['last_name'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Nom complet</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars(trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''))); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['discord'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Discord</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['discord']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['country'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Pays</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['country']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['nationality'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Nationalité</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['nationality']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['gender'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Genre</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['gender']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['birth_date'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Date de naissance</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo date('d/m/Y', strtotime($profile['birth_date'])); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['career_level'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Niveau de carrière</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['career_level']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['expertise'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Expertise</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['expertise']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($profile['tech_stack'])): ?>
                            <div style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                                <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 600;">Stack technique</div>
                                <div style="color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500;"><?php echo htmlspecialchars($profile['tech_stack']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="item-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                    <div style="font-size: 64px; margin-bottom: 20px; opacity: 0.6;">📋</div>
                    <h3 style="color: #00ffcc; margin-bottom: 15px; font-size: 24px; font-weight: 600;">Profil</h3>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 30px; font-size: 15px; line-height: 1.6;">Votre profil n'est pas encore complété.</p>
                </div>
            <?php endif; ?>
            
            <!-- Reconnaissance faciale -->
            <div class="item-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%); border: 1px solid rgba(0, 255, 204, 0.2); border-radius: 20px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                <h3 style="color: #00ffcc; margin-bottom: 25px; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-face-smile"></i> Reconnaissance Faciale
                </h3>
                
                <?php 
                $hasFace = isset($faceInfo) && $faceInfo && $faceInfo['has_face'];
                $faceEnabled = isset($faceInfo) && $faceInfo && $faceInfo['face_enabled'];
                $faceRegisteredAt = isset($faceInfo) && $faceInfo && !empty($faceInfo['face_registered_at']) ? $faceInfo['face_registered_at'] : null;
                ?>
                
                <div style="display: grid; gap: 20px;">
                    <div style="padding: 20px; background: rgba(0, 255, 204, 0.05); border-left: 3px solid #00ffcc; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="color: #00ffcc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">État</div>
                            <?php if ($hasFace && $faceEnabled): ?>
                                <span style="color: #00ff88; font-weight: 600; padding: 5px 15px; background: rgba(0, 255, 136, 0.1); border-radius: 20px; font-size: 14px;">
                                    ✓ Activée
                                </span>
                            <?php elseif ($hasFace && !$faceEnabled): ?>
                                <span style="color: #ffa500; font-weight: 600; padding: 5px 15px; background: rgba(255, 165, 0, 0.1); border-radius: 20px; font-size: 14px;">
                                    ⚠ Désactivée
                                </span>
                            <?php else: ?>
                                <span style="color: rgba(255, 255, 255, 0.6); font-weight: 600; padding: 5px 15px; background: rgba(255, 255, 255, 0.05); border-radius: 20px; font-size: 14px;">
                                    Non configurée
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($hasFace && $faceRegisteredAt): ?>
                            <div style="color: rgba(255, 255, 255, 0.7); font-size: 13px; margin-top: 10px;">
                                Enregistré le <?php echo date('d/m/Y à H:i', strtotime($faceRegisteredAt)); ?>
                            </div>
                        <?php else: ?>
                            <div style="color: rgba(255, 255, 255, 0.7); font-size: 14px; margin-top: 10px; line-height: 1.6;">
                                Activez la reconnaissance faciale pour une connexion rapide et sécurisée sans mot de passe.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php if (!$hasFace): ?>
                            <a href="?action=face_registration" class="face-register-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #00d1ff, #00b8e6); color: #000; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 209, 255, 0.3);">
                                📸 Enregistrer mon visage
                            </a>
                        <?php else: ?>
                            <a href="?action=face_registration" class="face-update-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: rgba(0, 255, 204, 0.1); color: #00ffcc; text-decoration: none; border: 1px solid rgba(0, 255, 204, 0.3); border-radius: 8px; font-weight: 600; transition: all 0.3s ease;">
                                🔄 Mettre à jour mon visage
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

