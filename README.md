<div align="center">

# Onyx

**Application de prise de notes**

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38BDF8?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)

</div>

---

## Présentation

> À compléter

---

## Fonctionnalités

> À compléter

---

## Aperçu

> Les captures d'écran seront ajoutées prochainement.

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 13, PHP 8.4+ |
| Frontend | Vue.js 3, Inertia.js 3 |
| Style | Tailwind CSS 3 |
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

# Installer toutes les dépendances (composer + npm)
make install
```

### Démarrage en développement

```bash
make start
```

Lance en parallèle : le serveur PHP, Vite et mailcatcher.

---

## Commandes utiles

```bash
# Développement
make start             # démarrer mailcatcher + serveurs de développement
make stop              # arrêter mailcatcher

# Base de données
make migrate           # exécuter les migrations
make fixtures          # repartir de zéro (drop + migrate + seed)

# Qualité du code
make fix               # auto-correction + analyse statique
make stan              # PHPStan seul
make rector            # Rector (dry-run)

# Tests
make test              # suite complète

# Utilitaires
make cc                # vider tous les caches
make help              # lister toutes les commandes disponibles
```

---

## Licence

MIT
