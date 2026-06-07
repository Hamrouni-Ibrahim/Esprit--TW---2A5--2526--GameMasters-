<?php
class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    // Inscription
    public function register($data) {
        // Valider reCAPTCHA
        require_once __DIR__ . '/../models/CaptchaHelper.php';
        $remoteIP = $_SERVER['REMOTE_ADDR'] ?? null;
        $captchaResult = CaptchaHelper::verify($data['g-recaptcha-response'] ?? '', $remoteIP);
        
        if(!$captchaResult['success']) {
            return ['success' => false, 'errors' => [$captchaResult['error'] ?? 'Vérification reCAPTCHA échouée.']];
        }
        
        $errors = $this->validateRegistrationData($data);
        
        if(!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->userModel->username = $data['username'];
        $this->userModel->email = $data['email'];
        $this->userModel->password = $data['password'];
        $this->userModel->role = 'player';
        $this->userModel->status = 'active';
        
        // Gestion de l'avatar
        $this->userModel->avatar = ''; // Valeur par défaut
        
        // 1. Upload d'image
        if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/avatars/';
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $extension = strtolower($fileInfo['extension']);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(in_array($extension, $allowedExtensions)) {
                $uniqueName = uniqid('avatar_') . '.' . $extension;
                $targetFile = $uploadDir . $uniqueName;
                
                if(move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                    $this->userModel->avatar = 'public/uploads/avatars/' . $uniqueName;
                }
            }
        } 
        // 2. Sélection d'un avatar prédéfini
        elseif(!empty($data['avatar_preset'])) {
            $presetPath = $data['avatar_preset'];
            // Validation: avatar1.jpg à avatar18.jpg
            if(preg_match('/^avatar[0-9]+\.jpg$/', $presetPath)) {
                $this->userModel->avatar = 'public/assets/img/avatars/' . $presetPath;
                error_log("AuthController - Avatar preset selected: " . $this->userModel->avatar);
            } else {
                error_log("AuthController - Invalid avatar preset format: " . $presetPath);
            }
        }
        
        // 3. Avatar par défaut (Lettre)
        if(empty($this->userModel->avatar)) {
            require_once __DIR__ . '/../models/AvatarGenerator.php';
            $uploadDir = __DIR__ . '/../public/uploads/avatars/';
            $generatedAvatar = AvatarGenerator::generateDefaultAvatar($data['username'], $uploadDir);
            
            if($generatedAvatar) {
                $this->userModel->avatar = 'public/uploads/avatars/' . $generatedAvatar;
                error_log("AuthController - Default avatar generated: " . $this->userModel->avatar);
            } else {
                error_log("AuthController - Failed to generate default avatar");
            }
        }
        
        error_log("AuthController - Final avatar value before save: " . ($this->userModel->avatar ?? 'NULL'));
        
        // PRODUCTION : Générer un code et envoyer l'email de vérification
        require_once __DIR__ . '/../models/EmailHelper.php';
        $verificationCode = EmailHelper::generateVerificationCode(6);

        // ÉTAPE 1: Créer le compte (email non vérifié)
        $this->userModel->email_verified = 0;
        $this->userModel->verification_code = $verificationCode;

        if($this->userModel->create()) {
            // ÉTAPE 2: Envoyer l'email de vérification
            $emailHelper = new EmailHelper();
            $emailSent = $emailHelper->sendVerificationEmail(
                $this->userModel->email,
                $this->userModel->username,
                $verificationCode
            );
            
            // Vérifier si SMTP est configuré
            $isSmtpConfigured = $emailHelper->isSmtpConfigured();
            $isDevelopment = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
            
            if($emailSent) {
                // Email envoyé avec succès
                // ÉTAPE 3: Stocker les infos dans la session pour la page de vérification
                $_SESSION['pending_verification_user_id'] = $this->userModel->id;
                $_SESSION['pending_verification_email'] = $this->userModel->email;
                
                $message = 'Compte créé avec succès! Un code de vérification a été envoyé à votre email.';
                
                return [
                    'success' => true,
                    'message' => $message,
                    'requires_verification' => true, // IMPORTANT: Active la vérification
                    'user_id' => $this->userModel->id
                ];
            } else {
                // Si l'email échoue
                if (!$isSmtpConfigured) {
                    // SMTP non configuré - logger et permettre quand même (en développement)
                    error_log("⚠️ SMTP non configuré - L'email n'a pas été envoyé à: " . $this->userModel->email);
                    
                    // Stocker quand même pour permettre la vérification (en dev)
                    $_SESSION['pending_verification_user_id'] = $this->userModel->id;
                    $_SESSION['pending_verification_email'] = $this->userModel->email;
                    
                    // ON N'AFFICHE PLUS LE CODE pour obéir à la demande utilisateur
                    // $_SESSION['dev_verification_code'] = $verificationCode;
                    
                    return [
                        'success' => true,
                        // Message générique, pas de code affiché
                        'message' => 'Compte créé! ⚠️ SMTP non configuré - Veuillez vérifier vos logs serveur (email_log.txt) pour le code.',
                        'requires_verification' => true,
                        'user_id' => $this->userModel->id
                    ];
                } else {
                    // SMTP configuré mais échec - erreur réelle
                    error_log("❌ Échec envoi email malgré SMTP configuré pour: " . $this->userModel->email);
                    $this->userModel->delete();
                    return ['success' => false, 'errors' => ['Erreur lors de l\'envoi de l\'email de vérification. Veuillez vérifier votre configuration SMTP.']];
                }
            }
        }
        
        return ['success' => false, 'errors' => ['Erreur lors de l\'inscription']];
    }
    
    // Vérifier le code d'email
    public function verifyEmail($userId, $code) {
        if($this->userModel->verifyEmail($userId, $code)) {
            // Connecter l'utilisateur après vérification
            $this->userModel->id = $userId;
            if($this->userModel->readOne()) {
                $_SESSION['user_id'] = $this->userModel->id;
                $_SESSION['username'] = $this->userModel->username;
                $_SESSION['role'] = $this->userModel->role;
                $_SESSION['email'] = $this->userModel->email;
                $_SESSION['avatar'] = $this->userModel->avatar ?? null;
                $_SESSION['profile_completed'] = false;
                
                error_log("✅ Email verified - User: " . $this->userModel->id . 
                         ", Avatar: " . ($this->userModel->avatar ?? 'NULL'));
                
                unset($_SESSION['pending_verification_user_id']);
                unset($_SESSION['pending_verification_email']);
                
                return ['success' => true, 'message' => 'Email vérifié avec succès!'];
            }
        }
        return ['success' => false, 'errors' => ['Code de vérification invalide ou expiré.']];
    }
    
    // Renvoyer le code de vérification
    public function resendVerificationCode($userId) {
        $this->userModel->id = $userId;
        if($this->userModel->readOne()) {
            require_once __DIR__ . '/../models/EmailHelper.php';
            $verificationCode = EmailHelper::generateVerificationCode(6);
            
            if($this->userModel->updateVerificationCode($userId, $verificationCode)) {
                $emailHelper = new EmailHelper();
                $emailSent = $emailHelper->sendVerificationEmail(
                    $this->userModel->email,
                    $this->userModel->username,
                    $verificationCode
                );
                
                if($emailSent) {
                    return ['success' => true, 'message' => 'Code de vérification renvoyé avec succès!'];
                }
            }
        }
        return ['success' => false, 'errors' => ['Erreur lors de l\'envoi du code.']];
    }
    
    // Demander la réinitialisation du mot de passe
    public function requestPasswordReset($email) {
        $this->userModel->email = $email;
        
        if($this->userModel->emailExists()) {
            require_once __DIR__ . '/../models/EmailHelper.php';
            $resetCode = $this->userModel->generateResetCode($email);
            
            if($resetCode) {
                error_log("🔐 Génération code réinitialisation pour: {$email}, Code: {$resetCode}");
                
                $emailHelper = new EmailHelper();
                $emailSent = $emailHelper->sendPasswordResetEmail(
                    $email,
                    $this->userModel->username,
                    $resetCode
                );
                
                error_log("📧 Résultat envoi email à {$email}: " . ($emailSent ? 'SUCCÈS' : 'ÉCHEC'));
                
                if($emailSent) {
                    return ['success' => true, 'message' => 'Un email de réinitialisation a été envoyé à votre adresse.'];
                } else {
                    error_log("❌ Échec envoi email de réinitialisation pour: {$email}");
                }
            } else {
                error_log("❌ Échec génération code réinitialisation pour: {$email}");
            }
        } else {
            error_log("⚠️ Email non trouvé pour réinitialisation: {$email}");
        }
        
        // Ne pas révéler si l'email existe ou non (sécurité)
        return ['success' => true, 'message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.'];
    }
    
    // Réinitialiser le mot de passe
    public function resetPassword($email, $code, $newPassword) {
        $userId = $this->userModel->verifyResetCode($email, $code);
        
        if($userId) {
            // Valider le nouveau mot de passe
            if(strlen($newPassword) < 8) {
                return ['success' => false, 'errors' => ['Le mot de passe doit contenir au moins 8 caractères']];
            }
            
            if($this->userModel->resetPassword($userId, $newPassword)) {
                return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès!'];
            }
        }
        
        return ['success' => false, 'errors' => ['Code de réinitialisation invalide ou expiré.']];
    }

    // Validation de l'inscription (RENFORCÉE)
    private function validateRegistrationData($data) {
        $errors = [];
        
        // Validation du nom d'utilisateur (RENFORCÉE)
        if(empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        } else {
            $username = trim($data['username']);
            if(strlen($username) < 3) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
            } elseif(strlen($username) > 20) {
                $errors[] = "Le nom d'utilisateur ne peut pas dépasser 20 caractères";
            } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
            } elseif(preg_match('/^[0-9]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut pas être composé uniquement de chiffres";
            } elseif(!preg_match('/[a-zA-Z]/', $username)) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins une lettre";
            } elseif($this->usernameExists($username)) {
                $errors[] = "Ce nom d'utilisateur est déjà utilisé";
            }
        }
        
        // Validation de l'email (RENFORCÉE)
        if(empty($data['email'])) {
            $errors[] = "L'email est requis";
        } else {
            $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif(strlen($email) > 100) {
                $errors[] = "L'email ne peut pas dépasser 100 caractères";
            } elseif($this->isDisposableEmail($email)) {
                $errors[] = "Les emails jetables ne sont pas autorisés";
            } else {
                $this->userModel->email = $email;
                if($this->userModel->emailExists()) {
                    $errors[] = "Cet email est déjà utilisé";
                }
            }
        }
        
        // Validation du mot de passe (RENFORCÉE)
        if(empty($data['password'])) {
            $errors[] = "Le mot de passe est requis";
        } else {
            $password = $data['password'];
            if(strlen($password) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
            } elseif(strlen($password) > 128) {
                $errors[] = "Le mot de passe est trop long";
            } elseif(!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins une majuscule";
            } elseif(!preg_match('/[a-z]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins une minuscule";
            } elseif(!preg_match('/[0-9]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins un chiffre";
            } elseif($data['password'] !== $data['confirm_password']) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
        }
        
        // Validation des champs cachés (anti-bot)
        if(!empty($data['honeypot'])) {
            $errors[] = "Requête invalide détectée";
        }
        
        // Validation CSRF - Générer le token s'il n'existe pas
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Vérifier le token seulement s'il est fourni
        if(!empty($data['csrf_token']) && $data['csrf_token'] !== $_SESSION['csrf_token']) {
            $errors[] = "Token de sécurité invalide";
        }
        
        return $errors;
    }

    // Vérifier si le nom d'utilisateur existe
    private function usernameExists($username) {
        return $this->userModel->checkUsernameExists($username);
    }

    // Vérifier les emails jetables
    private function isDisposableEmail($email) {
        $disposableDomains = [
            'tempmail.com', 'guerrillamail.com', 'mailinator.com', '10minutemail.com',
            'yopmail.com', 'throwawaymail.com', 'fakeinbox.com', 'trashmail.com'
        ];
        
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        return in_array($domain, $disposableDomains);
    }

    // Générer un captcha d'images avec images d'internet (style reCAPTCHA) - SANS RÉPÉTITIONS
    public static function generateImageCaptcha() {
        // Catégories de captcha avec vraies images UNIQUES d'internet
        $categories = [
            'traffic_lights' => [
                'question' => 'Sélectionnez toutes les images avec des feux de circulation',
                'correct' => [
                    'https://cdn.pixabay.com/photo/2016/11/29/13/45/traffic-light-1869993_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/08/24/03/41/traffic-light-2675323_640.jpg',
                    'https://cdn.pixabay.com/photo/2018/05/17/16/03/traffic-light-3409079_640.jpg',
                    'https://cdn.pixabay.com/photo/2019/07/14/16/27/traffic-light-4337450_640.jpg',
                    'https://cdn.pixabay.com/photo/2020/06/08/22/50/traffic-light-5275183_640.jpg',
                    'https://cdn.pixabay.com/photo/2021/08/31/12/12/traffic-light-6578509_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/01/14/10/56/traffic-light-1979590_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/03/27/18/54/traffic-light-1283699_640.jpg'
                ],
                'incorrect' => [
                    'https://cdn.pixabay.com/photo/2016/11/18/17/20/car-1836417_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/06/19/21/47/tree-815317_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/03/53/architecture-1867187_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/08/17/24/fantasy-2049567_640.jpg'
                ]
            ],
            'crosswalk' => [
                'question' => 'Sélectionnez toutes les images avec un passage piéton',
                'correct' => [
                    'https://cdn.pixabay.com/photo/2016/11/22/19/15/hand-1850120_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/03/27/14/56/auto-2179220_640.jpg',
                    'https://cdn.pixabay.com/photo/2018/01/09/03/49/the-road-3070231_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/02/05/street-1867715_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/08/06/12/52/people-2591874_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/18/15/44/pedestrian-1835168_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/06/05/11/01/airport-2373727_640.jpg',
                    'https://cdn.pixabay.com/photo/2018/03/13/22/53/puzzle-3223941_640.jpg'
                ],
                'incorrect' => [
                    'https://cdn.pixabay.com/photo/2016/11/18/17/20/car-1836417_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/06/19/21/47/tree-815317_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/03/53/architecture-1867187_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/08/17/24/fantasy-2049567_640.jpg'
                ]
            ],
            'bicycle' => [
                'question' => 'Sélectionnez toutes les images avec un vélo',
                'correct' => [
                    'https://cdn.pixabay.com/photo/2016/11/18/12/49/bicycle-1834265_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/08/27/09/06/bike-909690_640.jpg',
                    'https://cdn.pixabay.com/photo/2013/07/13/11/44/bicycle-158607_640.png',
                    'https://cdn.pixabay.com/photo/2016/11/29/01/34/bicycle-1867246_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/07/15/11/31/bike-2506316_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/21/16/21/bicycle-1846269_640.jpg',
                    'https://cdn.pixabay.com/photo/2014/09/07/22/34/bicycle-438400_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/07/19/10/00/bicycle-850961_640.jpg'
                ],
                'incorrect' => [
                    'https://cdn.pixabay.com/photo/2016/11/18/17/20/car-1836417_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/06/19/21/47/tree-815317_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/03/53/architecture-1867187_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/08/17/24/fantasy-2049567_640.jpg'
                ]
            ],
            'bus' => [
                'question' => 'Sélectionnez toutes les images avec un bus',
                'correct' => [
                    'https://cdn.pixabay.com/photo/2015/10/12/15/46/bus-984316_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/18/17/46/bus-1836990_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/05/15/18/39/school-bus-2315582_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/12/13/bus-1869085_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/08/06/22/01/louvre-2596278_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/22/23/40/adventure-1851092_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/01/14/10/57/bus-1979596_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/02/19/10/56/bus-1209761_640.jpg'
                ],
                'incorrect' => [
                    'https://cdn.pixabay.com/photo/2015/06/19/21/47/tree-815317_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/03/53/architecture-1867187_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/18/12/49/bicycle-1834265_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/08/17/24/fantasy-2049567_640.jpg'
                ]
            ],
            'mountain' => [
                'question' => 'Sélectionnez toutes les images avec une montagne',
                'correct' => [
                    'https://cdn.pixabay.com/photo/2015/06/19/21/47/tree-815317_640.jpg',
                    'https://cdn.pixabay.com/photo/2013/07/18/10/56/mountain-163717_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/08/17/24/fantasy-2049567_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/12/15/13/51/polynesia-3021072_640.jpg',
                    'https://cdn.pixabay.com/photo/2018/08/14/13/23/ocean-3605547_640.jpg',
                    'https://cdn.pixabay.com/photo/2017/02/01/22/02/mountain-landscape-2031539_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/08/01/20/34/girl-1562091_640.jpg'
                ],
                'incorrect' => [
                    'https://cdn.pixabay.com/photo/2016/11/18/17/20/car-1836417_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/18/12/49/bicycle-1834265_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/13/45/traffic-light-1869993_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/10/12/15/46/bus-984316_640.jpg',
                    'https://cdn.pixabay.com/photo/2016/11/29/03/53/architecture-1867187_640.jpg',
                    'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_640.jpg'
                ]
            ]
        ];
        
        // Sélectionner une catégorie aléatoire
        $categoryKey = array_rand($categories);
        $category = $categories[$categoryKey];
        
        // Créer un ensemble de 9 images UNIQUES (6 correctes + 3 incorrectes)
        $allImages = [];
        $correctImages = [];
        
        // Mélanger les images correctes et en prendre 6 UNIQUES (sans répétition)
        $correctUrls = $category['correct'];
        shuffle($correctUrls);
        $selectedCorrect = array_values(array_unique(array_slice($correctUrls, 0, 6)));
        
        // Si on n'a pas assez d'images uniques, prendre ce qu'on a
        if(count($selectedCorrect) < 6) {
            $selectedCorrect = array_values(array_unique($correctUrls));
            $selectedCorrect = array_slice($selectedCorrect, 0, min(6, count($selectedCorrect)));
        }
        
        // Ajouter les images correctes UNIQUES
        $index = 0;
        $usedUrls = []; // Pour éviter les répétitions
        foreach($selectedCorrect as $imgUrl) {
            // Vérifier qu'on n'a pas déjà utilisé cette URL
            if(!in_array($imgUrl, $usedUrls)) {
                $id = 'correct_' . $index;
                $allImages[] = [
                    'id' => $id,
                    'image' => $imgUrl,
                    'isCorrect' => true
                ];
                $correctImages[] = $id;
                $usedUrls[] = $imgUrl;
                $index++;
            }
        }
        
        // Ajouter 3 images incorrectes UNIQUES (sans répétition)
        $incorrectUrls = $category['incorrect'];
        shuffle($incorrectUrls);
        $selectedIncorrect = array_values(array_unique(array_slice($incorrectUrls, 0, 3)));
        
        // Si on n'a pas assez d'images uniques, prendre ce qu'on a
        if(count($selectedIncorrect) < 3) {
            $selectedIncorrect = array_values(array_unique($incorrectUrls));
            $selectedIncorrect = array_slice($selectedIncorrect, 0, min(3, count($selectedIncorrect)));
        }
        
        $index = 0;
        foreach($selectedIncorrect as $imgUrl) {
            // Vérifier qu'on n'a pas déjà utilisé cette URL
            if(!in_array($imgUrl, $usedUrls)) {
                $allImages[] = [
                    'id' => 'wrong_' . $index,
                    'image' => $imgUrl,
                    'isCorrect' => false
                ];
                $usedUrls[] = $imgUrl;
                $index++;
            }
        }
        
        // Mélanger toutes les images
        shuffle($allImages);
        
        // Stocker les réponses correctes dans la session
        $_SESSION['captcha_correct_answers'] = $correctImages;
        $_SESSION['captcha_category'] = $categoryKey;
        
        return [
            'images' => $allImages,
            'question' => $category['question']
        ];
    }
    
    // Valider le captcha d'images
    public static function validateImageCaptcha($selectedImages) {
        if(!isset($_SESSION['captcha_correct_answers'])) {
            return false;
        }
        
        $correctAnswers = $_SESSION['captcha_correct_answers'];
        
        // Nettoyer la session
        unset($_SESSION['captcha_correct_answers']);
        unset($_SESSION['captcha_category']);
        
        // Vérifier que l'utilisateur a sélectionné au moins une image
        if(empty($selectedImages) || !is_array($selectedImages)) {
            return false;
        }
        
        // Vérifier que toutes les images correctes sont sélectionnées
        // et qu'aucune image incorrecte n'est sélectionnée
        $selectedCount = count($selectedImages);
        $correctCount = count($correctAnswers);
        
        // Doit sélectionner exactement toutes les images correctes
        if($selectedCount !== $correctCount) {
            return false;
        }
        
        // Vérifier que toutes les sélections sont correctes
        foreach($selectedImages as $selected) {
            if(!in_array($selected, $correctAnswers)) {
                return false;
            }
        }
        
        return true;
    }

    // Connexion
    public function login($email, $password, $recaptchaResponse = null) {
        $this->userModel->email = $email;
        
        // Debug logging
        error_log("🔐 LOGIN ATTEMPT - Email: " . $email);
        
        if(!$this->userModel->emailExists()) {
            error_log("❌ LOGIN FAILED - Email not found: " . $email);
            return false;
        }
        
        error_log("✅ Email found - User ID: " . $this->userModel->id . ", Username: " . $this->userModel->username);
        
        if(!password_verify($password, $this->userModel->password)) {
            error_log("❌ LOGIN FAILED - Password incorrect for: " . $email);
            return false;
        }
        
        error_log("✅ Password verified");
        
        // Check if user is banned and if ban has expired
        if($this->userModel->status === 'banned') {
            // Vérifier si la colonne banned_until existe
            try {
                // Créer une nouvelle connexion à la base de données
                require_once __DIR__ . '/../config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                $checkBannedUntil = "SHOW COLUMNS FROM users LIKE 'banned_until'";
                $checkStmt = $db->prepare($checkBannedUntil);
                $checkStmt->execute();
                $hasBannedUntil = $checkStmt->rowCount() > 0;
                
                if ($hasBannedUntil) {
                    $bannedUntilQuery = "SELECT banned_until FROM users WHERE id = ?";
                    $bannedUntilStmt = $db->prepare($bannedUntilQuery);
                    $bannedUntilStmt->execute([$this->userModel->id]);
                    $bannedUntilRow = $bannedUntilStmt->fetch(PDO::FETCH_ASSOC);
                    $bannedUntil = $bannedUntilRow['banned_until'] ?? null;
                    
                    // Si banned_until est null, c'est un bannissement permanent
                    if ($bannedUntil === null) {
                        throw new Exception("Votre compte a été banni de manière permanente. Contactez un administrateur pour plus d'informations.");
                    }
                    
                    // Si banned_until est dans le passé, le bannissement a expiré
                    if (strtotime($bannedUntil) < time()) {
                        // Débannir automatiquement l'utilisateur
                        $unbanQuery = "UPDATE users SET status = 'active', banned_until = NULL WHERE id = ?";
                        $unbanStmt = $db->prepare($unbanQuery);
                        $unbanStmt->execute([$this->userModel->id]);
                        error_log("✅ User ban expired, automatically unbanned: " . $this->userModel->id);
                    } else {
                        // Le bannissement est toujours actif
                        $banDate = date('d/m/Y à H:i', strtotime($bannedUntil));
                        throw new Exception("Votre compte est banni jusqu'au " . $banDate . ". Contactez un administrateur pour plus d'informations.");
                    }
                } else {
                    // Pas de colonne banned_until, bannissement permanent (ancien système)
                    throw new Exception("Votre compte a été banni. Contactez un administrateur pour plus d'informations.");
                }
            } catch (Exception $e) {
                // Si c'est déjà une Exception avec un message, la relancer
                if (strpos($e->getMessage(), 'banni') !== false) {
                    throw $e;
                }
                // Sinon, c'est une erreur PDO
                error_log("Erreur lors de la vérification du bannissement: " . $e->getMessage());
                throw new Exception("Votre compte a été banni. Contactez un administrateur pour plus d'informations.");
            }
        }
        
        // Check account status
        if($this->userModel->status === 'active') {
            // Admin accounts can login without email verification
            // Regular users need email verification
            if($this->userModel->role !== 'admin' && !$this->userModel->isEmailVerified($this->userModel->id)) {
                $_SESSION['pending_verification_user_id'] = $this->userModel->id;
                $_SESSION['pending_verification_email'] = $this->userModel->email;
                throw new Exception("Votre email n'a pas été vérifié. Veuillez vérifier votre boîte de réception ou demander un nouveau code.");
            }
            
            // Re-read user to ensure all fields including avatar are loaded
            $this->userModel->id = $this->userModel->id;
            if (!$this->userModel->readOne()) {
                throw new Exception("Erreur lors de la récupération des données utilisateur");
            }
            
            $_SESSION['user_id'] = $this->userModel->id;
            $_SESSION['username'] = $this->userModel->username;
            $_SESSION['role'] = $this->userModel->role;
            $_SESSION['email'] = $this->userModel->email;
            $_SESSION['avatar'] = $this->userModel->avatar ?? null;
            
            error_log("🎯 LOGIN FINAL - User: " . $this->userModel->id . 
                     ", Username: " . $this->userModel->username . 
                     ", Avatar: " . ($this->userModel->avatar ?? 'NULL'));
            
            $profileCompleted = $this->checkProfileCompleted($this->userModel->id);
            $_SESSION['profile_completed'] = $profileCompleted;
            
            error_log("🎯 LOGIN FINAL - Profile Completed: " . ($profileCompleted ? 'YES' : 'NO'));
            
            return true;
        } else {
            throw new Exception("Votre compte est désactivé");
        }
    }

    // Vérification du profil complété
    private function checkProfileCompleted($user_id) {
        try {
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../models/Profile.php';
            
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT first_name, last_name FROM profiles WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $user_id);
            
            if (!$stmt->execute()) {
                error_log("❌ Erreur exécution requête profil pour user: " . $user_id);
                return false;
            }
            
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$profile) {
                error_log("❌ Aucun profil trouvé pour user: " . $user_id);
                return false;
            }
            
            $hasFirstName = !empty(trim($profile['first_name'] ?? ''));
            $hasLastName = !empty(trim($profile['last_name'] ?? ''));
            $isCompleted = $hasFirstName && $hasLastName;
            
            error_log("🔍 Vérification profil - User: " . $user_id . 
                     ", Prénom: '" . ($profile['first_name'] ?? 'NULL') . 
                     "', Nom: '" . ($profile['last_name'] ?? 'NULL') . 
                     "', Completed: " . ($isCompleted ? 'YES' : 'NO'));
            
            return $isCompleted;
            
        } catch (Exception $e) {
            error_log("💥 ERREUR CRITIQUE vérification profil - User: " . $user_id . " - " . $e->getMessage());
            return false;
        }
    }

    // Déconnexion
    public function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Delete the session cookie if it exists
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Start a new session to avoid warnings
        session_start();
        session_regenerate_id(true);
        
        return true;
    }

    // Vérifier si l'utilisateur est connecté
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Vérifier si l'utilisateur est admin
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Rediriger si non connecté
    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            header('Location: /game-masters/public/index.php?action=login');
            exit;
        }
    }

    // Rediriger si non admin
    public static function requireAdmin() {
        self::requireLogin();
        if(!self::isAdmin()) {
            header('Location: /game-masters/public/index.php');
            exit;
        }
    }

    // Méthode utilitaire pour vérifier l'état du profil
    public static function checkProfileStatus($user_id) {
        try {
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../models/Profile.php';
            
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT first_name, last_name FROM profiles WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profile) {
                return [
                    'exists' => true,
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'completed' => !empty(trim($profile['first_name'])) && !empty(trim($profile['last_name']))
                ];
            }
            
            return ['exists' => false, 'completed' => false];
            
        } catch (Exception $e) {
            error_log("Erreur checkProfileStatus: " . $e->getMessage());
            return ['exists' => false, 'completed' => false, 'error' => $e->getMessage()];
        }
    }

    // Générer un token CSRF
    public static function generateCsrfToken() {
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
}
?>
