<div align="center">

# Onyx

**Application de prise de notes**

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-4-38BDF8?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)

</div>

---

## Présentation

Onyx est une application web de prise de notes inspirée d'Obsidian. Elle offre un éditeur Markdown complet avec aperçu en temps réel, des liens wiki entre notes, un graphe de connexions interactif, des callouts et des commandes slash — le tout dans une interface sobre et sombre.

Disponible en thème sombre et clair, entièrement responsive, Onyx est conçu pour organiser vos idées avec puissance et simplicité.

---

## Fonctionnalités

- **Éditeur Markdown** — Support complet : titres, gras, italique, listes, cases à cocher interactives, tableaux, blocs de code avec coloration syntaxique, séparateurs, citations et code inline
- **Aperçu en temps réel** — Bascule édition/aperçu par note, préférence mémorisée automatiquement
- **Liens wiki** — Créez des liens entre notes avec `[[titre]]`, naviguez d'une note à l'autre, consultez les backlinks et mentions non liées
- **Graphe de notes** — Carte visuelle interactive de toutes les connexions entre vos notes
- **Callouts** — Blocs info, tip, note, warning, success style Obsidian
- **Commandes slash** — Palette de commandes accessible avec `/` pour insérer des blocs rapidement (titres, listes, citation, cases à cocher…)
- **Images** — Insertion par drag & drop ou collage depuis le presse-papier (Ctrl+V), rendu dans l'aperçu
- **Tags** — Taguez vos notes et filtrez par tag
- **Recherche** — Recherche plein texte dans toutes vos notes
- **Arborescence** — Organisation hiérarchique des notes avec dossiers
- **Note du jour** — Ouvre ou crée automatiquement la note du jour
- **Templates** — Sauvegardez une note comme modèle réutilisable
- **Sommaire** — Navigation rapide vers les titres de la note en cours
- **Guide intégré** — Lexique de syntaxe complet et aperçu de démonstration
- **Multilingue** — Français, anglais, espagnol, allemand
- **Profil** — Langue, informations personnelles, mot de passe, suppression de compte
- **Administration** — Statistiques, gestion des utilisateurs, invitations, paramètres

---

## Aperçu

### Connexion

![Connexion](docs/screenshots/login.jpg)

> Page de connexion en deux colonnes : présentation des fonctionnalités à gauche, formulaire à droite.

---

### Inscription

![Inscription](docs/screenshots/registration.jpg)

> Formulaire d'inscription : nom, e-mail, mot de passe et confirmation.

---

### Tableau de bord

![Tableau de bord](docs/screenshots/dashboard.jpg)

> Accueil personnalisé après connexion.

---

### Notes

![Notes](docs/screenshots/note-empty.jpg)

> Vue principale de l'espace notes : arborescence à droite, éditeur au centre, recherche et création rapide.

---

### Start here — liens wiki & callouts

![Édition](docs/screenshots/markdown-example-1.jpg)
![Aperçu](docs/screenshots/preview-example-1.jpg)

> Note d'index avec liens wiki `[[...]]` vers d'autres notes et callouts info/tip. En aperçu, les liens sont cliquables et les callouts colorés.

---

### Projects — tableau & callout warning

![Édition](docs/screenshots/markdown-example-2.jpg)
![Aperçu](docs/screenshots/preview-example-2.jpg)

> Tableau de suivi de projets avec liens wiki dans les cellules et callout warning. En aperçu, le tableau est mis en forme et les liens sont navigables.

---

### Onyx Development — cases à cocher & blocs de code

![Édition](docs/screenshots/markdown-example-3.jpg)
![Aperçu](docs/screenshots/preview-example-3.jpg)

> Liste de tâches avec cases à cocher, callout note et bloc de code. En aperçu, les cases sont interactives et le code est indenté.

---

### Mobile App Idea — callouts multiples & liens wiki

![Édition](docs/screenshots/markdown-example-4.jpg)
![Aperçu](docs/screenshots/preview-example-4.jpg)

> Note d'idées avec cases à cocher, callouts question/example et lien wiki vers une autre note. En aperçu, chaque callout a sa couleur distincte.

---

### Learning — liste de liens wiki

![Édition](docs/screenshots/markdown-example-5.jpg)
![Aperçu](docs/screenshots/preview-example-5.jpg)

> Note index de dossier avec liste de liens wiki et callout abstract. En aperçu, tous les liens sont navigables vers leurs notes respectives.

---

### Vue 3 Notes — blocs de code JavaScript

![Édition](docs/screenshots/markdown-example-6.jpg)
![Aperçu](docs/screenshots/preview-example-6.jpg)

> Note technique avec plusieurs blocs de code JavaScript. En aperçu, la coloration syntaxique est appliquée via highlight.js.

---

### Laravel Tips — code PHP, tableau & callout danger

![Édition](docs/screenshots/markdown-example-7.jpg)
![Aperçu](docs/screenshots/preview-example-7.jpg)

> Note de référence technique avec blocs de code PHP, callout danger et tableau Artisan. En aperçu, le PHP est coloré et le tableau est mis en forme.

---

### Journal — note du jour & callout tip

![Édition](docs/screenshots/markdown-example-8.jpg)
![Aperçu](docs/screenshots/preview-example-8.jpg)

> Dossier journal avec callout tip présentant la fonctionnalité "Note du jour". En aperçu, le callout est mis en valeur avec son icône.

---

### 2026-04-13 — note quotidienne & callout success

![Édition](docs/screenshots/markdown-example-9.jpg)
![Aperçu](docs/screenshots/preview-example-9.jpg)

> Note du jour avec tâches terminées/en cours, callout success et lien wiki vers un projet. En aperçu, les cases sont interactives et le callout vert est visible.

