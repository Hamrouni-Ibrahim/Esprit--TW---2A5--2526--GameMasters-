# Game Masters

## Description

**Game Masters** est une plateforme web dédiée aux jeux vidéo à impact social. Elle permet aux utilisateurs de découvrir, soumettre et noter des jeux qui traitent de sujets comme l'écologie, l'éducation, l'inclusion et la santé mentale. La plateforme propose un espace communautaire avec profils, événements, projets, donations et une bibliothèque de jeux éducatifs. Un back-office administrateur assure la modération, les statistiques et l'export PDF. Des fonctionnalités avancées incluent l'authentification faciale (WebRTC + face-api.js), un chatbot d'assistance (G-Bot) et l'envoi d'emails transactionnels.

## Technologies utilisées

**Frontend :** HTML5, CSS3, JavaScript (ES6+, vanilla), WebRTC, face-api.js, Google reCAPTCHA

**Backend :** PHP 8+ (natif, architecture MVC, sans framework), Composer

**Base de données :** MySQL / MariaDB (via phpMyAdmin / XAMPP, accès PDO)

**Bibliothèques :** PHPMailer, TCPDF

## Prérequis

- XAMPP / WAMP / Laragon (PHP 8+ + MySQL + Apache)
- Composer 2.x
- Navigateur web moderne (Chrome, Firefox, Edge)
- Extensions PHP : `pdo_mysql`, `openssl`, `gd`, `mbstring`

## Installation

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Créer la base de données
mysql -u root -p -e "CREATE DATABASE game_masters CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Importer le schéma et les données
mysql -u root -p game_masters < database/schema.sql

# 4. Configurer les variables d'environnement
cp .env.example .env
# Puis éditer .env avec vos identifiants SMTP et clés API
```

**Alternative phpMyAdmin :** créer la base `game_masters`, puis importer `database/schema.sql` (onglet *Importer*).

Vérifier `config/database.php` (par défaut XAMPP : utilisateur `root`, mot de passe vide).

## Lancement

### PHP natif (sans framework) — commandes ESPRIT

```bash
# Cloner le dépôt (ou copier le projet dans htdocs)
git clone https://github.com/Hamrouni-Ibrahim/Esprit--TW---2A5--2526--GameMasters-.git
cp -r Esprit--TW---2A5--2526--GameMasters- /xampp/htdocs/Esprit--TW---2A5--2526--GameMasters-

# Importer la base de données (ligne de commande)
mysql -u root -p game_masters < database/schema.sql

# Ou serveur PHP intégré (depuis la racine du projet cloné)
php -S localhost:8000
```

**Windows (équivalent XAMPP) :**

```powershell
# Cloner puis copier dans htdocs (ou cloner directement dans C:\xampp\htdocs\)
git clone https://github.com/Hamrouni-Ibrahim/Esprit--TW---2A5--2526--GameMasters-.git C:\xampp\htdocs\Esprit--TW---2A5--2526--GameMasters-

# Importer la BDD (si mysql est dans le PATH)
mysql -u root -p game_masters < database/schema.sql
```

### Vérifier que le projet se lance

1. Démarrer **Apache** et **MySQL** dans XAMPP / WAMP
2. Ouvrir le navigateur :

**Via XAMPP :**
```
http://localhost/Esprit--TW---2A5--2526--GameMasters-/
```

**Via serveur PHP intégré :**
```
http://localhost:8000
```

3. La page d'accueil Game Masters doit s'afficher sans erreur de connexion à la base de données.

## Variables d'environnement

Voir `.env.example` pour la liste complète des variables.

| Variable | Description |
|----------|-------------|
| `SMTP_*` | Configuration email (vérification, reset mot de passe) |
| `RECAPTCHA_*` | Protection anti-bot à l'inscription |
| `OPENAI_API_KEY` | Résumés IA des projets (optionnel) |
| `DB_*` | Connexion base de données (optionnel) |

Documentation détaillée SMTP : `SMTP_SETUP.md`

## Structure du dépôt (fichiers obligatoires ESPRIT)

| Fichier / Dossier | Rôle |
|-------------------|------|
| `README.md` | Présentation, installation et lancement |
| `.gitignore` | Fichiers à ne pas versionner (`.env`, uploads…) |
| `.env.example` | Modèle des variables d'environnement |
| `docs/` | Documentation technique (architecture, BDD, API) |
| `demo/` | Captures d'écran et lien vidéo de démonstration |
| `database/schema.sql` | Script SQL (schéma + données de démo) |

## Démo

**Vidéo :** https://www.youtube.com/watch?v=RyHrcSnryCQ&list=PLIZSMtKmopF_xx0q1JDwaVGVaAMngW2Xf&index=9

**Captures :** voir le dossier `demo/`

**Déploiement :** Non disponible *(application locale — XAMPP)*

## Auteurs

**Ibrahim Hamrouni** — Classe **2A5** — Année **2025/2026** — **Tuteur :** Madame Ameni Hajri

**Dépôt GitHub :** https://github.com/Hamrouni-Ibrahim/Esprit--TW---2A5--2526--GameMasters-

---

*Projet ESPRIT — Plateforme web PHP/MySQL — Game Masters*
