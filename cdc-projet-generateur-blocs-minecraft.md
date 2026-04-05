# Cahier des Charges — Générateur de Blocs Minecraft

---

## 1. Contexte et problématique

Dans **Minecraft Bedrock Edition**, la création de blocs personnalisés repose sur l'écriture manuelle de fichiers JSON organisés en **behavior packs** et **resource packs**. Ce processus requiert une connaissance technique des formats de fichiers Minecraft, de la structure des packs, et de la gestion des textures — autant de prérequis qui constituent une barrière pour les joueurs, administrateurs de serveurs ou créateurs de contenu sans compétences en développement.

Ce projet s'inscrit dans une logique de **démocratisation technique** : rendre la création de contenu Minecraft accessible à des non-développeurs grâce à un outil web. La problématique centrale est la suivante :

> *Comment concevoir une application web permettant à un utilisateur non technique de créer un bloc Minecraft personnalisé, d'en définir les propriétés et la texture, puis d'obtenir automatiquement les fichiers prêts à être importés dans un serveur Minecraft Bedrock ?*

Le projet mobilise des compétences en **développement backend** (génération dynamique de fichiers JSON, gestion d'uploads, création d'archives), en **développement frontend** (interface utilisateur intuitive, prévisualisation en temps réel) et en **connaissance du format Minecraft Bedrock** (structure des packs, conventions de nommage).

---

## 2. Objectifs du projet

### 2.1 Objectif principal

Développer une application web permettant de :

- **Créer** un bloc Minecraft personnalisé via un formulaire simple (nom, identifiant, texture, propriétés).
- **Générer automatiquement** les fichiers nécessaires (behavior pack + resource pack) au format attendu par Minecraft Bedrock.
- **Télécharger** le résultat sous forme d'archive `.zip` prête à être importée.

### 2.2 Objectifs secondaires

- Proposer une **interface intuitive** accessible à des utilisateurs sans connaissance technique.
- Offrir une **prévisualisation en temps réel** de la texture et du rendu du bloc.
- *(Bonus)* Permettre la gestion de plusieurs blocs, l'authentification utilisateur et des comportements avancés.

---

## 3. Périmètre

Le périmètre délimite clairement ce qui fait partie du projet et ce qui en est exclu, afin d'éviter toute ambiguïté.

| **Inclus** | **Exclu** |
|---|---|
| Création de blocs personnalisés (cube simple) | Création d'items, entités ou mobs personnalisés |
| Upload et gestion de textures | Éditeur de textures intégré (dessin pixel par pixel) |
| Génération de fichiers JSON (behavior + resource pack) | Mod Java Edition (uniquement Bedrock) |
| Téléchargement d'archive `.zip` | Déploiement automatique sur un serveur Minecraft |
| Interface web responsive | Application mobile native |
| Propriétés de base du bloc (solidité, résistance, destructibilité) | Système de crafting ou de recettes |

---

## 4. Acteurs et cas d'utilisation

### 4.1 Acteurs

| **Acteur** | **Description** |
|---|---|
| **Utilisateur** | Personne accédant à l'application web pour créer un bloc et télécharger les fichiers générés |
| **Système** | Application web (backend + frontend) réalisant la génération des fichiers |
| **Administrateur** *(bonus)* | Personne gérant les comptes utilisateurs et les blocs sauvegardés |

### 4.2 Cas d'utilisation principal

**Parcours utilisateur :**

| **Étape** | **Action** | **Résultat** |
|---|---|---|
| 1 | L'utilisateur accède à l'application web | Le formulaire de création s'affiche |
| 2 | Il remplit le formulaire (nom, identifiant, propriétés) | Les champs sont validés en temps réel |
| 3 | Il upload une image de texture | La texture est affichée en prévisualisation |
| 4 | Il clique sur « Générer mon bloc » | Le backend génère les fichiers JSON et l'archive |
| 5 | Le téléchargement du `.zip` se lance | L'utilisateur obtient un pack prêt à importer dans Minecraft |

**Scénario nominal :**

