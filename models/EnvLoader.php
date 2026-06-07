<?php
/**
 * Chargeur de variables d'environnement depuis un fichier .env
 * Permet de sécuriser les informations sensibles (mots de passe, clés API, etc.)
 */
class EnvLoader {
    /**
     * Charge les variables d'environnement depuis un fichier .env
     * @param string $path Chemin vers le fichier .env
     * @return bool True si le chargement a réussi, False sinon
     */
    public static function load($path) {
        if (!file_exists($path)) {
            error_log("Fichier .env non trouvé : {$path}");
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            error_log("Erreur lors de la lecture du fichier .env");
            return false;
        }

        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Vérifier que la ligne contient un =
            if (strpos($line, '=') === false) {
                continue;
            }

            // Parser la ligne (nom=valeur)
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Supprimer les guillemets doubles ou simples
            $value = trim($value, '"\'');

            // Définir la variable d'environnement
            if (!array_key_exists($name, $_ENV)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }

        return true;
    }
}
?>
