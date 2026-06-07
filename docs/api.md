# API & routes — Game Masters

Le routage est centralisé dans `index.php` via le paramètre `?action=`.

## Authentification

| Action | Méthode | Description |
|--------|---------|-------------|
| `login` | POST | Connexion email / mot de passe |
| `register` | POST | Inscription + vérification email |
| `forgot_password` | POST | Demande de reset |
| `reset_password` | POST | Réinitialisation mot de passe |
| `face_login` | GET/POST | Connexion par reconnaissance faciale |
| `save_face` | POST | Enregistrement descripteur facial |

## Jeux & contenu

| Action | Description |
|--------|-------------|
| `games` | Catalogue public |
| `game_details` | Détail d'un jeu |
| `add_game` | Soumission (authentifié) |
| `rate_game` | Notation 1–5 étoiles |
| `projects` | Liste des projets |
| `events` | Liste des événements |

## Chatbots (JSON)

Les chatbots répondent en JSON (`Content-Type: application/json`) :

- `GameChatbotController` — assistant jeux
- `ProjectChatbotController` — assistant projets
- `EventChatbotController` — assistant événements
- `UnifiedChatbotController` — assistant global (G-Bot)

**Requête :**
```json
POST { "message": "Quels jeux sur l'écologie ?" }
```

**Réponse :**
```json
{ "success": true, "response": "..." }
```

## Administration

Routes préfixées `admin_*` : dashboard, gestion utilisateurs, modération jeux, export PDF.