1. L'utilisateur ouvre l'application dans son navigateur.
2. Il saisit le nom du bloc, son identifiant technique et ses propriétés de base.
3. Il upload une image (texture du bloc).
4. L'interface affiche une prévisualisation de la texture.
5. Il clique sur le bouton de génération.
6. Le serveur génère le behavior pack (`blocks.json`) et le resource pack (texture + `terrain_texture.json`).
7. Les fichiers sont compressés en `.zip` et le téléchargement démarre automatiquement.

---

## 5. Exigences fonctionnelles

Le projet est découpé en deux niveaux : le **minimum attendu**, qui constitue le socle obligatoire à livrer, et la **partie bonus**, qui valorise les groupes allant plus loin.

### 5.1 Minimum attendu

| **Fonctionnalité** | **Description** |
|---|---|
| Formulaire de création | Champs : nom du bloc, identifiant technique, type (cube simple) |
| Upload de texture | L'utilisateur peut envoyer une image (PNG, dimensions carrées) servant de texture au bloc |
| Prévisualisation | L'image uploadée est affichée en temps réel dans l'interface avant génération |
| Propriétés de base | L'utilisateur peut définir : solidité (oui/non), destructibilité (oui/non), résistance (valeur numérique) |
| Validation des champs | Le système vérifie les entrées (identifiant valide, image au bon format, champs obligatoires remplis) |
| Génération JSON | Le backend produit les fichiers `blocks.json` (behavior pack) et `terrain_texture.json` (resource pack) conformes au format Minecraft Bedrock |
| Génération de l'archive | Les fichiers générés + la texture uploadée sont empaquetés dans un `.zip` avec la structure Minecraft valide |
| Téléchargement | L'utilisateur peut télécharger le `.zip` généré en un clic |
| Interface responsive | L'application est utilisable sur desktop et mobile |

> *Le minimum attendu doit fournir un **parcours complet** : formulaire → upload → génération → téléchargement. Un utilisateur doit pouvoir créer un bloc fonctionnel dans Minecraft sans aucune intervention manuelle sur les fichiers.*

### 5.2 Partie bonus

| **Fonctionnalité** | **Description** |
|---|---|
| Texture par face | L'utilisateur peut assigner une texture différente à chaque face du bloc (haut, bas, côtés) |
| Bloc lumineux | Option pour définir un niveau de luminosité émise par le bloc |
| Bloc interactif | Comportement au clic (click event) configurable depuis le formulaire |
| Drop personnalisé | Définir ce que le bloc laisse tomber quand il est cassé |
| Rotation du bloc | Permettre la rotation du bloc selon l'orientation du joueur |
| Sauvegarde des blocs | L'utilisateur peut sauvegarder ses blocs créés et les retrouver plus tard |
| Édition d'un bloc existant | Modifier un bloc déjà créé sans repartir de zéro |
| Historique | Consultation des blocs précédemment générés |
| Authentification | Système de compte utilisateur avec dashboard personnel |
| Export serveur | Export spécifiquement formaté pour injection directe dans un serveur existant (behavior pack déjà en place) |

---

## 6. Exigences non fonctionnelles

| **Exigence** | **Détail** |
|---|---|
| Performance | Génération du `.zip` en moins de 5 secondes après soumission du formulaire |
| Format de sortie | Archive `.zip` respectant strictement la structure Minecraft Bedrock (behavior pack + resource pack) |
| Compatibilité Minecraft | Fichiers générés compatibles avec Minecraft Bedrock Edition (dernière version stable) |
| Taille d'image | Textures acceptées : PNG, dimensions carrées (16×16, 32×32, 64×64, 128×128 pixels) |
| Sécurité upload | Validation du type MIME, limitation de la taille du fichier, rejet de tout fichier non-image |
| Responsive | Interface fonctionnelle sur desktop (Chrome, Firefox, Edge) et mobile |
| Maintenabilité | Code structuré selon les conventions Laravel (controllers, services, routes), séparation front/back |

---

## 7. Structure des fichiers générés

Le système doit produire une archive `.zip` contenant une arborescence conforme au format Minecraft Bedrock. Voici la structure attendue pour un bloc nommé `my_block` :

