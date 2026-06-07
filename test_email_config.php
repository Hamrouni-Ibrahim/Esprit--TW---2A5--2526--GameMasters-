<?php
// Script de test pour la configuration Email/SMTP
// Placez ce fichier à la racine de votre projet et lancez-le depuis le navigateur ou en ligne de commande

// Afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Configuration Email</h1>";

// 1. Vérification des fichiers
echo "<h2>1. Vérification des fichiers</h2>";

$paths = [
    'Composer Autoload' => __DIR__ . '/vendor/autoload.php',
    '.env File' => __DIR__ . '/.env',
    'EmailLoader' => __DIR__ . '/models/EnvLoader.php',
    'EmailHelper' => __DIR__ . '/models/EmailHelper.php'
];

foreach ($paths as $name => $path) {
    if (file_exists($path)) {
        echo "<div style='color:green'>✅ $name trouvé: $path</div>";
    } else {
        echo "<div style='color:red'>❌ $name NON trouvé: $path</div>";
    }
}

// 2. Chargement des dépendances
echo "<h2>2. Chargement des dépendances</h2>";

if (file_exists($paths['Composer Autoload'])) {
    require_once $paths['Composer Autoload'];
    echo "<div style='color:green'>✅ Autoloader chargé</div>";
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<div style='color:green'>✅ Classe PHPMailer disponible</div>";
    } else {
        echo "<div style='color:red'>❌ Classe PHPMailer NON disponible (Vérifiez 'composer install')</div>";
    }
}

// 3. Vérification de la configuration SMTP (.env)
echo "<h2>3. Configuration SMTP (.env)</h2>";

require_once $paths['EmailLoader'];
EnvLoader::load($paths['.env File']);

$smtp_host = getenv('SMTP_HOST');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');

// Masquer le mot de passe pour l'affichage
$masked_pass = substr($smtp_pass, 0, 3) . '****';

echo "<ul>";
echo "<li>SMTP_HOST: " . ($smtp_host ? $smtp_host : "<span style='color:red'>NON DÉFINI</span>") . "</li>";
echo "<li>SMTP_USER: " . ($smtp_user ? $smtp_user : "<span style='color:red'>NON DÉFINI</span>") . "</li>";
echo "<li>SMTP_PASS: " . ($smtp_pass ? $masked_pass : "<span style='color:red'>NON DÉFINI</span>") . "</li>";
echo "</ul>";

if ($smtp_user === 'votre-email@gmail.com') {
    echo "<div style='color:orange; font-weight:bold; padding: 10px; border: 2px solid orange;'>
        ⚠️ ATTENTION: Vous utilisez encore les identifiants par défaut !<br>
        L'application restera en 'Mode Développement' et affichera le code à l'écran au lieu d'envoyer un email.<br>
        Veuillez modifier le fichier .env avec vos vrais identifiants.
    </div>";
} elseif (!$smtp_host || !$smtp_user || !$smtp_pass) {
    echo "<div style='color:red'>❌ Configuration incomplète. EmailHelper ne tentera même pas d'envoyer l'email.</div>";
} else {
    echo "<div style='color:green'>✅ La configuration semble correcte (structurellement).</div>";
}

// 4. Test d'envoi réel
echo "<h2>4. Tentative d'envoi (via EmailHelper)</h2>";

require_once $paths['EmailHelper'];
$emailHelper = new EmailHelper();

// Force check of config
if (!$emailHelper->isSmtpConfigured()) {
    echo "<div style='color:red'>❌ EmailHelper détecte que la configuration est INVALIDE ou PAR DÉFAUT.</div>";
    echo "Il se comportera comme s'il était en mode développement (affichage du code seulement).";
} else {
    echo "<div style='color:green'>✅ EmailHelper valide la configuration. Tentative d'envoi...</div>";
    
    // Le destinataire sera l'utilisateur SMTP lui-même pour le test
    $to = $smtp_user; 
    $subject = "Test de configuration Game Masters";
    $code = "123456";
    $result = $emailHelper->sendVerificationEmail($to, "Test User", $code);
    
    if ($result) {
        echo "<div style='color:green; padding:10px; border:1px solid green; background:#eaffea;'>
            ✅ SUCCÈS : L'email a été envoyé à $to !<br>
            Vérifiez votre boîte de réception (et vos spams).
        </div>";
    } else {
        echo "<div style='color:red; padding:10px; border:1px solid red; background:#ffeaea;'>
            ❌ ÉCHEC : L'envoi a échoué. Vérifiez email_log.txt ou les logs PHP.<br>
            Causes possibles : Mot de passe d'application incorrect, blocage pare-feu, port incorrect.
        </div>";
    }
}
