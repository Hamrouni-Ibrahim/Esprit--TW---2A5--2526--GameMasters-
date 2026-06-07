<?php
// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ?action=login');
    exit;
}

// Vérifier les permissions
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$editing_user_id = $_GET['user_id'] ?? $_SESSION['user_id'];

// Si un admin modifie le profil d'un autre utilisateur
if(isset($_GET['user_id']) && !$is_admin) {
    header('Location: ?action=home');
    exit;
}

// Si le profil n'est pas déjà défini par ProfileController, l'initialiser
if (!isset($profile)) {
    $profile = [
        'first_name' => '',
        'last_name' => '',
        'discord' => '',
        'country' => '',
        'nationality' => '',
        'gender' => '',
        'birth_date' => '',
        'career_level' => '',
        'expertise' => '',
        'tech_stack' => '',
        'timezone' => 'Europe/Paris'
    ];
}

// Si l'utilisateur n'est pas défini, le récupérer
if (!isset($user)) {
    $user = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'Utilisateur',
        'email' => $_SESSION['email'] ?? ''
    ];
}
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
        <h2 class="section-title">Modifier mon Profil</h2>
        
        <?php if(isset($errors) && !empty($errors)): ?>
            <div style="background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
    <style>
        :root {
            --primary-bg: #0e0e16;
            --secondary-bg: #141429;
            --card-bg: #1a1a2e;
            --accent-color: #00d1ff;
            --text-color: #ffffff;
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }


        .profile-form {
            background: var(--card-bg);
            padding: 40px;
            border-radius: var(--border-radius);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .form-section h2 {
            color: var(--accent-color);
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--accent-color);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--border-radius);
            color: var(--text-color);
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-hint {
            color: #888888;
            font-size: 0.9em;
            margin-top: 5px;
            opacity: 0.7;
        }

        .btn-primary {
            background: var(--accent-color);
            color: var(--primary-bg);
            padding: 15px 40px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: bold;
            cursor: pointer;
            font-size: 1.1em;
            display: block;
            margin: 40px auto 0;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #00b8e6;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

        .separator {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            margin: 30px 0;
            opacity: 0.5;
        }

        .error {
            color: #ff4444;
            background: rgba(255,68,68,0.1);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .success {
            color: #00ff88;
            background: rgba(0,255,136,0.1);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .admin-notice {
            background: rgba(255,170,0,0.2);
            color: #ffaa00;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border: 1px solid rgba(255,170,0,0.3);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .profile-form {
                padding: 20px;
            }
        }
    </style>
        
        <?php if($is_admin && $editing_user_id != $_SESSION['user_id']): ?>
            <div class="admin-notice" style="background: rgba(255,170,0,0.2); color: #ffaa00; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(255,170,0,0.3);">
                <strong>Mode administration :</strong> Vous modifiez le profil d'un autre utilisateur.
                <a href="?action=admin_users" class="btn-secondary" style="float: right; background: transparent; border: 1px solid #ffaa00; color: #ffaa00; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block;">← Retour aux utilisateurs</a>
            </div>
        <?php endif; ?>

        <form class="profile-form" method="POST" action="?action=edit_profile" id="profileForm" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 40px; margin-top: 30px;">
            <input type="hidden" name="user_id" value="<?php echo $editing_user_id; ?>">
            
            <!-- Informations personnelles -->
            <div class="form-section">
                <h2>Informations personnelles</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom *</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required 
                               value="<?php echo htmlspecialchars($profile['first_name']); ?>"
                               placeholder="Votre prénom">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nom *</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required 
                               value="<?php echo htmlspecialchars($profile['last_name']); ?>"
                               placeholder="Votre nom">
                    </div>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Nom d'utilisateur Discord -->
            <div class="form-section">
                <h2>Nom d'utilisateur Discord</h2>
                <div class="form-group">
                    <input type="text" id="discord" name="discord" class="form-control" 
                           value="<?php echo htmlspecialchars($profile['discord']); ?>"
                           placeholder="Votre pseudo Discord (ex: User#1234)">
                </div>
            </div>

            <!-- Informations de localisation -->
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Pays de résidence</label>
                        <select id="country" name="country" class="form-control">
                            <option value="">Sélectionner un pays</option>
                            <option value="fr" <?php echo ($profile['country'] == 'fr') ? 'selected' : ''; ?>>France</option>
                            <option value="tn" <?php echo ($profile['country'] == 'tn') ? 'selected' : ''; ?>>Tunisie</option>
                            <option value="ca" <?php echo ($profile['country'] == 'ca') ? 'selected' : ''; ?>>Canada</option>
                            <option value="be" <?php echo ($profile['country'] == 'be') ? 'selected' : ''; ?>>Belgique</option>
                            <option value="ch" <?php echo ($profile['country'] == 'ch') ? 'selected' : ''; ?>>Suisse</option>
                            <option value="dz" <?php echo ($profile['country'] == 'dz') ? 'selected' : ''; ?>>Algérie</option>
                            <option value="ma" <?php echo ($profile['country'] == 'ma') ? 'selected' : ''; ?>>Maroc</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationalité</label>
                        <select id="nationality" name="nationality" class="form-control">
                            <option value="">Sélectionner une nationalité</option>
                            <option value="fr" <?php echo ($profile['nationality'] == 'fr') ? 'selected' : ''; ?>>Française</option>
                            <option value="tn" <?php echo ($profile['nationality'] == 'tn') ? 'selected' : ''; ?>>Tunisienne</option>
                            <option value="ca" <?php echo ($profile['nationality'] == 'ca') ? 'selected' : ''; ?>>Canadienne</option>
                            <option value="be" <?php echo ($profile['nationality'] == 'be') ? 'selected' : ''; ?>>Belge</option>
                            <option value="ch" <?php echo ($profile['nationality'] == 'ch') ? 'selected' : ''; ?>>Suisse</option>
                            <option value="dz" <?php echo ($profile['nationality'] == 'dz') ? 'selected' : ''; ?>>Algérienne</option>
                            <option value="ma" <?php echo ($profile['nationality'] == 'ma') ? 'selected' : ''; ?>>Marocaine</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gender">Genre</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">Sélectionner un genre</option>
                        <option value="male" <?php echo ($profile['gender'] == 'male') ? 'selected' : ''; ?>>Masculin</option>
                        <option value="female" <?php echo ($profile['gender'] == 'female') ? 'selected' : ''; ?>>Féminin</option>
                        <option value="other" <?php echo ($profile['gender'] == 'other') ? 'selected' : ''; ?>>Autre</option>
                        <option value="prefer_not_say" <?php echo ($profile['gender'] == 'prefer_not_say') ? 'selected' : ''; ?>>Je préfère ne pas répondre</option>
                    </select>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Date de naissance -->
            <div class="form-section">
                <h2>Date de naissance</h2>
                <div class="form-group">
                    <input type="date" id="birth_date" name="birth_date" class="form-control" required
                           value="<?php echo htmlspecialchars($profile['birth_date']); ?>">
                    <div class="form-hint">Vous devez avoir au moins 10 ans</div>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Niveau de carrière -->
            <div class="form-section">
                <h2>Niveau de carrière</h2>
                <div class="form-group">
                    <label for="career_level">Niveau de carrière</label>
                    <select id="career_level" name="career_level" class="form-control">
                        <option value="">Sélectionner un niveau</option>
                        <option value="student" <?php echo ($profile['career_level'] == 'student') ? 'selected' : ''; ?>>Étudiant</option>
                        <option value="junior" <?php echo ($profile['career_level'] == 'junior') ? 'selected' : ''; ?>>Débutant</option>
                        <option value="mid" <?php echo ($profile['career_level'] == 'mid') ? 'selected' : ''; ?>>Intermédiaire</option>
                        <option value="senior" <?php echo ($profile['career_level'] == 'senior') ? 'selected' : ''; ?>>Confirmé</option>
                        <option value="expert" <?php echo ($profile['career_level'] == 'expert') ? 'selected' : ''; ?>>Expert</option>
                        <option value="manager" <?php echo ($profile['career_level'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="expertise">Domaines d'expertise</label>
                        <select id="expertise" name="expertise" class="form-control">
                            <option value="">Sélectionner un domaine</option>
                            <option value="web" <?php echo ($profile['expertise'] == 'web') ? 'selected' : ''; ?>>Développement Web</option>
                            <option value="mobile" <?php echo ($profile['expertise'] == 'mobile') ? 'selected' : ''; ?>>Développement Mobile</option>
                            <option value="game" <?php echo ($profile['expertise'] == 'game') ? 'selected' : ''; ?>>Développement de Jeux</option>
                            <option value="design" <?php echo ($profile['expertise'] == 'design') ? 'selected' : ''; ?>>Design</option>
                            <option value="data" <?php echo ($profile['expertise'] == 'data') ? 'selected' : ''; ?>>Data Science</option>
                            <option value="ai" <?php echo ($profile['expertise'] == 'ai') ? 'selected' : ''; ?>>Intelligence Artificielle</option>
                            <option value="cyber" <?php echo ($profile['expertise'] == 'cyber') ? 'selected' : ''; ?>>Cybersécurité</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tech_stack">Stack technologique</label>
                        <select id="tech_stack" name="tech_stack" class="form-control">
                            <option value="">Sélectionner une technologie</option>
                            <option value="js" <?php echo ($profile['tech_stack'] == 'js') ? 'selected' : ''; ?>>JavaScript</option>
                            <option value="python" <?php echo ($profile['tech_stack'] == 'python') ? 'selected' : ''; ?>>Python</option>
                            <option value="java" <?php echo ($profile['tech_stack'] == 'java') ? 'selected' : ''; ?>>Java</option>
                            <option value="csharp" <?php echo ($profile['tech_stack'] == 'csharp') ? 'selected' : ''; ?>>C#</option>
                            <option value="php" <?php echo ($profile['tech_stack'] == 'php') ? 'selected' : ''; ?>>PHP</option>
                            <option value="react" <?php echo ($profile['tech_stack'] == 'react') ? 'selected' : ''; ?>>React</option>
                            <option value="vue" <?php echo ($profile['tech_stack'] == 'vue') ? 'selected' : ''; ?>>Vue.js</option>
                            <option value="angular" <?php echo ($profile['tech_stack'] == 'angular') ? 'selected' : ''; ?>>Angular</option>
                            <option value="node" <?php echo ($profile['tech_stack'] == 'node') ? 'selected' : ''; ?>>Node.js</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Fuseau horaire -->
            <div class="form-section">
                <h2>Fuseau horaire</h2>
                <div class="form-group">
                    <label for="timezone">Fuseau horaire</label>
                    <select id="timezone" name="timezone" class="form-control">
                        <option value="Europe/Paris" <?php echo ($profile['timezone'] == 'Europe/Paris') ? 'selected' : ''; ?>>Paris (GMT+1)</option>
                        <option value="Africa/Tunis" <?php echo ($profile['timezone'] == 'Africa/Tunis') ? 'selected' : ''; ?>>Tunis (GMT+1)</option>
                        <option value="America/Montreal" <?php echo ($profile['timezone'] == 'America/Montreal') ? 'selected' : ''; ?>>Montréal (GMT-4)</option>
                        <option value="Europe/London" <?php echo ($profile['timezone'] == 'Europe/London') ? 'selected' : ''; ?>>Londres (GMT+0)</option>
                        <option value="America/New_York" <?php echo ($profile['timezone'] == 'America/New_York') ? 'selected' : ''; ?>>New York (GMT-5)</option>
                    </select>
                    <div class="form-hint">Affecte la façon dont vous voyez les dates et heures</div>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Sécurité biométrique -->
            <div class="form-section" style="border-bottom: none;">
                <h2>🎭 Sécurité biométrique</h2>
                <p class="form-hint" style="margin-bottom: 20px;">
                    Activez la reconnaissance faciale pour une connexion rapide et sécurisée sans mot de passe.
                </p>
                
                <?php if($editing_user_id == $_SESSION['user_id']): // Ne montrer que pour le profil personnel ?>
                    <div id="faceAuthSection">
                        <div class="form-group">
                            <div style="background: rgba(0,209,255,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid var(--accent-color);">
                                <p style="margin: 0; font-size: 0.95em;">
                                    <strong>État  :</strong> 
                                    <span id="faceStatus" style="color: var(--accent-color);">Chargement...</span>
                                </p>
                                <p id="faceRegisteredDate" style="margin: 10px 0 0 0; font-size: 0.85em; color: rgba(255,255,255,0.6);"></p>
                            </div>
                        </div>
                        
                        <div class="form-group" id="faceActions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="?action=face_registration" class="btn-primary" id="registerFaceBtn" style="display: none; text-decoration: none; text-align: center; margin: 0;">
                                📸 Enregistrer mon visage
                            </a>
                            <button type="button" class="btn-secondary" id="updateFaceBtn" style="display: none;">
                                🔄 Mettre à jour mon visage
                            </button>
                            <button type="button" class="btn-secondary" id="removeFaceBtn" style="display: none; background: rgba(255,68,68,0.2); border-color: #ff4444; color: #ff4444;">
                                🗑️ Supprimer mes données faciales
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: rgba(255,255,255,0.5); font-style: italic;">
                        La gestion de la reconnaissance faciale n'est disponible que pour votre propre profil.
                    </p>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary">Mettre à jour le profil</button>
                <?php if($is_admin && $editing_user_id != $_SESSION['user_id']): ?>
                    <a href="?action=admin_users" class="btn-secondary">Annuler</a>
                <?php else: ?>
                    <a href="?action=profile" class="btn-secondary">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
<script>
// Validation complète du formulaire de profil
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const firstName = document.getElementById('first_name');
    const lastName = document.getElementById('last_name');
    const birthDate = document.getElementById('birth_date');
    const discord = document.getElementById('discord');
    
    // Expressions régulières
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']{2,50}$/;
    const discordRegex = /^[a-zA-Z0-9_]{2,32}$/;
    
    // Validation en temps réel
    firstName.addEventListener('input', validateFirstName);
    lastName.addEventListener('input', validateLastName);
    birthDate.addEventListener('input', validateBirthDate);
    discord.addEventListener('input', validateDiscord);
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const isFirstNameValid = validateFirstName();
        const isLastNameValid = validateLastName();
        const isBirthDateValid = validateBirthDate();
        const isDiscordValid = validateDiscord();
        
        if (isFirstNameValid && isLastNameValid && isBirthDateValid && isDiscordValid) {
            // Sanitization finale
            sanitizeForm();
            form.submit();
        } else {
            alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
            scrollToFirstError();
        }
    });
    
    function validateFirstName() {
        const value = firstName.value.trim();
        
        if (value === '') {
            showFieldError(firstName, "Le prénom est requis");
            return false;
        }
        
        if (value.length < 2) {
            showFieldError(firstName, "Minimum 2 caractères");
            return false;
        }
        
        if (value.length > 50) {
            showFieldError(firstName, "Maximum 50 caractères");
            return false;
        }
        
        if (!nameRegex.test(value)) {
            showFieldError(firstName, "Caractères autorisés : lettres, espaces, tirets et apostrophes");
            return false;
        }
        
        showFieldSuccess(firstName);
        return true;
    }
    
    function validateLastName() {
        const value = lastName.value.trim();
        
        if (value === '') {
            showFieldError(lastName, "Le nom est requis");
            return false;
        }
        
        if (value.length < 2) {
            showFieldError(lastName, "Minimum 2 caractères");
            return false;
        }
        
        if (value.length > 50) {
            showFieldError(lastName, "Maximum 50 caractères");
            return false;
        }
        
        if (!nameRegex.test(value)) {
            showFieldError(lastName, "Caractères autorisés : lettres, espaces, tirets et apostrophes");
            return false;
        }
        
        showFieldSuccess(lastName);
        return true;
    }
    
    function validateBirthDate() {
        const value = birthDate.value;
        
        if (value === '') {
            showFieldError(birthDate, "La date de naissance est requise");
            return false;
        }
        
        const birthDateObj = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDateObj.getFullYear();
        const monthDiff = today.getMonth() - birthDateObj.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
            age--;
        }
        
        if (age < 10) {
            showFieldError(birthDate, "Vous devez avoir au moins 10 ans");
            return false;
        }
        
        if (age > 120) {
            showFieldError(birthDate, "Date de naissance invalide");
            return false;
        }
        
        // Vérifier que la date n'est pas dans le futur
        if (birthDateObj > today) {
            showFieldError(birthDate, "La date de naissance ne peut pas être dans le futur");
            return false;
        }
        
        showFieldSuccess(birthDate);
        return true;
    }
    
    function validateDiscord() {
        const value = discord.value.trim();
        
        // Discord est optionnel
        if (value === '') {
            showFieldSuccess(discord);
            return true;
        }
        
        if (!discordRegex.test(value)) {
            showFieldError(discord, "Seulement lettres, chiffres et underscores (2-32 caractères)");
            return false;
        }
        
        showFieldSuccess(discord);
        return true;
    }
    
    function showFieldError(input, message) {
        // Retirer les classes existantes
        input.classList.remove('success', 'error');
        
        // Ajouter la classe d'erreur
        input.classList.add('error');
        
        // Créer ou mettre à jour le message d'erreur
        let errorElement = input.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('span');
            errorElement.className = 'error-message';
            input.parentNode.insertBefore(errorElement, input.nextSibling);
        }
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    function showFieldSuccess(input) {
        // Retirer les classes existantes
        input.classList.remove('success', 'error');
        
        // Ajouter la classe de succès
        input.classList.add('success');
        
        // Cacher le message d'erreur s'il existe
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.style.display = 'none';
        }
    }
    
    function sanitizeForm() {
        // Nettoyer toutes les entrées texte
        const textInputs = form.querySelectorAll('input[type="text"], textarea');
        textInputs.forEach(input => {
            input.value = input.value.replace(/[<>]/g, '').trim();
        });
    }
    
    function scrollToFirstError() {
        const firstError = form.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
    
    // Validation initiale
    validateFirstName();
    validateLastName();
    validateBirthDate();
    validateDiscord();
});

