# BYT3LAB WordPress Theme Builder

Un plugin WordPress avancé pour la création, la personnalisation et la génération de thèmes WordPress en totale autonomie, directement depuis l'interface de l'administration, avec une approche orientée "Composants".

## 🚀 Fonctionnalités Principales (Pour les Développeurs)

- **Génération dynamique de thèmes** : Crée à la volée une structure complète de thème WordPress (`wp-content/themes/votre-theme`) avec ses répertoires respectifs (`components`, `pages`, `assets`).
- **Architecture Component-Driven** : Un composant = un dossier. Chaque composant encapsule son `php`, son `css` et son `js`.
- **Système d'Enqueue Natif WordPress** : L'injection des CSS et JS de pages ou de composants se fait **exclusivement** via le hook `wp_enqueue_scripts` et un fichier de configuration JSON dédié par page, garantissant la performance et l'absence de *Flash of Unstyled Content (FOUC)*.
- **Rendu hiérarchique dynamique** : Lecture de `pages/page-{slug}.json` pour déterminer exactement quels composants charger via `get_template_part()` dans quel ordre.

## 📂 Structure du projet (Code Source du Plugin)

```text
byt3lab_wordpress_theme_builder/
├── byt3lab-builder.php         # Point d'entrée principal du plugin
├── core/                       # Moteurs de génération
│   ├── Application.php         # Initialisation du constructeur
│   ├── ThemeGenerator.php      # Générateur de la structure mère
│   ├── PageGenerator.php       # Générateur de stubs de pages PHP
│   ├── ComponentGenerator.php  # Générateur de dossiers de composants
│   └── ConfigManager.php       # Gestion des I/O JSON
├── admin/                      # Contrôleurs Backend
│   ├── AdminMenu.php           # Enregistrement des pages d'admin
│   └── *Controller.php         # Routes de traitement (Themes, Pages, Settings...)
├── views/                      # Vues HTML de l'interface d'administration
├── stubs/                      # Modèles bruts (templates) pour la génération (.stub)
└── docs/                       # Documentation Utilisateur Front-End
```

## 🛠️ Architecture du thème cible (Généré)

Lorsqu'un thème est généré par `ThemeGenerator`, voici sa structure finale dans `wp-content/themes/[theme-slug]/` :

```text
mon-theme/
├── config.json                 # Core settings (front-page, 404, etc.)
├── functions.php               # Gère l'Enqueue automatique des JS/CSS via les JSON de pages
├── assets/                     # Assets globaux du thème
│   ├── css/
│   └── js/
├── components/                 # Les composants isolés
│   └── header/
│       ├── header.php
│       ├── header.css
│       └── header.js
└── pages/                      # Fichiers lus par page.php dynamique
    ├── page-home.php           # Stub propre (sans inline assets)
    └── page-home.json          # Array : components[], css_files[], js_files[]
```

## 🔧 Extensibilité

- **Ajouter un Modèle de Génération** : Modifiez les fichiers `.stub` dans `/stubs/theme/`.
- **Ajouter une Route** : 
  1. Enregistrer le submenu dans `AdminMenu.php`.
  2. Créer une classe `[Nom]Controller.php` dans `/admin/`.
  3. Lier au rendu HTML dans `/views/nom.php`.

## 🤝 Contribution

Veuillez consulter le fichier `AGENTS.md` pour prendre connaissance de l'architecture spécifique imposée aux Intelligences Artificielles qui modifient ce code.
