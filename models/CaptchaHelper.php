<?php
/**
 * Helper pour la validation Google reCAPTCHA
 */
class CaptchaHelper
{
    /**
     * Vérifie la réponse reCAPTCHA avec l'API Google
     * 
     * @param string $response La réponse du CAPTCHA (g-recaptcha-response)
     * @param string $remoteIP L'adresse IP de l'utilisateur (optionnel)
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function verify($response, $remoteIP = null)
    {
        // Si le CAPTCHA est désactivé, on retourne succès
        if (!defined('RECAPTCHA_ENABLED') || !RECAPTCHA_ENABLED) {
            return ['success' => true, 'error' => null];
        }

        // Vérifier que la réponse n'est pas vide
        if (empty($response)) {
            return [
                'success' => false,
                'error' => 'Veuillez cocher la case "Je ne suis pas un robot"'
            ];
        }

        // Vérifier que la clé secrète est définie
        if (!defined('RECAPTCHA_SECRET_KEY') || empty(RECAPTCHA_SECRET_KEY)) {
            error_log("CaptchaHelper: RECAPTCHA_SECRET_KEY not defined");
            return [
                'success' => false,
                'error' => 'Configuration CAPTCHA manquante'
            ];
        }

        // Préparer les données pour l'API Google
        $data = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $response
        ];

        if ($remoteIP) {
            $data['remoteip'] = $remoteIP;
        }

        // Appeler l'API de vérification Google
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';

        try {
            $result = @file_get_contents($verifyURL, false, $context);

            if ($result === false) {
                error_log("CaptchaHelper: file_get_contents failed");
                return [
                    'success' => false,
                    'error' => 'Erreur de connexion au service CAPTCHA. Veuillez réessayer.'
                ];
            }

            $resultJson = json_decode($result, true);

            // DEBUG: Loguer la réponse de Google
            error_log("CaptchaHelper Response: " . print_r($resultJson, true));

            if ($resultJson['success']) {
                return ['success' => true, 'error' => null];
            } else {
                // Erreurs possibles de reCAPTCHA
                $errorCodes = $resultJson['error-codes'] ?? [];

                if (in_array('timeout-or-duplicate', $errorCodes)) {
                    $errorMsg = 'Le CAPTCHA a expiré. Veuillez rafraîchir la page et réessayer.';
                } else {
                    $errorMsg = 'Validation CAPTCHA échouée. Codes: ' . implode(', ', $errorCodes);
                }

                return [
                    'success' => false,
                    'error' => $errorMsg
                ];
            }
        } catch (Exception $e) {
            error_log("CaptchaHelper Exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur lors de la vérification CAPTCHA. Veuillez réessayer.'
            ];
        }
    }
}
?>




