# Répartition du projet — 4 personnes

## Personne 1 — Frontend & UI

**Fichiers :**
- `resources/views/block/create.blade.php` — formulaire, Tailwind, responsive
- `resources/views/block/history.blade.php` — page historique

**Responsabilités :**
- Mise en page et style Tailwind CSS
- Responsive (desktop + mobile)
- Prévisualisation de la texture en temps réel (FileReader API)
- Retours de validation côté client (JS)

---

## Personne 2 — Génération JSON

**Fichiers :**
- `app/Services/BlockJsonService.php`

**Responsabilités :**
- Produire les fichiers `manifest.json`, `blocks.json`, `terrain_texture.json`
- Respecter strictement le format Minecraft Bedrock (vérifier avec le dossier `Example/`)
- Méthodes : `behaviorManifest()`, `resourceManifest()`, `blockBehavior()`, `terrainTexture()`, `encode()`

---

## Personne 3 — Assemblage ZIP & Gestion des fichiers

**Fichiers :**
- `app/Services/BlockZipService.php`

**Responsabilités :**
- Structure du ZIP (`generated_pack/behavior_pack/` + `generated_pack/resource_pack/`)
- Upload et stockage permanent de la texture (`storage/app/textures/`)
- Fichier temporaire + déclenchement du téléchargement
- Méthodes : `generate()`, `generateFromPath()`, `buildZip()`

---

## Personne 4 — Contrôleur, Validation & Base de données

**Fichiers :**
- `app/Http/Controllers/BlockController.php`
- `app/Http/Requests/BlockRequest.php`
- `app/Models/Block.php`
- `database/migrations/2026_04_05_083835_create_blocks_table.php`
- `routes/web.php`

**Responsabilités :**
- Règles de validation serveur (nom, identifiant, texture PNG carrée, résistance…)
- Routes et orchestration entre les services
- Modèle Eloquent et migration SQLite
- Actions : `index`, `create`, `history`, `download`, `destroy`