```
generated_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── blocks/
│       └── my_block.json
└── resource_pack/
    ├── manifest.json
    ├── textures/
    │   └── blocks/
    │       └── my_block.png
    └── terrain_texture.json
```

### 7.1 Fichier behavior pack — `my_block.json`

Le fichier de comportement définit les propriétés du bloc côté serveur. Exemple de structure attendue :

```json
{
  "format_version": "1.16.0",
  "minecraft:block": {
    "description": {
      "identifier": "custom:my_block"
    },
    "components": {
      "minecraft:destroy_time": 1.5,
      "minecraft:explosion_resistance": 3.0,
      "minecraft:friction": 0.6
    }
  }
}
```

### 7.2 Fichier resource pack — `terrain_texture.json`

Le fichier de mapping associe l'identifiant du bloc à sa texture :

```json
{
  "resource_pack_name": "custom_blocks",
  "texture_name": "atlas.terrain",
  "texture_data": {
    "custom_my_block": {
      "textures": "textures/blocks/my_block"
    }
  }
}
```

> *Ces exemples sont indicatifs. La structure exacte doit respecter la documentation officielle Minecraft Bedrock au moment du développement.*

---

## 8. Architecture technique

### 8.1 Vue d'ensemble

Le système est organisé en deux couches qui communiquent via des routes HTTP :

**Frontend (navigateur) :**

- Formulaire de création de bloc avec validation côté client
- Upload d'image avec prévisualisation en temps réel
- Appel à l'API backend pour la génération, réception et déclenchement du téléchargement

**Backend (serveur Laravel) :**

| **Module** | **Rôle** | **Entrée → Sortie** |
|---|---|---|
| Routes API | Points d'entrée HTTP, reçoit les données du formulaire et le fichier image | Requête HTTP → dispatch vers le controller |
| Controller | Orchestre la logique : valide les entrées, appelle les services | Données validées → réponse HTTP (fichier `.zip`) |
| Service générateur JSON | Produit les fichiers `blocks.json`, `manifest.json`, `terrain_texture.json` | Paramètres du bloc → fichiers JSON |
| Service générateur ZIP | Assemble les fichiers JSON + texture dans une archive `.zip` | Fichiers générés + image → archive `.zip` |
| Stockage temporaire | Sauvegarde provisoire des fichiers avant téléchargement | Fichiers → disque temporaire |

### 8.2 Stack technique

| **Composant** | **Technologies** |
|---|---|
| **Backend** | PHP / Laravel (version 10 minimum) |
| **API** | Routes REST Laravel (JSON) |
| **Génération de fichiers** | Services Laravel (JSON natif PHP + ZipArchive) |
| **Frontend** | HTML / CSS / JavaScript |
| **Framework CSS** | Tailwind CSS (v3+) ou Bootstrap (v5+) |
| **Prévisualisation** | JavaScript natif (FileReader API pour l'aperçu d'image) |
| **Validation** | Validation Laravel côté serveur + validation JS côté client |

### 8.3 Endpoints API

| **Méthode** | **Route** | **Description** | **Entrée** | **Sortie** |
|---|---|---|---|---|
| `POST` | `/block/create` | Génère les fichiers du bloc et renvoie l'archive | Données formulaire + image (multipart) | Fichier `.zip` (téléchargement) |
| `GET` | `/block/download/{id}` | Télécharge un bloc précédemment généré *(bonus)* | Identifiant du bloc | Fichier `.zip` |
| `GET` | `/blocks` | Liste les blocs sauvegardés *(bonus)* | — | JSON (liste des blocs) |

---

## 9. Données et validation

### 9.1 Données du formulaire

| **Champ** | **Type** | **Contraintes** | **Obligatoire** |
|---|---|---|---|
| Nom du bloc | Texte | 1 à 50 caractères, alphanumérique + espaces | Oui |
| Identifiant technique | Texte | Minuscules, underscores uniquement, sans espaces (ex: `my_block`) | Oui |
| Texture | Fichier image | PNG, dimensions carrées, max 512 Ko | Oui |
| Solidité | Booléen | Oui / Non | Oui |
| Destructible | Booléen | Oui / Non | Oui |
| Résistance | Nombre | Valeur entre 0 et 100 | Oui |

