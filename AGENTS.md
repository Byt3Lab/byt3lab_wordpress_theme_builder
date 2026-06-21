# Instructions pour les Agents IA (AGENTS.md)

Ce fichier définit les règles architecturales strictes du plugin **BYT3LAB WordPress Theme Builder** que tout Agent (IA) doit respecter lors de l'édition du code ou de l'ajout de fonctionnalités.

---

## 🛑 1. Règles d'Architecture Fondamentales (Theme Builder)

* **Approche Component-Driven** : Tout élément visuel du thème généré DOIT être un composant.
  * Structure exigée par composant : un dossier nommé par son slug, contenant `slug.php`, `slug.css` et `slug.js` (situé dans `themes/[slug]/components/[slug]/`).
* **Gestion des Assets (CSS/JS)** : Il est **STRICTEMENT INTERDIT** d'injecter des balises `<link>` ou `<script>` directement (en inline / echo) dans le `<body>` ou dans les fichiers `page-*.php` générés.
  * *Raison* : Cela provoque des erreurs 404 et des problèmes de FOUC avec WordPress.
  * *Solution imposée* : L'agent doit persister les chemins relatifs (ex: `assets/css/main.css`) dans le tableau du fichier JSON (ex: `page-home.json`). Le fichier `functions.php` du thème généré se charge automatiquement de lire ce JSON et de faire les hooks `wp_enqueue_scripts`.
* **Sanitization Différenciée** :
  * Pour les slugs et noms de fichiers : utiliser `sanitize_title()` ou `sanitize_file_name()`.
  * **ATTENTION** : Ne **jamais** utiliser `sanitize_file_name()` pour des chemins de fichiers globaux contenant des slashs (`/`), car cela supprime l'arborescence (ex: `assets/css/test.css` deviendrait `assetscsstest.css`). Utiliser `sanitize_text_field()`.

## ⚙️ 2. Comment le code interne du Plugin est divisé

Si l'utilisateur demande d'ajouter un nouveau menu à l'interface d'administration :

1. **Le Menu (`AdminMenu.php`)** : Ajoutez un `add_submenu_page` avec un raccourci vers la méthode `render()` de votre futur contrôleur.
2. **Le Contrôleur (`admin/...Controller.php`)** : Traitez les variables POST/GET, gérez les nonces de sécurité (`check_admin_referer`), définissez vos variables, puis exigez la vue (`require BYT3LAB_BUILDER_PATH . 'views/...php';`).
3. **Le Moteur ('core/...Generator.php')** : Séparer logiquement l'I/O (création de dossiers, `FileManager->putContents()`) loin du contrôleur. Un Controller ne doit pas écrire de fichiers directement.
4. **La Vue (`views/...php`)** : Uniquement du HTML imbriqué avec du PHP (aucun traitement lourd, uniquement de l'affichage et des boucles sur les variables fournies par le contrôleur).

## 📄 3. Fichiers Stubs (Templates d'origine)

Si vous devez modifier ce à quoi ressemble un fichier nouvellement généré (par exemple si l'utilisateur demande "Quand je crée un thème, je veux que le CSS contienne tel commentaire"):
* Ne modifiez pas le PHP dur dans le code.
* Allez éditer les fichiers `.stub` dans le répertoire `stubs/`. 
* *Exception* : S'il s'agit d'un micro fragment de code, il peut se trouver sous forme de variable `<<<'PHP'` ou `$phpContent` dans des générateurs comme `PageGenerator.php`.

## 🛡️ 4. Sécurité

* Toutes les vues de formulaires POST doivent inclure `<?php wp_nonce_field('une_action_nonce'); ?>`.
* Tous les contrôleurs traitant des POST doivent avoir `check_admin_referer('une_action_nonce');` au début de leur processus PHP.

**Maintien de Cap** : Votre objectif majeur en tant qu'agent est de conserver l'architecture propre, asynchrone pour les développeurs, mais unifiée par la philosophie **"Outil GUI générant du code natif WordPress pur qui ne dépend plus du constructeur une fois exporté"**.
