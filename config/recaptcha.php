<?php
/**
 * Configuration reCAPTCHA
 *
 * Les clés sont lues depuis le fichier .env (jamais commité).
 * Copiez .env.example vers .env et renseignez vos clés :
 * https://www.google.com/recaptcha/admin
 */

require_once __DIR__ . '/../models/EnvLoader.php';

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    EnvLoader::load($envPath);
}

if (!defined('RECAPTCHA_ENABLED')) {
    $enabled = strtolower(trim(getenv('RECAPTCHA_ENABLED') ?: 'false'));
    define('RECAPTCHA_ENABLED', in_array($enabled, ['1', 'true', 'yes', 'on'], true));
}

if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', trim(getenv('RECAPTCHA_SITE_KEY') ?: ''));
}

if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', trim(getenv('RECAPTCHA_SECRET_KEY') ?: ''));
}
