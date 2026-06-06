# 🎮 Minecraft Block Generator — Bedrock Edition

**Auteurs** : Romain Lautard, Yona Auerbach, Olivier Bonneyrat

[![Authors](https://img.shields.io/badge/Auteurs-Romain%20Lautard%20%7C%20Yona%20Auerbach%20%7C%20Olivier%20Bonneyrat-blue)](https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE)
[![License](https://img.shields.io/badge/License-School%20Project-green)](https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE)
[![Status](https://img.shields.io/badge/Status-Production--Ready-brightgreen)](https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE)

Un générateur web intuitif pour créer des **blocs, items et mobs personnalisés** pour Minecraft Bedrock Edition. Transformez une simple image de texture en pack Minecraft complet, prêt à être importé.

## ✨ Caractéristiques

### 🧱 Blocs Personnalisés

- Créez des blocs avec un **formulaire simple** (nom, identifiant, propriétés)
- Définissez les **propriétés de base**: solidité, destructibilité, résistance
- **Prévisualisation 3D en temps réel** du bloc généré
- Génération automatique des **fichiers JSON** (behavior pack + resource pack)
- Téléchargement en **archive .zip prête à importer**

### 🎁 Items Personnalisés

- Créez des items avec propriétés avancées:
  - **Taille max de stack** (jusqu'à 64)
  - **Durabilité personnalisée**
  - **Tier d'outil** et **multiplicateur de vitesse**
  - **Dégâts** (attaque)
  - **Équipable en main**

### 🐾 Mobs Personnalisés

- Créez des créatures avec:
  - **Santé** et **vitesse** personnalisées
  - **Types de comportement** (passif, neutre, hostile)
  - **Dégâts d'attaque**
  - **Spawn egg** avec couleurs personnalisées
  - **Modèles** (humanoid, quadrupède, creeper)
  - Propriétés avancées (spawnable, summonable, collision, scale)

### 👥 Gestion Utilisateur

- **Authentification complète** avec dashboard personnel
- **Sauvegarde des créations** avec historique
- **Édition** et **suppression** de vos créations
- **Filtrage** : voir tous les créations ou vos créations personnelles
- **Rôles utilisateur**: User et Admin
- **Admin panel** : gestion des utilisateurs et modification des identifiants

### 🎨 Interface & UX

- **Interface responsive** (desktop et mobile)
- **Onglets intégrés** pour blocs, items et mobs
- **Validation en temps réel** des entrées
- **Prévisualisation des textures** avant génération
- **Notifications** avec modales de confirmation
- **Design moderne** avec Tailwind CSS

## 🚀 Installation & Setup

### Prérequis

- **Docker** et **Docker Compose**
- **PHP 8.2+** (si installation locale)
- **Laravel 12.56.0+** (si installation locale)
- **Composer** (si installation locale)
- **SQLite** (base de données incluse)
- **Node.js** (pour Vite, si installation locale)

### Installation avec Docker (Recommandé)

```bash
# 1. Cloner le projet
git clone https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE.git
cd Mise-En-Situation-Professionnelle_ECE/minecraft-block-generator

# 2. Construire et démarrer les conteneurs Docker
docker-compose up -d

# 3. Installer les dépendances PHP
docker-compose exec app composer install

# 4. Copier le fichier .env
docker-compose exec app cp .env.example .env

# 5. Générer la clé d'application
docker-compose exec app php artisan key:generate

# 6. Migrer la base de données
docker-compose exec app php artisan migrate

# 7. Installer les dépendances Node
docker-compose exec app npm install

# 8. Lancer le serveur de développement
docker-compose exec app composer run dev
```

L'application sera disponible sur **[http://localhost:8000](http://localhost:8000)**

### Installation Locale

```bash
# 1. Cloner le projet
git clone https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE.git
cd Mise-En-Situation-Professionnelle_ECE/minecraft-block-generator

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier .env
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Migrer la base de données
php artisan migrate

# 6. Installer les dépendances Node
npm install

# 7. Lancer le serveur de développement
composer run dev
```

L'application sera disponible sur **[http://localhost:8000](http://localhost:8000)**

## 📋 Commandes Disponibles

```bash
# Démarrer le serveur de développement (serve + queue + pail + vite)
composer run dev

# Lancer les tests
composer run test

# Configuration initiale
composer run setup
```

## 📦 Architecture

### Backend (Laravel 12)

- **Controllers**: BlockController, MobController, ItemController, UserController
- **Models**: Block, Mob, Item, User
- **Services**:
  - `BlockJsonService`, `BlockZipService` — Génération des packs blocs
  - `MobJsonService`, `MobZipService` — Génération des packs mobs
  - `ItemJsonService`, `ItemZipService` — Génération des packs items
- **Migrations**: Gestion complète de la base de données
- **Validation**: FormRequest pour chaque entité

### Frontend (Blade + Tailwind)

- **Prévisualisation 3D**: Three.js pour le cube du bloc
- **Upload d'images**: Drag-and-drop avec aperçu en temps réel
- **Validation dynamique**: JavaScript client-side + serveur
- **Responsive design**: Mobile-first avec Tailwind CSS

### Base de Données (SQLite)

```text
blocks (id, name, identifier, solid, destructible, resistance, texture_path, ...)
mobs (id, name, identifier, health, speed, behavior_type, texture_path, ...)
items (id, name, identifier, max_stack_size, max_durability, damage, texture_path, ...)
users (id, name, email, identifier, password, role, ...)
```

## 🎯 Routes Principales

| Méthode | Route | Description |
| --- | --- | --- |
| GET | `/` | Page d'accueil - formulaire de création |
| POST | `/block/create` | Créer un bloc et télécharger le pack |
| POST | `/mob/create` | Créer un mob et télécharger le pack |
| POST | `/item/create` | Créer un item et télécharger le pack |
| GET | `/blocks` | Historique des blocs/mobs/items |
| GET | `/block/{id}/edit` | Éditer un bloc existant |
| POST | `/block/{id}/update` | Mettre à jour un bloc |
| DELETE | `/block/{id}` | Supprimer un bloc |
| GET | `/admin/users` | Panel admin - gestion des utilisateurs |

## 📝 Cahier des Charges (CDC)

📄 [Voir le cahier des charges complet (PDF)](../cdc-projet-generateur-blocs-minecraft.pdf)

Le projet est **100% conforme** au cahier des charges minimum:

- ✅ Formulaire de création avec validation
- ✅ Upload de textures (PNG, dimensions carrées)
- ✅ Prévisualisation en temps réel
- ✅ Génération JSON (behavior pack + resource pack)
- ✅ Génération d'archive .zip
- ✅ Téléchargement direct
- ✅ Interface responsive

**Bonus implémentés:**

- ✅ Authentification et gestion utilisateurs
- ✅ Sauvegarde et historique des créations
- ✅ Édition des créations existantes
- ✅ Support complet des items (hors périmètre CDC)
- ✅ Support complet des mobs (hors périmètre CDC)
- ✅ Admin panel avec gestion des identifiants

## 💾 Stockage des Fichiers

Les textures sont stockées dans `storage/app/`:

```text
storage/app/
├── textures/          # Textures des blocs
├── mob_textures/      # Textures des mobs
├── item_textures/     # Textures des items
└── geometry/          # Géométrie personnalisée des mobs
```

**Note**: Les fichiers `storage/app/` sont ignorés par git par défaut. Utilisez `.gitignore` pour autoriser les textures:

```text
!textures/
!mob_textures/
!item_textures/
!geometry/
```

## 🔐 Sécurité

- **Validation MIME**: Vérification du type PNG
- **Limite de taille**: Max 512 Ko par texture
- **Validation des dimensions**: Dimensions carrées uniquement (16×16, 32×32, 64×64, 128×128)
- **CSRF Protection**: Tokens CSRF sur tous les formulaires
- **Authentification**: Système de roles (User/Admin)
- **Authorization**: Vérification des droits avant édition/suppression

## 📱 Navigateurs Supportés

- Chrome (dernière version)
- Firefox (dernière version)
- Edge (dernière version)
- Safari (dernière version)
- Mobile responsive (iOS Safari, Chrome Mobile)

## 🛠️ Technologie

- **Framework Backend**: Laravel 12 (PHP 8.2)
- **Framework Frontend**: Blade Templates
- **CSS**: Tailwind CSS v3
- **JavaScript**: Vanilla JS + Three.js (prévisualisation 3D)
- **Base de Données**: SQLite
- **Build Tool**: Vite
- **Compression**: ZipArchive (PHP natif)

## 📄 Structure des Packs Générés

```text
generated_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── blocks/ (ou entities/ pour mobs, items/)
│       └── custom_block.json
└── resource_pack/
    ├── manifest.json
    ├── textures/
    │   ├── blocks/
    │   │   └── custom_block.png
    │   └── entity/
    │       └── custom_mob.png
    └── terrain_texture.json
```

## 🐛 Dépannage

### Le bloc n'apparaît pas dans Minecraft

- Vérifiez que l'**identifiant technique** ne contient que des minuscules et underscores
- Assurez-vous que la texture est au **bon format** (PNG carré)
- Vérifiez la **version de Minecraft Bedrock** (1.21.80+)

### La texture ne s'affiche pas

- Utilisez une image **PNG native** (pas de format appliqué)
- Respectez les **dimensions carrées** (16×16, 32×32, etc.)
- Vérifiez que le fichier ne dépasse pas **512 Ko**

### Erreur lors du téléchargement

- Vérifiez que **tous les champs** sont remplis
- Assurez-vous que l'**identifiant est unique**
- Vérifiez l'espace **disque disponible**

## 📞 Support & Contribution

Pour des questions ou des bugs :

- 💬 Discord: **FuZIon3RedX**
- 🐛 GitHub: [Dépôt du projet](https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE)
- 📋 Issues: [Signaler un bug](https://github.com/FuZionRedX/Mise-En-Situation-Professionnelle_ECE/issues)

## 📄 Licence

Ce projet est un projet scolaire (ECE) créé dans le cadre d'une "Mise En Situation Professionnelle".

---

**Version**: 1.0.0  
**Dernière mise à jour**: Juin 2026  
**Statut**: ✅ Production-Ready