---

### 2026-04-12 — note quotidienne & code avec callout info

![Édition](docs/screenshots/markdown-example-10.jpg)
![Aperçu](docs/screenshots/preview-example-10.jpg)

> Note quotidienne avec liste de tâches, callout info et bloc de code JavaScript illustrant une extension marked.js. En aperçu, le code est mis en couleur.

---

### Reference — cheat sheet callouts & raccourcis

![Édition](docs/screenshots/markdown-example-11.jpg)
![Aperçu](docs/screenshots/preview-example-11.jpg)

> Note de référence avec tableaux listant tous les types de callouts disponibles et les raccourcis clavier. En aperçu, les tableaux sont alignés et lisibles.

---

### Ideas — brainstorm avec callouts variés

![Édition](docs/screenshots/markdown-example-12.jpg)
![Aperçu](docs/screenshots/preview-example-12.jpg)

> Note de brainstorm libre avec callouts example, question et bug, listes et liens wiki. En aperçu, chaque callout a sa couleur selon son type.

---

### Images

![Image — édition](docs/screenshots/markdown-image.png)

![Image — aperçu](docs/screenshots/preview-image.png)

> Insertion d'images par drag & drop ou collage depuis le presse-papier — rendu directement dans l'aperçu. La taille est redimensionnable dynamiquement en glissant le coin inférieur droit de l'image, ou via la syntaxe `![alt](url){width=300}` ou `![alt](url){width=300 height=200}`.

---

### Commandes slash

![Commandes slash](docs/screenshots/slash-shortcut.png)

> Palette de commandes accessible avec `/` en début de ligne : titres, listes, cases à cocher, citation…

---

### Graphe de notes

![Graphe](docs/screenshots/graphe.jpg)

> Carte visuelle interactive de toutes les connexions entre vos notes via les liens wiki.

---

### Guide — Lexique

![Guide — Lexique](docs/screenshots/guide-lexique.png)

> Référence complète de la syntaxe Markdown supportée par Onyx, avec exemples côte à côte.

---

### Guide — Aperçu

![Guide — Aperçu](docs/screenshots/guide-apercu.png)

> Note de démonstration en lecture seule illustrant les capacités de rendu de l'éditeur.

---

### Profil

![Profil](docs/screenshots/profil.png)

> Gestion du profil : langue d'affichage, informations personnelles, changement de mot de passe et suppression de compte.

---

### Administration

![Administration](docs/screenshots/administration.jpg)

> Dashboard admin : statistiques globales (utilisateurs, notes), gestion des utilisateurs, invitations et paramètres.

---

## Architecture — SPA sans API REST

Onyx est une SPA (Single Page Application) construite sans API REST exposée. C'est le choix technique central du projet.

### Inertia.js : le pont Laravel ↔ Vue

Sans Inertia, construire une SPA nécessite soit une API REST dédiée (et donc dupliquer la logique métier), soit du rendu serveur classique (et perdre la fluidité du frontend). Inertia résout ce dilemme en servant de pont entre les deux mondes.

```
[Browser]                           [Laravel server]
     │                                      │
     │  Initial request (HTML)              │  Render layout + page component
     │ ◄──────────────────────────────────  │  + JSON data injected
     │                                      │
     │  Navigation (Inertia visit)          │
     │  ──────────────────────────────────► │  Controller → Inertia::render('Page', $data)
     │ ◄──────────────────────────────────  │  JSON response {component, props, url}
     │                                      │
     │  Vue swaps the component             │
     │  (no full page reload)               │
```

- Le contrôleur retourne `Inertia::render('Notes/Index', ['notes' => $notes])` — pas de sérialisation manuelle, pas de route API
- Vue reçoit les données comme des props directement typées
- La navigation est fluide (SPA) sans écrire une seule ligne de fetch/axios pour les données de page
- Les règles d'autorisation, la validation, les redirections restent dans Laravel — la seule source de vérité

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 13, PHP 8.4+ |
| Frontend | Vue.js 3, Inertia.js 3 |
| Style | Tailwind CSS 4 |
| Éditeur | marked.js 18, highlight.js 11 |
| Sécurité | DOMPurify |
| Auth & permissions | Laravel Sanctum, Spatie Permissions |
| Emails | Resend |
| Build | Vite 8 |
| Base de données | PostgreSQL |

---

## Installation

### Prérequis

- PHP >= 8.4
- Composer >= 2
- Node.js / pnpm
- PostgreSQL

### Mise en place

```bash
# Cloner le dépôt
git clone https://github.com/AxelRaboit/onyx.git
cd onyx

# Copier le fichier d'environnement et configurer
cp .env.example .env
php artisan key:generate

# Installer toutes les dépendances (composer + tools + pnpm)
make install

# Exécuter les migrations
make migrate
```

### Démarrage en développement

```bash
make start
```

Lance en parallèle : le mailer (via Docker), le serveur PHP et Vite.

---

## Commandes utiles

```bash
# Développement
make start             # démarrer le mailer + serveurs de développement
make stop              # arrêter le mailer

# Base de données
make migrate           # exécuter les migrations
make migrate-fresh     # repartir de zéro (drop + migrate)
make fixtures          # migrate-fresh + seed + synchronisation des paramètres

# Qualité du code
make fix               # auto-correction (Rector, Pint, ESLint) + PHPStan
make stan              # PHPStan seul

# Tests
make test              # suite complète
make test-unit         # tests unitaires uniquement
make test-feature      # tests de fonctionnalité uniquement

# Utilisateurs
make role-dev EMAIL=user@example.com   # passer un utilisateur en ROLE_DEV

# Utilitaires
make cc                # vider tous les caches
make help              # lister toutes les commandes disponibles
```

---

## Licence

MIT