// Ajouter le CSS pour les messages d'erreur
const style = document.createElement('style');
style.textContent = `
    .error-message {
        color: #ff4444;
        font-size: 0.85em;
        margin-top: 5px;
        display: block;
    }
    
    .form-control.error {
        border-color: #ff4444;
        box-shadow: 0 0 0 2px rgba(255,68,68,0.2);
    }
    
    .form-control.success {
        border-color: #00ff88;
        box-shadow: 0 0 0 2px rgba(0,255,136,0.2);
    }
`;
document.head.appendChild(style);

// Gestion de la reconnaissance faciale
<?php if($editing_user_id == $_SESSION['user_id']): ?>
document.addEventListener('DOMContentLoaded', async function() {
    const faceStatus = document.getElementById('faceStatus');
    const faceRegisteredDate = document.getElementById('faceRegisteredDate');
    const registerFaceBtn = document.getElementById('registerFaceBtn');
    const updateFaceBtn = document.getElementById('updateFaceBtn');
    const removeFaceBtn = document.getElementById('removeFaceBtn');
    
    // Vérifier l'état de la reconnaissance faciale
    try {
        const response = await fetch('?action=face_info');
        const data = await response.json();
        
        if (data.success && data.info) {
            const info = data.info;
            
            if (info.has_face == 1) {
                faceStatus.textContent = info.face_enabled == 1 ? '✓ Activée' : '✓ Enregistrée (désactivée)';
                faceStatus.style.color = info.face_enabled == 1 ? '#00ff88' : '#ffaa00';
                
                if (info.face_registered_at) {
                    const date = new Date(info.face_registered_at);
                    faceRegisteredDate.textContent = 'Enregistré le ' + date.toLocaleDateString('fr-FR');
                }
                
                updateFaceBtn.style.display = 'inline-block';
                removeFaceBtn.style.display = 'inline-block';
            } else {
                faceStatus.textContent = '✗ Non configurée';
                faceStatus.style.color = '#888';
                registerFaceBtn.style.display = 'inline-block';
            }
        } else {
            faceStatus.textContent = 'Erreur de chargement';
            faceStatus.style.color = '#ff4444';
        }
    } catch (error) {
        console.error('Erreur:', error);
        faceStatus.textContent = 'Erreur de chargement';
        faceStatus.style.color = '#ff4444';
    }
    
    // Gérer la mise à jour du visage
    if (updateFaceBtn) {
        updateFaceBtn.addEventListener('click', function() {
            window.location.href = '?action=face_registration';
        });
    }
    
    // Gérer la suppression
    if (removeFaceBtn) {
        removeFaceBtn.addEventListener('click', async function() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer vos données de reconnaissance faciale ?')) {
                return;
            }
            
            try {
                const response = await fetch('?action=remove_face', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Données faciales supprimées avec succès');
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.error || 'Une erreur est survenue'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de communication avec le serveur');
            }
        });
    }
});
<?php endif; ?>
</script>

<script>
// Forcer le masquage du loader immédiatement
(function() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
        loader.classList.add('hidden');
    }
    
    // Aussi au chargement de la page
    if (document.readyState === 'complete') {
        if (loader) {
            loader.style.display = 'none';
        }
    } else {
        window.addEventListener('load', function() {
            if (loader) {
                loader.style.display = 'none';
            }
        });
    }
})();
</script>
    </div>
</section>