<?php
// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Vérifier si le profil est déjà complété et rediriger si c'est le cas
if(isset($_SESSION['profile_completed']) && $_SESSION['profile_completed']) {
    header('Location: index.php?action=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier votre profil | Gaming & Impact Social</title>
    <style>
        :root {
            --primary-bg: #0e0e16;
            --secondary-bg: #141429;
            --card-bg: #1a1a2e;
            --accent-color: #00d1ff;
            --text-color: #ffffff;
            --border-radius: 12px;
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-bg);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease-out;
        }

        .header h1 {
            color: var(--accent-color);
            font-size: 3em;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(0, 209, 255, 0.3);
        }

        .header p {
            color: #cccccc;
            font-size: 1.2em;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .profile-form {
            background: var(--card-bg);
            padding: 50px;
            border-radius: var(--border-radius);
            border: 1px solid var(--input-border);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--input-border);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h2 {
            color: var(--accent-color);
            margin-bottom: 25px;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #a0a0b0;
            font-weight: 500;
            font-size: 0.95em;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #ffffff;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(0, 209, 255, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus + label {
            color: var(--accent-color);
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            cursor: pointer;
        }

        select.form-control option {
            background-color: var(--secondary-bg);
            color: var(--text-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-hint {
            color: #666;
            font-size: 0.85em;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), #00b8e6);
            color: #000;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1em;
            display: block;
            margin: 50px auto 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(0, 209, 255, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 209, 255, 0.3);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .error-message {
            color: #ff3333; /* Bright red for visibility */
            font-size: 0.9em;
            margin-top: 8px;
            display: none;
            font-weight: 500;
            animation: fadeIn 0.3s ease-out;
        }

        .form-control.error {
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.05);
        }

        .form-control.success {
            border-color: #00ff88;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-form {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Complétez votre profil</h1>
            <p>Dites-nous en plus sur vous pour personnaliser votre expérience</p>
        </div>

        <?php if(isset($errors) && !empty($errors)): ?>
            <div style="background: rgba(255, 77, 77, 0.2); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Erreurs :</strong><br>
                <?php foreach($errors as $error): ?>
                    • <?php echo htmlspecialchars($error); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=complete_profile" method="POST" class="profile-form">
            <!-- Informations personnelles -->
            <div class="form-section">
                <h2>Informations personnelles</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" 
                               value="<?php echo isset($profile['first_name']) ? htmlspecialchars($profile['first_name']) : ''; ?>"
                               placeholder="Votre prénom">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" 
                               value="<?php echo isset($profile['last_name']) ? htmlspecialchars($profile['last_name']) : ''; ?>"
                               placeholder="Votre nom">
                    </div>
                </div>
            </div>

            <!-- Nom d'utilisateur Discord -->
            <div class="form-section">
                <h2>Nom d'utilisateur Discord</h2>
                <div class="form-group">
                    <input type="text" id="discord" name="discord" class="form-control" 
                           value="<?php echo isset($profile['discord']) ? htmlspecialchars($profile['discord']) : ''; ?>"
                           placeholder="Votre pseudo Discord (ex: User#1234)">
                </div>
            </div>

            <!-- Informations de localisation -->
            <div class="form-section">
                <h2>Localisation & Identité</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Pays de résidence</label>
                        <input type="text" id="country" name="country" class="form-control" 
                               value="<?php echo isset($profile['country']) ? htmlspecialchars($profile['country']) : ''; ?>"
                               placeholder="Ex: France, Canada...">
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationalité</label>
                        <input type="text" id="nationality" name="nationality" class="form-control" 
                               value="<?php echo isset($profile['nationality']) ? htmlspecialchars($profile['nationality']) : ''; ?>"
                               placeholder="Ex: Française, Canadienne...">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gender">Genre</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">Sélectionner un genre</option>
                        <option value="male" <?php echo (isset($profile['gender']) && $profile['gender'] == 'male') ? 'selected' : ''; ?>>Masculin</option>
                        <option value="female" <?php echo (isset($profile['gender']) && $profile['gender'] == 'female') ? 'selected' : ''; ?>>Féminin</option>
                        <option value="other" <?php echo (isset($profile['gender']) && $profile['gender'] == 'other') ? 'selected' : ''; ?>>Autre</option>
                        <option value="prefer_not_say" <?php echo (isset($profile['gender']) && $profile['gender'] == 'prefer_not_say') ? 'selected' : ''; ?>>Je préfère ne pas répondre</option>
                    </select>
                </div>
            </div>

            <!-- Date de naissance -->
            <div class="form-section">
                <h2>Date de naissance</h2>
                <div class="form-group">
                    <input type="date" id="birth_date" name="birth_date" class="form-control" 
                           value="<?php echo isset($profile['birth_date']) ? htmlspecialchars($profile['birth_date']) : ''; ?>">
                    <div class="form-hint">Vous devez avoir au moins 10 ans pour vous inscrire</div>
                </div>
            </div>

            <!-- Niveau de carrière -->
            <div class="form-section">
                <h2>Niveau de carrière</h2>
                <div class="form-group">
                    <label for="career_level">Niveau de carrière</label>
                    <select id="career_level" name="career_level" class="form-control">
                        <option value="">Sélectionner un niveau</option>
                        <option value="student" <?php echo (isset($profile['career_level']) && $profile['career_level'] == 'student') ? 'selected' : ''; ?>>Étudiant</option>
                        <option value="junior" <?php echo (isset($profile['career_level']) && $profile['career_level'] == 'junior') ? 'selected' : ''; ?>>Débutant</option>
                        <option value="mid" <?php echo (isset($profile['career_level']) && $profile['career_level'] == 'mid') ? 'selected' : ''; ?>>Intermédiaire</option>
                        <option value="senior" <?php echo (isset($profile['career_level']) && $profile['career_level'] == 'senior') ? 'selected' : ''; ?>>Confirmé</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="expertise">Domaines d'expertise</label>
                        <input type="text" id="expertise" name="expertise" class="form-control" 
                               value="<?php echo isset($profile['expertise']) ? htmlspecialchars($profile['expertise']) : ''; ?>"
                               placeholder="Ex: Web, Mobile, Game Design...">
                    </div>
                    <div class="form-group">
                        <label for="tech_stack">Stack technologique</label>
                        <input type="text" id="tech_stack" name="tech_stack" class="form-control" 
                               value="<?php echo isset($profile['tech_stack']) ? htmlspecialchars($profile['tech_stack']) : ''; ?>"
                               placeholder="Ex: PHP, React, Unity...">
                    </div>
                </div>
            </div>

            <!-- Fuseau horaire -->
            <div class="form-section">
                <h2>Fuseau horaire</h2>
                <div class="form-group">
                    <label for="timezone">Fuseau horaire</label>
                    <input type="text" id="timezone" name="timezone" class="form-control" 
                           value="<?php echo isset($profile['timezone']) ? htmlspecialchars($profile['timezone']) : 'Europe/Paris'; ?>"
                           placeholder="Ex: Europe/Paris, GMT+1...">
                    <div class="form-hint">Affecte la façon dont vous voyez les dates et heures sur nos produits.</div>
                </div>
            </div>

            <button type="submit" class="btn-primary">Sauvegarder le profil</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.profile-form');
            const inputs = form.querySelectorAll('input, select');

            // Listes de validation (Identiques au contrôleur pour cohérence)
            const validCountries = [
                'France', 'Belgique', 'Suisse', 'Canada', 'Tunisie', 'Maroc', 'Algérie', 'Sénégal', 'Côte d\'Ivoire', 
                'États-Unis', 'Royaume-Uni', 'Allemagne', 'Espagne', 'Italie', 'Portugal', 'Autriche', 'Pays-Bas', 
                'Suède', 'Norvège', 'Danemark', 'Finlande', 'Irlande', 'Pologne', 'Russie', 'Chine', 'Japon', 
                'Corée du Sud', 'Inde', 'Brésil', 'Argentine', 'Mexique', 'Australie', 'Nouvelle-Zélande', 'Luxembourg'
            ];

            const validNationalities = [
                'Française', 'Belge', 'Suisse', 'Canadienne', 'Tunisienne', 'Marocaine', 'Algérienne', 'Sénégalaise', 
                'Ivoirienne', 'Américaine', 'Britannique', 'Allemande', 'Espagnole', 'Italienne', 'Portugaise', 
                'Autrichienne', 'Néerlandaise', 'Suédoise', 'Norvégienne', 'Danoise', 'Finlandaise', 'Irlandaise', 
                'Polonaise', 'Russe', 'Chinoise', 'Japonaise', 'Coréenne', 'Indienne', 'Brésilienne', 'Argentine', 
                'Mexicaine', 'Australienne', 'Néo-Zélandaise', 'Luxembourgeoise'
            ];

            // Validation rules
            const rules = {
                first_name: {
                    required: true,
                    minLength: 2,
                    pattern: /^[a-zA-ZÀ-ÿ\s\-\']+$/,
                    message: "Le prénom doit contenir au moins 2 lettres (pas de chiffres)"
                },
                last_name: {
                    required: true,
                    minLength: 2,
                    pattern: /^[a-zA-ZÀ-ÿ\s\-\']+$/,
                    message: "Le nom doit contenir au moins 2 lettres (pas de chiffres)"
                },
                country: {
                    required: true,
                    validate: (value) => validCountries.some(c => c.toLowerCase() === value.toLowerCase()),
                    message: "Veuillez choisir un pays valide (ex: France, Tunisie, Canada...)"
                },
                nationality: {
                    required: true,
                    validate: (value) => validNationalities.some(n => n.toLowerCase() === value.toLowerCase()),
                    message: "Veuillez choisir une nationalité valide (ex: Française, Tunisienne...)"
                },
                discord: {
                    required: false,
                    pattern: /^.{3,32}$/,
                    message: "Format Discord invalide (3-32 caractères)"
                },
                expertise: {
                    required: false,
                    minLength: 2,
                    message: "Minimum 2 caractères"
                },
                tech_stack: {
                    required: false,
                    minLength: 2,
                    message: "Minimum 2 caractères"
                },
                timezone: {
                    required: false,
                    minLength: 2,
                    message: "Fuseau horaire invalide"
                },
                birth_date: {
                    required: true,
                    validate: (value) => {
                        const birthDate = new Date(value);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const m = today.getMonth() - birthDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        return age >= 10 && age <= 100;
                    },
                    message: "Vous devez avoir entre 10 et 100 ans"
                }
            };

            // Real-time validation
            inputs.forEach(input => {
                // Add error message container if not exists
                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    input.parentNode.appendChild(errorDiv);
                }

                input.addEventListener('input', () => validateField(input));
                input.addEventListener('blur', () => validateField(input));
            });

            function validateField(input) {
                const name = input.name;
                const value = input.value.trim();
                const rule = rules[name];
                const errorDiv = input.parentNode.querySelector('.error-message');

                // Reset status
                input.classList.remove('error', 'success');
                errorDiv.style.display = 'none';

                if (!rule) return true;

                // Check required
                if (rule.required && !value) {
                    showError(input, "Ce champ est requis");
                    return false;
                }

                // If empty and not required, it's valid
                if (!value && !rule.required) {
                    return true;
                }

                // Check min length
                if (rule.minLength && value.length < rule.minLength) {
                    showError(input, rule.message);
                    return false;
                }

                // Check pattern
                if (rule.pattern && !rule.pattern.test(value)) {
                    showError(input, rule.message);
                    return false;
                }

                // Custom validation
                if (rule.validate && !rule.validate(value)) {
                    showError(input, rule.message);
                    return false;
                }

                // Success
                input.classList.add('success');
                return true;
            }

            function showError(input, message) {
                const errorDiv = input.parentNode.querySelector('.error-message');
                input.classList.add('error');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
</body>
</html>