# Base de données — Game Masters

**SGBD :** MySQL / MariaDB  
**Nom de la base :** `game_masters`  
**Script d'installation :** `database/schema.sql`

## Tables principales

| Table | Description |
|-------|-------------|
| `users` | Comptes utilisateurs (rôles, statut, auth faciale) |
| `games` | Jeux soumis par la communauté |
| `categories` | Catégories de jeux |
| `games_library` | Bibliothèque de jeux éducatifs |
| `projects` | Projets à impact social |
| `events` | Événements communautaires |
| `donations` | Dons / contributions |
| `reclamations` | Réclamations utilisateurs |
| `formations` / `educations` | Contenus éducatifs |
| `test_*` | Système de tests et médailles |

## Import

```bash
mysql -u root -p -e "CREATE DATABASE game_masters CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p game_masters < database/schema.sql
```

## Migrations additionnelles

Des scripts d'évolution sont disponibles dans `database/migrations/` pour les mises à jour incrémentales.
