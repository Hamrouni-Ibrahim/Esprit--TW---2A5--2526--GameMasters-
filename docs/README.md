# Documentation technique — Game Masters

## Contenu

| Document | Description |
|----------|-------------|
| [architecture.md](architecture.md) | Architecture MVC, structure des dossiers |
| [database.md](database.md) | Schéma relationnel et tables principales |
| [api.md](api.md) | Routes et endpoints (chatbots, auth faciale) |

## Diagrammes recommandés (à ajouter)

- MCD / MLD de la base `game_masters`
- Diagramme de cas d'utilisation (utilisateur / admin)
- Diagramme de séquence (inscription, soumission de jeu)

## Script SQL

Le script officiel d'installation est : `database/schema.sql`

```bash
mysql -u root -p game_masters < database/schema.sql
```
