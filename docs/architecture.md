# Architecture — Game Masters

## Vue d'ensemble

Application web **PHP natif** en architecture **MVC** (sans framework).

```
Navigateur (HTML/CSS/JS)
        │
        ▼
   index.php (routeur)
        │
   ┌────┴────┐
   ▼         ▼
Controllers  Models
   │         │
   └────┬────┘
        ▼
   views/ (front + admin)
        │
        ▼
   MySQL (PDO)
```

## Dossiers principaux

| Dossier | Rôle |
|---------|------|
| `index.php` | Point d'entrée, routage par `?action=` |
| `controllers/` | Logique métier (Auth, Game, Event, Project…) |
| `models/` | Accès BDD, entités PHP |
| `views/front/` | Interface utilisateur |
| `views/admin/` | Back-office administrateur |
| `config/` | Base de données, reCAPTCHA |
| `public/` | CSS, JS, images, uploads |
| `database/` | Scripts SQL (`schema.sql`, migrations) |
| `vendor/` | PHPMailer, TCPDF (Composer) |

## Sécurité

- Sessions PHP pour l'authentification
- Mots de passe hashés (`password_hash` / bcrypt)
- Requêtes préparées PDO
- Tokens CSRF sur les formulaires
- reCAPTCHA à l'inscription (optionnel)
- Variables sensibles dans `.env` (non versionné)
