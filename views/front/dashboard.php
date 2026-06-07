<?php 
$pageTitle = 'Tableau de Bord - Game Master';
$currentPage = 'dashboard';
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
        <?php 
        // Get username from session
        $isAuthenticated = isset($_SESSION['user_id']);
        $username = $_SESSION['username'] ?? 'Visiteur';
        // If username is not set, try to get it from the database
        if (!$isAuthenticated) {
            $username = 'Visiteur';
        } elseif (!isset($_SESSION['username']) && isset($_SESSION['user_id']) && isset($db)) {
            require_once "models/User.php";
            $userModel = new User($db);
            $userModel->id = $_SESSION['user_id'];
            if ($userModel->readOne()) {
                $username = $userModel->username ?? 'Utilisateur';
            }
        }
        ?>
        <div style="text-align: center; margin-bottom: 30px; padding: 20px; background: rgba(255, 255, 255, 0.03); border-radius: 15px; border: 1px solid rgba(0, 255, 204, 0.2);">
            <h2 style="color: #00ffcc; font-size: 32px; margin: 0; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);">
                <?php if ($isAuthenticated): ?>
                👋 Hello <?php echo htmlspecialchars($username); ?>!
                <?php else: ?>
                    👋 Bienvenue sur Game Masters!
                <?php endif; ?>
            </h2>
            <p style="color: rgba(255, 255, 255, 0.8); margin-top: 10px; font-size: 16px;">
                <?php if ($isAuthenticated): ?>
                Bienvenue sur votre tableau de bord
                <?php else: ?>
                    Découvrez nos jeux éducatifs et formations. <a href="?action=register" style="color: #00ffcc; text-decoration: underline;">Créez un compte</a> pour ajouter vos propres jeux!
                <?php endif; ?>
            </p>
        </div>
        <h2 class="section-title">Tableau de Bord</h2>
        
        <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #3b82f6; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);">🎮 Jeux</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Découvrez notre collection de jeux éducatifs et engageants. Jouez, apprenez et explorez des jeux créés par la communauté et notre équipe pour un apprentissage interactif et amusant.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?action=games" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #3b82f6, #9333ea); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);">Voir les jeux</a>
                </div>
            </div>

            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(0, 255, 204, 0.1), rgba(0, 204, 255, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #00ffcc; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(0, 255, 204, 0.5);">Formations</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Découvrez nos formations complètes qui regroupent plusieurs cours et contenus éducatifs. Chaque formation vous guide à travers un parcours d'apprentissage structuré pour développer vos compétences étape par étape.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?controller=formation&action=list" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0e27; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(0, 255, 204, 0.3);">Voir les formations</a>
                </div>
            </div>

            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(255, 142, 83, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #ff8e53; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(255, 142, 83, 0.5);">Éducations</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Parcourez nos contenus éducatifs individuels. Chaque éducation fait partie d'une formation et vous permet d'apprendre des sujets spécifiques. Accédez aux cours, tutoriels et ressources pour développer vos compétences.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?controller=education&action=list" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #ff6b6b, #ff8e53); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(255, 142, 83, 0.3);">Voir les éducations</a>
                </div>
            </div>

            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(232, 121, 249, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #e879f9; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(232, 121, 249, 0.5);">Recherche</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Trouvez rapidement les formations et éducations qui vous intéressent. Utilisez la recherche par catégorie ou par mots-clés pour filtrer et découvrir le contenu qui correspond à vos besoins d'apprentissage.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?controller=category&action=search" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #9333ea, #e879f9); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(232, 121, 249, 0.3);">Rechercher</a>
                </div>
            </div>

            <?php if ($isAuthenticated): ?>
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 193, 7, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #ffd700; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);">⭐ Mes Favoris</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Accédez rapidement à vos formations et éducations favorites. Retrouvez tous les contenus que vous avez marqués comme favoris pour un accès facile et rapide.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?controller=favorite&action=list" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #ffd700, #ffc107); color: #0a0e27; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);">Voir mes favoris</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Projets & Donations - Visible pour tous -->
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(244, 114, 182, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #f472b6; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(244, 114, 182, 0.5);">💝 Projets & Donations</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Découvrez nos projets internationaux et contribuez en faisant un don. Soutenez des initiatives qui créent un impact social positif à travers le monde.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?action=projects" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #ec4899, #f472b6); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(244, 114, 182, 0.3);">Voir les projets</a>
                    <?php if ($isAuthenticated): ?>
                    <a href="?action=donation" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #f472b6, #ec4899); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(244, 114, 182, 0.3);">Mes donations</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Événements - Visible pour tous -->
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(96, 165, 250, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #60a5fa; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(96, 165, 250, 0.5);">📅 Événements</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Participez à nos événements et rencontres communautaires. Découvrez les événements à venir<?php if ($isAuthenticated): ?> et gérez vos participations<?php endif; ?>.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?action=events" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #3b82f6, #60a5fa); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(96, 165, 250, 0.3);">Voir les événements</a>
                    <?php if ($isAuthenticated): ?>
                    <a href="?action=my_participations" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #60a5fa, #3b82f6); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(96, 165, 250, 0.3);">Mes participations</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isAuthenticated): ?>
            <!-- Bibliothèque de Jeux -->
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(192, 132, 252, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #c084fc; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(192, 132, 252, 0.5);">🎮 Bibliothèque de Jeux</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Découvrez notre collection de jeux simples et amusants. Jouez à des jeux classiques et divertissants pour vous détendre et vous amuser.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?action=games_library" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #9333ea, #c084fc); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(192, 132, 252, 0.3);">Voir les jeux</a>
                </div>
            </div>


            <!-- Test QCM & Certificat -->
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(74, 222, 128, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #4ade80; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(74, 222, 128, 0.5);">🎯 Test QCM & Certificat</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Passez le test QCM pour obtenir votre certificat et médaille. Consultez vos résultats et téléchargez votre certificat de réussite.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?controller=test&action=request" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #22c55e, #4ade80); color: #0a0e27; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(74, 222, 128, 0.3);">Passer le test</a>
                    <a href="?controller=test&action=status" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #4ade80, #22c55e); color: #0a0e27; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(74, 222, 128, 0.3);">Mes résultats</a>
                </div>
            </div>

            <!-- Réclamations -->
            <div class="item-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 30px; transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(255, 142, 83, 0.1), rgba(255, 107, 107, 0.1)); border-radius: 50%; filter: blur(40px);"></div>
                <h3 style="color: #ff8e53; margin-bottom: 15px; font-size: 24px; position: relative; z-index: 1; text-shadow: 0 0 20px rgba(255, 142, 83, 0.5);">📋 Réclamations</h3>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 25px; line-height: 1.8; position: relative; z-index: 1; font-size: 15px;">Envoyez une réclamation, modifiez-la ou supprimez-la dans les 30 minutes suivant sa création. Suivez les réponses de l'administration.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1;">
                    <a href="?action=reclamation_create" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #ff8e53, #ff6b6b); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(255, 142, 83, 0.3);">Créer une réclamation</a>
                    <a href="?action=mes_reclamations" class="btn btn-primary" style="flex: 1; min-width: 150px; text-align: center; background: linear-gradient(135deg, #ff6b6b, #ff8e53); color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 0 20px rgba(255, 142, 83, 0.3);">Mes réclamations</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.item-card:hover {
    transform: translateY(-5px);
    border-color: rgba(0, 255, 204, 0.3);
    box-shadow: 0 10px 30px rgba(0, 255, 204, 0.2);
}

.item-card:nth-child(2):hover {
    border-color: rgba(255, 142, 83, 0.3);
    box-shadow: 0 10px 30px rgba(255, 142, 83, 0.2);
}

.item-card:nth-child(3):hover {
    border-color: rgba(232, 121, 249, 0.3);
    box-shadow: 0 10px 30px rgba(232, 121, 249, 0.2);
}

.item-card:nth-child(1):hover {
    border-color: rgba(59, 130, 246, 0.3);
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
}

.item-card:nth-child(5):hover {
    border-color: rgba(255, 215, 0, 0.3);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
}

.item-card:nth-child(6):hover {
    border-color: rgba(192, 132, 252, 0.3);
    box-shadow: 0 10px 30px rgba(192, 132, 252, 0.2);
}

.item-card:nth-child(7):hover {
    border-color: rgba(244, 114, 182, 0.3);
    box-shadow: 0 10px 30px rgba(244, 114, 182, 0.2);
}

.item-card:nth-child(8):hover {
    border-color: rgba(96, 165, 250, 0.3);
    box-shadow: 0 10px 30px rgba(96, 165, 250, 0.2);
}

.item-card:nth-child(9):hover {
    border-color: rgba(74, 222, 128, 0.3);
    box-shadow: 0 10px 30px rgba(74, 222, 128, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 255, 204, 0.4);
}

.item-card:nth-child(2) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(255, 142, 83, 0.4);
}

.item-card:nth-child(3) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(232, 121, 249, 0.4);
}

.item-card:nth-child(1) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(59, 130, 246, 0.4);
}

.item-card:nth-child(5) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
}

.item-card:nth-child(6) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(192, 132, 252, 0.4);
}

.item-card:nth-child(7) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(244, 114, 182, 0.4);
}

.item-card:nth-child(8) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(96, 165, 250, 0.4);
}

.item-card:nth-child(9) .btn-primary:hover {
    box-shadow: 0 5px 20px rgba(74, 222, 128, 0.4);
}
</style>

<?php include "views/front/includes/footer.php"; ?>

