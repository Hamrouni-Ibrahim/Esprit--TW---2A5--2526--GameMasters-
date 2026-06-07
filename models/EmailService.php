<?php
class EmailService {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;

    public function __construct() {
        // Configuration par défaut (à adapter selon votre serveur)
        $this->smtp_host = 'smtp.gmail.com'; // Ou votre serveur SMTP
        $this->smtp_port = 587;
        $this->smtp_username = 'your-email@gmail.com'; // À configurer
        $this->smtp_password = 'your-app-password'; // À configurer
        $this->from_email = 'noreply@gamemasters.com';
        $this->from_name = 'Game Masters';
    }

    /**
     * Envoyer un email de vérification
     */
    public function sendVerificationEmail($to, $verificationCode) {
        $subject = "Vérification de votre email - Game Masters";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .code-box { background: #fff; border: 2px dashed #667eea; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
                .code { font-size: 32px; font-weight: bold; color: #667eea; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎮 Game Masters</h1>
                </div>
                <div class='content'>
                    <h2>Vérification de votre email</h2>
                    <p>Bonjour,</p>
                    <p>Merci de vous être inscrit sur Game Masters ! Pour activer votre compte, veuillez utiliser le code de vérification suivant :</p>
                    <div class='code-box'>
                        <div class='code'>{$verificationCode}</div>
                    </div>
                    <p>Ce code est valide pendant 24 heures.</p>
                    <p>Si vous n'avez pas créé de compte, veuillez ignorer cet email.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Game Masters. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($to, $subject, $message);
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe
     */
    public function sendPasswordResetEmail($to, $resetToken) {
        $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
                     "://" . $_SERVER['HTTP_HOST'] . 
                     dirname($_SERVER['PHP_SELF']) . 
                     "/index.php?action=reset_password&token=" . $resetToken;
        
        $subject = "Réinitialisation de votre mot de passe - Game Masters";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎮 Game Masters</h1>
                </div>
                <div class='content'>
                    <h2>Réinitialisation de mot de passe</h2>
                    <p>Bonjour,</p>
                    <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    <p style='text-align: center;'>
                        <a href='{$resetLink}' class='button'>Réinitialiser mon mot de passe</a>
                    </p>
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <p style='word-break: break-all; color: #667eea;'>{$resetLink}</p>
                    <p><strong>Ce lien est valide pendant 1 heure.</strong></p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Game Masters. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($to, $subject, $message);
    }

    /**
     * Envoyer un email (utilise mail() par défaut, peut être amélioré avec PHPMailer)
     */
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">" . "\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Pour le développement local, on peut logger au lieu d'envoyer
        if (getenv('APP_ENV') === 'development' || !function_exists('mail')) {
            error_log("=== EMAIL (DEV MODE) ===");
            error_log("To: {$to}");
            error_log("Subject: {$subject}");
            error_log("Message: " . strip_tags($message));
            error_log("========================");
            return true; // Simuler l'envoi réussi en développement
        }

        return mail($to, $subject, $message, $headers);
    }

    /**
     * Générer un code de vérification aléatoire
     */
    public static function generateVerificationCode($length = 6) {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Générer un token de réinitialisation
     */
    public static function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
}
?>




