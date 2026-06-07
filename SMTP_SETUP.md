# Configuration SMTP pour l'envoi d'emails

Pour envoyer des emails réels (codes de vérification, réinitialisation de mot de passe), vous devez configurer SMTP.

## Étapes de configuration

### 1. Créer un fichier `.env` à la racine du projet

Créez un fichier `.env` (sans extension) à la racine du projet avec le contenu suivant :

```env
# Configuration SMTP pour l'envoi d'emails

# Serveur SMTP (ex: smtp.gmail.com pour Gmail)
SMTP_HOST=smtp.gmail.com

# Port SMTP (587 pour TLS, 465 pour SSL)
SMTP_PORT=587

# Votre adresse email SMTP
SMTP_USER=votre-email@gmail.com

# Mot de passe de l'application (pour Gmail, utilisez un "App Password")
# Ne pas utiliser votre mot de passe principal pour Gmail !
SMTP_PASS=votre-mot-de-passe-application

# Email expéditeur (peut être différent de SMTP_USER)
SMTP_FROM_EMAIL=noreply@gamemasters.com

# Nom de l'expéditeur
SMTP_FROM_NAME=Game Masters

# Mode debug SMTP (0 = désactivé, 1-4 = niveaux de debug)
# Mettez 2 pour voir les détails de connexion SMTP en cas de problème
SMTP_DEBUG=0

# Environnement (development ou production)
APP_ENV=production
```

### 2. Configuration Gmail (si vous utilisez Gmail)

1. **Activer la validation en 2 étapes** sur votre compte Google
   - Allez sur https://myaccount.google.com/security
   - Activez "Validation en deux étapes"

2. **Créer un "App Password" (Mot de passe d'application)**
   - Allez sur https://myaccount.google.com/apppasswords
   - Sélectionnez "Autre (nom personnalisé)" et entrez "Game Masters"
   - Copiez le mot de passe généré (16 caractères)
   - Utilisez ce mot de passe dans `SMTP_PASS` (pas votre mot de passe principal !)

3. **Remplir le fichier .env**
   - `SMTP_USER` = votre adresse Gmail complète
   - `SMTP_PASS` = le mot de passe d'application généré
   - `SMTP_HOST` = smtp.gmail.com
   - `SMTP_PORT` = 587

### 3. Configuration avec d'autres fournisseurs SMTP

#### Outlook/Hotmail
```env
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_USER=votre-email@outlook.com
SMTP_PASS=votre-mot-de-passe
```

#### Yahoo
```env
SMTP_HOST=smtp.mail.yahoo.com
SMTP_PORT=587
SMTP_USER=votre-email@yahoo.com
SMTP_PASS=votre-mot-de-passe
```

#### Serveur SMTP personnalisé
Contactez votre administrateur système pour obtenir les paramètres SMTP de votre serveur.

### 4. Vérifier la configuration

Une fois le fichier `.env` créé et configuré :

1. Le système détectera automatiquement la configuration SMTP
2. Les emails seront envoyés **réellement** via PHPMailer
3. Si SMTP n'est pas configuré, les emails seront loggés dans `email_log.txt` mais pas envoyés

### 5. Dépannage

#### Les emails ne sont pas envoyés

1. **Vérifier les logs** : Regardez les logs PHP (error_log) pour voir les erreurs SMTP
2. **Activer le debug** : Mettez `SMTP_DEBUG=2` dans `.env` pour voir les détails de connexion
3. **Vérifier les credentials** : Assurez-vous que `SMTP_USER` et `SMTP_PASS` sont corrects
4. **Vérifier le firewall** : Assurez-vous que le port 587 (ou 465) n'est pas bloqué

#### Erreur "SMTP connect() failed"

- Vérifiez que le serveur SMTP est accessible
- Vérifiez le port (587 pour TLS, 465 pour SSL)
- Pour Gmail, assurez-vous d'utiliser un "App Password" et non votre mot de passe principal

#### Erreur "Username and Password not accepted"

- Pour Gmail : Vérifiez que vous utilisez un "App Password" et non votre mot de passe principal
- Vérifiez que l'adresse email dans `SMTP_USER` est correcte
- Assurez-vous que la validation en 2 étapes est activée (pour Gmail)

### 6. Sécurité

⚠️ **IMPORTANT** :
- Le fichier `.env` contient des informations sensibles
- **NE COMMITTEZ JAMAIS** le fichier `.env` dans Git
- Ajoutez `.env` à votre `.gitignore`
- Partagez les credentials uniquement avec les personnes autorisées

## Fichiers concernés

- `models/EmailHelper.php` : Gère l'envoi d'emails via PHPMailer
- `models/EnvLoader.php` : Charge les variables d'environnement depuis `.env`
- `controllers/AuthController.php` : Utilise EmailHelper pour envoyer les codes de vérification

## Note

Si SMTP n'est pas configuré, le système fonctionnera toujours mais :
- Les emails ne seront **pas envoyés réellement**
- Les codes seront loggés dans `email_log.txt` pour le développement
- Un message d'avertissement s'affichera à l'utilisateur

