<?php
class EmailHelper {
    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_pass;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Charger les variables d'environnement
        require_once __DIR__ . '/EnvLoader.php';
        $envPath = __DIR__ . '/../.env';
        if(file_exists($envPath)) {
            EnvLoader::load($envPath);
        }
        
        // Configuration SMTP depuis .env
        $this->smtp_host = getenv('SMTP_HOST') ?: '';
        $this->smtp_port = (int)(getenv('SMTP_PORT') ?: 587);
        $this->smtp_user = getenv('SMTP_USER') ?: '';
        $this->smtp_pass = getenv('SMTP_PASS') ?: '';
        $this->from_email = getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER') ?: 'noreply@gamemasters.com';
        $this->from_name = getenv('SMTP_FROM_NAME') ?: 'Game Masters';
    }
    
    // Vérifier si SMTP est configuré pour l'envoi réel
    public function isSmtpConfigured() {
        // Vérifier que les valeurs essentielles sont présentes et ne sont pas les valeurs par défaut/exemple
        $hasValidUser = !empty($this->smtp_user) && 
                       $this->smtp_user !== 'votre-email@gmail.com' &&
                       filter_var($this->smtp_user, FILTER_VALIDATE_EMAIL);
        
        $hasValidPass = !empty($this->smtp_pass) && 
                       $this->smtp_pass !== 'votre-mot-de-passe' &&
                       $this->smtp_pass !== 'votre-mot-de-passe-application' &&
                       strlen($this->smtp_pass) > 5; // Au moins 6 caractères pour être valide
        
        $hasValidHost = !empty($this->smtp_host) && 
                       (filter_var($this->smtp_host, FILTER_VALIDATE_DOMAIN) || 
                        filter_var($this->smtp_host, FILTER_VALIDATE_IP));
        
        return $hasValidUser && $hasValidPass && $hasValidHost;
    }
    
    // Envoyer un email avec PHPMailer ou mail() natif
    public function sendEmail($to, $subject, $body, $isHTML = true) {
        // Charger PHPMailer si disponible
        $autoloadPath = __DIR__ . '/../vendor/autoload.php';
        if(file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }
        
        // Méthode 1: Utiliser PHPMailer si disponible et SMTP configuré (recommandé)
        if(class_exists('PHPMailer\PHPMailer\PHPMailer') && $this->isSmtpConfigured()) {
            $result = $this->sendWithPHPMailer($to, $subject, $body, $isHTML);
            // Si PHPMailer réussit, retourner vrai (email réellement envoyé)
            if ($result) {
                return true;
            }
            // Si PHPMailer échoue mais SMTP est configuré, loguer l'erreur et retourner false
            error_log("⚠️ Échec envoi email via PHPMailer pour: $to - Vérifiez vos credentials SMTP");
        }
        
        // Méthode 2: Utiliser mail() natif (fallback uniquement si PHPMailer n'est pas disponible ou non configuré)
        // Ne pas utiliser en développement si SMTP n'est pas configuré (juste logger)
        if (!$this->isSmtpConfigured()) {
            error_log("⚠️ SMTP non configuré - Email non envoyé à: $to");
            // En mode développement sans SMTP, logger dans un fichier
            $this->logEmailForDevelopment($to, $subject, $body);
            return false; // Retourner false car email non envoyé réellement
        }
        
        return $this->sendWithMail($to, $subject, $body, $isHTML);
    }
    
    // Logger l'email en développement (si SMTP non configuré)
    private function logEmailForDevelopment($to, $subject, $body) {
        // Extract verification code from body if it's a verification email
        preg_match('/<div class=[\'"]code[\'"]>(\d+)<\/div>/', $body, $matches);
        $code = isset($matches[1]) ? $matches[1] : 'N/A';
        
        error_log("=== 📧 EMAIL (SMTP NON CONFIGURÉ - NON ENVOYÉ) ===");
        error_log("To: {$to}");
        error_log("Subject: {$subject}");
        error_log("Verification Code: {$code}");
        error_log("Message Preview: " . substr(strip_tags($body), 0, 200) . "...");
        error_log("=====================================");
        
        // Also log to a file for easy access
        $logFile = __DIR__ . '/../email_log.txt';
        $logEntry = date('Y-m-d H:i:s') . " - To: {$to} - Code: {$code} - Subject: {$subject}\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
    
    // Envoyer avec PHPMailer (envoi réel via SMTP)
    private function sendWithPHPMailer($to, $subject, $body, $isHTML) {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_user;
            $mail->Password = $this->smtp_pass;
            
            // Choisir automatiquement STARTTLS (port 587) ou SSL (port 465)
            if ($this->smtp_port == 465) {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port = $this->smtp_port;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 30; // Timeout de 30 secondes
            
            // Activer le debug si configuré
            $debugLevel = getenv('SMTP_DEBUG') ? (int)getenv('SMTP_DEBUG') : 0;
            if ($debugLevel > 0) {
                $mail->SMTPDebug = $debugLevel;
                $mail->Debugoutput = function($str, $level) {
                    error_log("SMTP Debug: $str");
                };
            }
            
            // Expéditeur et destinataire
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to);
            
            // Contenu
            $mail->isHTML($isHTML);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            if(!$isHTML) {
                $mail->AltBody = strip_tags($body);
            }
            
            // Envoyer l'email
            $mail->send();
            error_log("✅ Email envoyé avec succès via SMTP à: $to");
            return true;
        } catch(Exception $e) {
            error_log("❌ Erreur envoi email PHPMailer à $to: " . $e->getMessage());
            error_log("   SMTP Host: " . $this->smtp_host);
            error_log("   SMTP Port: " . $this->smtp_port);
            error_log("   SMTP User: " . $this->smtp_user);
            return false;
        }
    }
    
    // Envoyer avec mail() natif
    private function sendWithMail($to, $subject, $body, $isHTML) {
        // En développement local, logger l'email au lieu de l'envoyer
        // Check if we're in development (no SMTP configured or localhost)
        $isDevelopment = (getenv('APP_ENV') === 'development' || 
                         empty($this->smtp_user) || 
                         $this->smtp_user === 'votre-email@gmail.com' ||
                         $_SERVER['HTTP_HOST'] === 'localhost' ||
                         strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
                         strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
        
        if ($isDevelopment) {
            // Extract verification code from body if it's a verification email
            preg_match('/<div class=[\'"]code[\'"]>(\d+)<\/div>/', $body, $matches);
            $code = isset($matches[1]) ? $matches[1] : 'N/A';
            
            error_log("=== 📧 EMAIL (DEV MODE - NOT SENT) ===");
            error_log("To: {$to}");
            error_log("Subject: {$subject}");
            error_log("Verification Code: {$code}");
            error_log("Message Preview: " . substr(strip_tags($body), 0, 200) . "...");
            error_log("=====================================");
            
            // Also log to a file for easy access
            $logFile = __DIR__ . '/../email_log.txt';
            $logEntry = date('Y-m-d H:i:s') . " - To: {$to} - Code: {$code} - Subject: {$subject}\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND);
            
            return true; // Simuler l'envoi réussi en développement
        }
        
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: " . ($isHTML ? "text/html" : "text/plain") . "; charset=UTF-8";
        $headers[] = "From: " . $this->from_name . " <" . $this->from_email . ">";
        $headers[] = "Reply-To: " . $this->from_email;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        $headersString = implode("\r\n", $headers);
        
        $result = @mail($to, $subject, $body, $headersString);
        
        if (!$result) {
            $error = error_get_last();
            error_log("Erreur envoi email à {$to}: " . ($error ? $error['message'] : 'Unknown error'));
        }
        
        return $result;
    }
    
    // Envoyer un email de vérification
    public function sendVerificationEmail($to, $username, $verificationCode) {
        $subject = "Vérification de votre compte Game Masters";
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; }
                .code { background: #f0f0f0; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; margin: 20px 0; border-radius: 5px; color: #667eea; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎮 Game Masters</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour " . htmlspecialchars($username) . ",</h2>
                    <p>Merci de vous être inscrit sur Game Masters !</p>
                    <p>Pour activer votre compte, veuillez entrer le code de vérification suivant :</p>
                    <div class='code'>" . htmlspecialchars($verificationCode) . "</div>
                    <p>Ce code est valide pendant 24 heures.</p>
                    <p>Si vous n'avez pas créé de compte, ignorez cet email.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Game Masters. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($to, $subject, $body, true);
    }
    
    // Envoyer un email de réinitialisation de mot de passe
    public function sendPasswordResetEmail($to, $username, $resetCode) {
        $subject = "Réinitialisation de votre mot de passe - Game Masters";
        
        // Construire le lien avec email et code
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Déterminer le chemin de base
        $scriptPath = $_SERVER['PHP_SELF'] ?? '/index.php';
        $basePath = dirname($scriptPath);
        
        // Nettoyer le chemin (sur Windows dirname peut retourner \ et on veut /)
        $basePath = str_replace('\\', '/', $basePath);
        
        // Remove trailing slash if present
        $basePath = rtrim($basePath, '/');
        
        $resetLink = $protocol . "://" . $host . $basePath . "/index.php?action=reset_password&email=" . urlencode($to) . "&code=" . urlencode($resetCode);
        
        error_log("🔗 Lien de réinitialisation généré: {$resetLink}");
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .code { background: #f0f0f0; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 3px; margin: 20px 0; border-radius: 5px; color: #667eea; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Réinitialisation de mot de passe</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour " . htmlspecialchars($username) . ",</h2>
                    <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                    <p>Utilisez le code suivant pour réinitialiser votre mot de passe :</p>
                    <div class='code'>" . htmlspecialchars($resetCode) . "</div>
                    <p>Ou cliquez sur le lien ci-dessous :</p>
                    <p style='text-align: center;'><a href='" . htmlspecialchars($resetLink) . "' class='button'>Réinitialiser mon mot de passe</a></p>
                    <p>Ce code est valide pendant 1 heure.</p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Game Masters. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($to, $subject, $body, true);
    }
    
    // Générer un code de vérification
    public static function generateVerificationCode($length = 6) {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
    
    // Générer un code de réinitialisation
    public static function generateResetCode($length = 8) {
        return bin2hex(random_bytes($length / 2));
    }
}
?>