### 9.2 Règles de validation

- L'identifiant technique doit être **unique** au sein d'un même pack (en cas de multi-blocs en bonus).
- L'image uploadée est vérifiée : **type MIME** (`image/png`), **dimensions carrées** et **taille maximale** (512 Ko).
- Tout champ manquant ou invalide provoque un message d'erreur explicite dans l'interface.
- Côté backend, la validation Laravel (`FormRequest`) protège contre les données malformées ou malveillantes.

---

## 10. Livrables

### 10.1 Livrables minimum

| **Livrable** | **Description** |
|---|---|
| **Application web fonctionnelle** | Formulaire de création + upload + génération + téléchargement `.zip` |
| **Service de génération** | Module Laravel produisant des fichiers JSON conformes au format Minecraft Bedrock |
| **Gestion multi-blocs** | Sauvegarde, édition et historique des blocs créés |
| **Export serveur** | Génération formatée pour injection directe dans un serveur existant |
| **Interface responsive** | Page web utilisable sur desktop et mobile, avec prévisualisation de la texture |
| **Rapport / documentation** | Rapport présentant la démarche, l'architecture et les choix techniques |

### 10.2 Livrables bonus

| **Livrable** | **Description** |
|---|---|
| **Authentification** | Système de comptes utilisateurs avec dashboard personnel |
| **Blocs avancés** | Support des comportements avancés (luminosité, interactivité, drop, rotation, texture par face) |

---

## 11. Planning prévisionnel

### 11.1 Phases minimum

| **Phase** | **Description** | **Livrables associés** |
|---|---|---|
| **Phase 1 — Cadrage** | Définir la structure des fichiers Minecraft Bedrock cibles, prototyper le formulaire | Spécifications techniques, maquette |
| **Phase 2 — Backend génération** | Développer les services Laravel : génération JSON + ZIP | Service de génération |
| **Phase 3 — Frontend formulaire** | Développer l'interface : formulaire, upload, prévisualisation | Interface web |
| **Phase 4 — Intégration** | Connecter frontend et backend, tester le parcours complet (formulaire → `.zip`) | Application fonctionnelle |
| **Phase 5 — Finalisation** | Tests, validation des fichiers dans Minecraft, documentation | Rapport |

### 11.2 Phases bonus (si le temps le permet)

| **Phase** | **Description** | **Livrables associés** |
|---|---|---|
| **Bonus A — Blocs avancés** | Ajouter les propriétés avancées (luminosité, drop, texture par face…) | Blocs avancés |
| **Bonus B — Gestion utilisateur** | Authentification, sauvegarde des blocs, dashboard | Authentification, gestion multi-blocs |
| **Bonus C — Export serveur** | Adaptation de l'export pour injection dans un serveur existant | Export serveur |

---

## 12. Glossaire

| **Terme** | **Définition** |
|---|---|
| **Behavior Pack** | Pack de données Minecraft Bedrock définissant le comportement des éléments (blocs, entités, items) côté serveur |
| **Resource Pack** | Pack de données Minecraft Bedrock contenant les ressources visuelles (textures, modèles, sons) côté client |
| **Bloc** | Unité de base du monde Minecraft, occupant un espace cubique dans la grille 3D du jeu |
| **Identifiant technique** | Nom interne unique d'un bloc au format `namespace:nom` (ex: `custom:my_block`), utilisé dans les fichiers JSON |
| **terrain_texture.json** | Fichier du resource pack qui associe chaque identifiant de bloc à son fichier de texture |
| **manifest.json** | Fichier de métadonnées décrivant un pack Minecraft (nom, version, type, UUID) |
| **ZIP** | Format d'archive compressée utilisé pour regrouper les fichiers du pack |
| **Laravel** | Framework PHP pour le développement d'applications web, utilisé ici côté backend |
| **Tailwind CSS** | Framework CSS utilitaire permettant de construire des interfaces rapidement via des classes prédéfinies |
| **Responsive** | Conception web adaptant l'affichage à la taille de l'écran (desktop, tablette, mobile) |
