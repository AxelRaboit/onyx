<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class DemoFixtureService
{
    private const string PREFIX = 'fixture_';

    public function clear(User $user): void
    {
        $user->notes()->delete();
        $this->clearImages();
    }

    public function seed(User $user): void
    {
        $imgs = $this->generateImages();
        $this->createNotes($user, $imgs);
    }

    // ── Images ───────────────────────────────────────────────────────────────

    private function dir(): string
    {
        return storage_path('app/private/note-images');
    }

    private function clearImages(): void
    {
        $dir = $this->dir();
        if (! is_dir($dir)) {
            return;
        }

        foreach (glob($dir.'/'.self::PREFIX.'*') ?: [] as $file) {
            @unlink($file);
        }
    }

    /**
     * @return array<string, string> map of key → filename
     */
    private function generateImages(): array
    {
        $dir = $this->dir();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Deterministic seeds from picsum.photos — same image every run
        $sources = [
            'dashboard' => 'https://picsum.photos/seed/onyx-dashboard/960/600',
            'architecture' => 'https://picsum.photos/seed/onyx-architecture/800/500',
            'photo' => 'https://picsum.photos/seed/onyx-photo/800/450',
            'terminal' => 'https://picsum.photos/seed/onyx-terminal/800/400',
            'cuisine' => 'https://picsum.photos/seed/onyx-cuisine/800/500',
            'voyage' => 'https://picsum.photos/seed/onyx-voyage/960/540',
            'livre' => 'https://picsum.photos/seed/onyx-livre/700/450',
            'sport' => 'https://picsum.photos/seed/onyx-sport/800/450',
        ];

        $images = [];

        foreach ($sources as $key => $url) {
            $name = self::PREFIX.$key.'.jpg';
            $path = $dir.'/'.$name;

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                file_put_contents($path, $response->body());
            }

            $images[$key] = $name;
        }

        return $images;
    }

    private function imgUrl(string $filename): string
    {
        return '/notes/images/'.$filename;
    }

    // ── Notes ────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createNotes(User $user, array $imgs): void
    {
        $this->createIndex($user, $imgs);
        $this->createGuide($user, $imgs);
        $this->createProjects($user, $imgs);
        $this->createJournal($user, $imgs);
        $this->createReference($user);
        $this->createCuisine($user, $imgs);
        $this->createLivres($user, $imgs);
        $this->createVoyages($user, $imgs);
        $this->createSport($user, $imgs);
        $this->createFinance($user);
        $this->createIdeas($user);
    }

    private function note(User $user, array $data): Note
    {
        return Note::create(array_merge(['user_id' => $user->id], $data));
    }

    // ── Index ────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createIndex(User $user, array $imgs): void
    {
        $this->note($user, [
            'position' => 0,
            'title' => 'Start here',
            'tags' => ['index', 'home'],
            'content' => sprintf(<<<'MD'
            Bienvenue dans **Onyx** — votre espace de notes personnel.

            ![Aperçu de l'interface](%s)

            ## Navigation

            - [[Guide]] — toutes les fonctionnalités expliquées
            - [[Projects]] — projets en cours et idées
            - [[Journal]] — notes quotidiennes
            - [[Reference]] — cheat sheets et ressources
            - [[Ideas]] — brainstorm libre

            > [!tip] Raccourci
            > Tape `/` en début de ligne pour insérer un bloc, ou `[[` pour lier une note.

            > [!info] À propos d'Onyx
            > Onyx supporte le Markdown complet, les liens wiki, les callouts, les blocs de code avec coloration syntaxique et les cases à cocher interactives.
            MD, $this->imgUrl($imgs['dashboard'])),
        ]);
    }

    // ── Guide ────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createGuide(User $user, array $imgs): void
    {
        $guide = $this->note($user, [
            'position' => 1,
            'title' => 'Guide',
            'tags' => ['guide', 'docs'],
            'content' => <<<'MD'
            Index de toutes les fonctionnalités d'Onyx.

            - [[Markdown]] — syntaxe complète
            - [[Callouts]] — tous les types disponibles
            - [[Images et médias]] — intégrer des visuels dans vos notes

            > [!abstract] But
            > Ce guide est une note de référence vivante. Mets-le à jour au fil de l'utilisation.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $guide->id,
            'position' => 0,
            'title' => 'Markdown',
            'tags' => ['markdown', 'syntax'],
            'content' => <<<'MD'
            ## Titres

            ```markdown
            # H1
            ## H2
            ### H3
            ```

            ## Emphase

            ```markdown
            **gras**   *italique*   ~~barré~~   `code inline`
            ```

            Rendu : **gras** *italique* ~~barré~~ `code inline`

            ## Listes

            ```markdown
            - item non ordonné
            1. item ordonné
            - [ ] tâche à faire
            - [x] tâche faite
            ```

            - [ ] Lire la documentation
            - [x] Installer Onyx
            - [ ] Créer mes premières notes

            ## Tableaux

            | Colonne A | Colonne B | Colonne C |
            | --- | --- | --- |
            | Valeur 1 | Valeur 2 | Valeur 3 |
            | `code` | **gras** | *italique* |

            ## Blocs de code

            ```javascript
            const onyx = {
                editor: 'markdown',
                links: 'wiki',
                encrypted: true,
            }
            ```

            ```php
            // Chiffrement transparent via Eloquent
            protected function casts(): array
            {
                return ['content' => 'encrypted'];
            }
            ```

            ## Citations

            > Une citation simple.
            >
            > Sur plusieurs lignes.

            ## Séparateur

            ---

            ## Lien wiki

            Syntaxe : `[[Titre de la note]]` — génère un lien cliquable vers la note.

            Exemple : voir [[Reference]] pour le cheatsheet complet.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $guide->id,
            'position' => 1,
            'title' => 'Callouts',
            'tags' => ['callouts', 'blocks'],
            'content' => <<<'MD'
            Les callouts sont inspirés d'Obsidian. Syntaxe : `> [!type] Titre optionnel`.

            > [!note] Note
            > Information neutre ou remarque générale.

            > [!info] Info
            > Contexte ou explication complémentaire.

            > [!tip] Conseil
            > Astuce pratique ou bonne pratique.

            > [!success] Succès
            > Confirmation d'une action réussie ou d'un résultat positif.

            > [!warning] Attention
            > Point à surveiller ou risque potentiel.

            > [!danger] Danger
            > Erreur critique ou action irréversible.

            > [!bug] Bug
            > Problème connu, comportement inattendu.

            > [!example] Exemple
            > Illustration concrète du concept.

            > [!question] Question
            > Interrogation ouverte ou FAQ.

            > [!quote] Citation
            > Extrait ou référence externe.

            > [!todo] Todo
            > Liste d'actions à effectuer.

            > [!abstract] Résumé
            > Synthèse ou vue d'ensemble.

            > [!failure] Échec
            > Ce qui n'a pas fonctionné, retour d'expérience.

            Voir [[Reference]] pour le tableau récapitulatif.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $guide->id,
            'position' => 2,
            'title' => 'Images et médias',
            'tags' => ['images', 'médias'],
            'content' => sprintf(<<<'MD'
            ## Insérer une image

            Glisse-dépose une image dans l'éditeur, ou utilise le bouton d'upload dans la barre d'outils.

            La syntaxe générée est du Markdown standard :

            ```markdown
            ![texte alternatif](/notes/images/nom-du-fichier.webp)
            ```

            ## Exemple — paysage

            ![Paysage nature](%s)

            ## Exemple — terminal

            ![Terminal de développement](%s)

            ## Notes techniques

            - Les images sont converties en **WebP** automatiquement (gain de poids ~30%%)
            - Résolution maximale : **1920×1920 px**
            - Les images sont stockées de façon privée — accessibles uniquement aux utilisateurs authentifiés

            > [!tip] Glisser-déposer
            > Tu peux directement glisser une image depuis ton bureau dans l'éditeur pour l'insérer.

            > [!warning] Suppression
            > Supprimer une note ne supprime **pas** automatiquement les images qu'elle contient. Utiliser la commande `onyx:prune-orphaned-images` pour nettoyer le stockage.
            MD, $this->imgUrl($imgs['photo']), $this->imgUrl($imgs['terminal'])),
        ]);
    }

    // ── Projects ─────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createProjects(User $user, array $imgs): void
    {
        $projects = $this->note($user, [
            'position' => 2,
            'title' => 'Projects',
            'tags' => ['projects'],
            'content' => <<<'MD'
            Dossier de tous les projets actifs et archivés.

            | Projet | Statut | Priorité |
            | --- | --- | --- |
            | [[Onyx Development]] | 🟢 Actif | Haute |
            | [[Mobile App Idea]] | 🟡 En pause | Moyenne |

            > [!warning] Revue hebdomadaire
            > Mettre à jour les statuts chaque lundi.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $projects->id,
            'position' => 0,
            'title' => 'Onyx Development',
            'tags' => ['dev', 'onyx', 'laravel'],
            'content' => sprintf(<<<'MD'
            ## Objectif

            Construire une application de notes chiffrées, inspirée d'Obsidian, avec Laravel + Vue 3 + Inertia.

            ## Architecture

            ![Diagramme d'architecture](%s)

            ## Tâches

            - [x] Structure de base (auth, migrations, modèles)
            - [x] Éditeur Markdown avec aperçu
            - [x] Wiki-links `[[note]]` et backlinks
            - [x] Callouts Obsidian-style
            - [x] Commandes slash `/`
            - [x] Graphe de notes
            - [x] Upload d'images
            - [ ] Recherche full-text
            - [ ] Mode hors-ligne (PWA)
            - [ ] Partage de notes

            ## Stack technique

            ```php
            // Note.php — chiffrement transparent
            protected function casts(): array
            {
                return [
                    'title'   => 'encrypted',
                    'content' => 'encrypted',
                    'tags'    => 'array',
                ];
            }
            ```

            > [!note] Chiffrement
            > Les champs `title` et `content` sont chiffrés via le cast `encrypted` de Laravel. La clé d'application est la seule façon de déchiffrer.

            Voir aussi : [[Vue 3 Notes]] pour les notes frontend associées.
            MD, $this->imgUrl($imgs['architecture'])),
        ]);

        $this->note($user, [
            'parent_id' => $projects->id,
            'position' => 1,
            'title' => 'Mobile App Idea',
            'tags' => ['idée', 'mobile'],
            'content' => <<<'MD'
            ## Concept

            Une app mobile compagnon pour Onyx — consultation et création rapide de notes depuis le téléphone.

            ## Fonctionnalités envisagées

            - [ ] Auth biométrique
            - [ ] Sync offline avec reconciliation
            - [ ] Widget de note rapide
            - [ ] Partage vers une note depuis d'autres apps

            > [!question] Stack mobile ?
            > React Native vs Flutter vs Capacitor (réutiliser le frontend Vue existant).

            > [!example] Référence
            > Regarder comment Obsidian Mobile gère le sync — approche vault local + plugin sync payant.

            En lien avec : [[Onyx Development]]
            MD,
        ]);
    }

    // ── Journal ───────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createJournal(User $user, array $imgs): void
    {
        $journal = $this->note($user, [
            'position' => 3,
            'title' => 'Journal',
            'tags' => ['journal'],
            'content' => <<<'MD'
            Notes quotidiennes — une note par jour.

            > [!tip] Note du jour
            > Utilise le bouton **Note du jour** dans la barre latérale pour ouvrir ou créer automatiquement la note d'aujourd'hui.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 0,
            'title' => '2026-04-21',
            'tags' => ['journal', 'daily'],
            'content' => sprintf(<<<'MD'
            ## Aujourd'hui

            - [x] Implémenter le système de fixtures pour Onyx
            - [x] Ajouter le support des images dans les fixtures
            - [ ] Tester le responsive mobile
            - [ ] Review du code

            ## Photo du jour

            ![Paysage](%s)

            ## Notes

            Bonne session. Le système de fixtures génère maintenant des images réalistes directement depuis PHP/GD, sans dépendance externe. Le contenu des notes couvre tous les cas d'usage : callouts, code, tableaux, images, wiki-links, checkboxes.

            Travaillé sur : [[Onyx Development]]

            > [!success] Fixtures opérationnelles
            > La commande `onyx:seed-fixtures` permet de regénérer les données de démo sur n'importe quel environnement, prod inclus.

            ## Demain

            - [ ] Implémenter la recherche full-text
            - [ ] Tester sur Safari iOS
            MD, $this->imgUrl($imgs['photo'])),
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 1,
            'title' => '2026-04-20',
            'tags' => ['journal', 'daily'],
            'content' => <<<'MD'
            ## Aujourd'hui

            - [x] Setup initial du projet Onyx
            - [x] Migrations : notes, application_parameters
            - [x] Authentification et rôles Spatie
            - [x] Éditeur Markdown de base
            - [x] Upload d'images

            ## Découvertes

            > [!info] Marked.js extensions
            > Il est possible d'écrire des extensions Marked pour transformer la syntaxe Obsidian (`> [!type]`) en HTML avec les bonnes classes CSS. Beaucoup plus propre qu'un regex post-processing.

            ```javascript
            const calloutExtension = {
              name: 'callout',
              level: 'block',
              start(src) { return src.indexOf('> [!') },
            }
            ```

            Voir [[Guide]] pour les détails.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 2,
            'title' => '2026-04-19',
            'tags' => ['journal', 'daily'],
            'content' => <<<'MD'
            ## Réflexions

            Pris le temps de regarder comment Obsidian gère le chiffrement bout-en-bout dans ses vaults.

            > [!quote] Extrait de la doc Obsidian
            > "Your notes are stored locally on your device and never sent to our servers."

            Onyx prend une approche différente : stockage serveur mais chiffrement au niveau de la base de données avec la clé applicative. Trade-off : accessibilité multi-device vs confiance absolue.

            ## À lire

            - [ ] RFC sur le chiffrement homomorphe (calcul sur données chiffrées)
            - [ ] Article "End-to-end encryption in web apps" — web.dev
            - [ ] Spec WebCrypto API

            En lien avec : [[Onyx Development]]
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 3,
            'title' => '2026-04-18',
            'tags' => ['journal', 'daily'],
            'content' => <<<'MD'
            ## Aujourd'hui

            - [x] Réunion de synchro équipe
            - [x] Code review PR #42
            - [x] Déploiement v1.3 en staging
            - [ ] Rédiger les release notes

            ## Extrait de réunion

            Points clés discutés :
            - Migration vers PHP 8.4 planifiée pour Q3
            - Refonte du système de cache (passer de Redis à Valkey)
            - Onboarding du nouveau dev la semaine prochaine

            > [!warning] À ne pas oublier
            > Prévenir l'hébergeur pour la migration PHP. Vérifier la compatibilité des extensions.

            ## Humeur

            Bonne journée productive. La PR était propre, merge rapide.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 4,
            'title' => '2026-04-15',
            'tags' => ['journal', 'daily'],
            'content' => <<<'MD'
            ## Lectures du matin

            Fini le chapitre 7 de "A Philosophy of Software Design" — sur la gestion des exceptions.

            > [!quote] John Ousterhout
            > "The best way to reduce the complexity damage done by exception handling is to reduce the number of places where exceptions have to be handled."

            Résumé personnel : préférer des interfaces qui n'échouent pas plutôt que des interfaces qui exposent toutes les erreurs possibles. Ça rejoint le principe "fail fast, fail loud" au niveau système mais "be silent" au niveau API interne.

            Voir [[Livres lus]] pour ma fiche complète.

            ## Soir

            - [x] Course → [[Risotto aux champignons]]
            - [x] Appel parents
            - [ ] Préparer la semaine
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $journal->id,
            'position' => 5,
            'title' => '2026-04-10',
            'tags' => ['journal', 'weekly'],
            'content' => <<<'MD'
            ## Revue de semaine

            ### Ce qui s'est bien passé

            - [x] Sprint terminé avec 0 ticket en retard
            - [x] Nouvelle fonctionnalité d'upload d'images déployée
            - [x] Séance de sport × 3

            ### Ce qui aurait pu mieux se passer

            - Trop de réunions non nécessaires (4h sur la semaine)
            - Documentation négligée → à rattraper

            ### Objectifs semaine prochaine

            - [ ] Documenter l'API NoteImageService
            - [ ] Commencer [[Mobile App Idea]]
            - [ ] Lire 30 min/jour minimum

            > [!tip] Méthode
            > Revoir ces weekly reviews chaque vendredi soir, 15 min max.
            MD,
        ]);
    }

    // ── Reference ────────────────────────────────────────────────────────────

    private function createReference(User $user): void
    {
        $ref = $this->note($user, [
            'position' => 4,
            'title' => 'Reference',
            'tags' => ['reference', 'cheatsheet'],
            'content' => <<<'MD'
            ## Callouts disponibles

            | Type | Alias | Couleur |
            | --- | --- | --- |
            | `note` | — | Indigo |
            | `info` | `abstract`, `summary` | Sky |
            | `tip` | `hint` | Emerald |
            | `success` | — | Vert |
            | `warning` | `caution` | Amber |
            | `danger` | — | Rouge |
            | `bug` | — | Orange |
            | `example` | — | Violet |
            | `question` | `faq` | Jaune |
            | `quote` | `cite` | Gris |
            | `todo` | — | Indigo |
            | `failure` | `fail`, `missing` | Rouge |

            ## Raccourcis clavier

            | Raccourci | Action |
            | --- | --- |
            | `Ctrl+S` | Sauvegarder |
            | `Ctrl+B` | Gras |
            | `Ctrl+I` | Italique |
            | `/` | Ouvrir la palette slash |
            | `[[` | Insérer un wiki-link |

            ## Syntaxe Markdown

            ```markdown
            # H1  ## H2  ### H3
            **gras**  *italique*  ~~barré~~  `code`
            - liste  1. numérotée  - [ ] checkbox
            > citation
            ---
            [[NomNote]]   → lien wiki
            > [!tip] Callout
            > Contenu du callout
            ```

            Voir [[Start here]] pour l'introduction complète.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $ref->id,
            'position' => 0,
            'title' => 'Vue 3 Notes',
            'tags' => ['vue', 'javascript', 'frontend'],
            'content' => <<<'MD'
            ## Composition API — bases

            ```javascript
            import { ref, computed, watch, onMounted } from 'vue'

            const count = ref(0)
            const double = computed(() => count.value * 2)

            watch(count, (newVal) => {
                console.log('changed:', newVal)
            })
            ```

            ## Composable pattern

            ```javascript
            export function useCounter(initial = 0) {
                const count = ref(initial)
                const increment = () => count.value++
                return { count, increment }
            }
            ```

            ## `<script setup>`

            ```vue
            <script setup>
            import { ref } from 'vue'
            const msg = ref('Hello')
            </script>

            <template><p>{{ msg }}</p></template>
            ```

            > [!tip] `defineProps` vs `withDefaults`
            > Préférer `withDefaults(defineProps<Props>(), {...})` pour les valeurs par défaut typées.

            Voir : [[Onyx Development]] pour l'usage en production.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $ref->id,
            'position' => 1,
            'title' => 'Laravel Tips',
            'tags' => ['laravel', 'php', 'backend'],
            'content' => <<<'MD'
            ## Eloquent — astuces

            ```php
            // Eager loading conditionnel
            $notes = Note::when($search, fn($q) => $q->where('title', 'like', "%$search%"))
                ->with('user')
                ->paginate(20);

            // whereHas pour filtrer via relation
            User::whereHas('roles', fn($q) => $q->where('name', 'ROLE_DEV'))->get();
            ```

            ## Chiffrement Eloquent

            ```php
            protected function casts(): array
            {
                return ['secret' => 'encrypted'];
            }
            ```

            > [!danger] Rotation de clé APP_KEY
            > Changer `APP_KEY` invalide **toutes** les données chiffrées. Ne jamais le faire en prod sans migration préalable.

            > [!warning] N+1
            > Toujours vérifier les relations avec `DB::listen()` ou Laravel Debugbar.

            ## Commandes utiles

            | Commande | Usage |
            | --- | --- |
            | `php artisan make:model Foo -mfc` | Modèle + migration + factory + controller |
            | `php artisan tinker` | REPL interactif |
            | `php artisan route:list` | Liste des routes |
            | `php artisan queue:work --tries=3` | Worker avec retry |
            MD,
        ]);
    }

    // ── Cuisine ──────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createCuisine(User $user, array $imgs): void
    {
        $cuisine = $this->note($user, [
            'position' => 5,
            'title' => 'Cuisine',
            'tags' => ['cuisine', 'recettes'],
            'content' => sprintf(<<<'MD'
            Mes recettes favorites et notes de cuisine.

            ![Cuisine](%s)

            - [[Risotto aux champignons]] — classique réconfortant
            - [[Curry de pois chiches]] — rapide et végétarien
            - [[Tarte tatin]] — dessert signature

            > [!tip] Organisation
            > Une note = une recette. Tags : `rapide`, `végétarien`, `dessert`, `batch-cooking`.
            MD, $this->imgUrl($imgs['cuisine'])),
        ]);

        $this->note($user, [
            'parent_id' => $cuisine->id,
            'position' => 0,
            'title' => 'Risotto aux champignons',
            'tags' => ['recette', 'végétarien', 'réconfort'],
            'content' => <<<'MD'
            **Temps :** 35 min · **Portions :** 2

            ## Ingrédients

            | Quantité | Ingrédient |
            | --- | --- |
            | 200 g | Riz arborio |
            | 250 g | Champignons de Paris |
            | 1 | Échalote |
            | 80 cl | Bouillon de légumes chaud |
            | 10 cl | Vin blanc sec |
            | 40 g | Parmesan râpé |
            | 2 c.s. | Huile d'olive |
            | | Sel, poivre, persil |

            ## Préparation

            1. Faire revenir l'échalote émincée dans l'huile d'olive à feu moyen.
            2. Ajouter les champignons tranchés, cuire 5 min jusqu'à évaporation.
            3. Ajouter le riz, nacrer 2 min en remuant.
            4. Déglacer au vin blanc, mélanger jusqu'à absorption.
            5. Ajouter le bouillon louche par louche en remuant constamment.
            6. Après ~18 min, hors du feu : incorporer le parmesan, ajuster l'assaisonnement.

            > [!tip] Le secret
            > Le bouillon doit être **chaud** — un bouillon froid casse la cuisson du riz et donne un résultat collant.

            > [!success] Variante
            > Remplacer les champignons de Paris par des cèpes séchés (réhydratés 20 min) pour une version plus intense.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $cuisine->id,
            'position' => 1,
            'title' => 'Curry de pois chiches',
            'tags' => ['recette', 'végétarien', 'rapide', 'batch-cooking'],
            'content' => <<<'MD'
            **Temps :** 25 min · **Portions :** 4 · **Se conserve 4 jours au frigo**

            ## Ingrédients

            | Quantité | Ingrédient |
            | --- | --- |
            | 2 boîtes | Pois chiches (400 g chacune) |
            | 1 boîte | Tomates concassées |
            | 20 cl | Lait de coco |
            | 1 | Oignon |
            | 3 gousses | Ail |
            | 2 c.c. | Curry en poudre |
            | 1 c.c. | Cumin |
            | 1 c.c. | Curcuma |
            | 1 c.s. | Huile de coco |
            | | Sel, coriandre fraîche |

            ## Préparation

            1. Faire revenir l'oignon et l'ail dans l'huile de coco.
            2. Ajouter les épices, cuire 1 min.
            3. Ajouter les tomates, lait de coco et pois chiches égouttés.
            4. Laisser mijoter 15 min à feu moyen.
            5. Servir avec du riz basmati, garnir de coriandre.

            > [!example] Batch cooking
            > Doubler les quantités, congeler en portions. Se réchauffe parfaitement.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $cuisine->id,
            'position' => 2,
            'title' => 'Tarte tatin',
            'tags' => ['recette', 'dessert'],
            'content' => <<<'MD'
            **Temps :** 1h · **Portions :** 6

            ## Ingrédients

            - 1 pâte feuilletée
            - 6 pommes (Golden ou Reine des Reinettes)
            - 120 g sucre
            - 60 g beurre
            - 1 pincée de fleur de sel

            ## Préparation

            1. Faire un caramel à sec avec le sucre dans un moule à tarte allant au four.
            2. Hors du feu, ajouter le beurre en morceaux et la fleur de sel.
            3. Disposer les pommes pelées et coupées en quartiers serrés sur le caramel.
            4. Couvrir de la pâte feuilletée en rentrant les bords.
            5. Cuire 30 min à 190°C.
            6. Laisser tiédir 10 min avant de retourner.

            > [!warning] Le retournement
            > Ne pas attendre trop longtemps pour retourner — le caramel fige et la tarte colle au moule.

            > [!tip] Service
            > Servir tiède avec une boule de glace vanille ou une cuillère de crème fraîche épaisse.
            MD,
        ]);
    }

    // ── Livres ────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createLivres(User $user, array $imgs): void
    {
        $livres = $this->note($user, [
            'position' => 6,
            'title' => 'Livres lus',
            'tags' => ['livres', 'lecture'],
            'content' => sprintf(<<<'MD'
            Fiches de lecture — une note par livre.

            ![Livres](%s)

            | Titre | Auteur | Note |
            | --- | --- | --- |
            | [[A Philosophy of Software Design]] | John Ousterhout | ⭐⭐⭐⭐⭐ |
            | [[Deep Work]] | Cal Newport | ⭐⭐⭐⭐ |
            | [[The Pragmatic Programmer]] | Hunt & Thomas | ⭐⭐⭐⭐⭐ |

            > [!info] Méthode de lecture
            > Pendant la lecture : surligner + noter les idées clés. Après : fiche de synthèse dans Onyx.
            MD, $this->imgUrl($imgs['livre'])),
        ]);

        $this->note($user, [
            'parent_id' => $livres->id,
            'position' => 0,
            'title' => 'A Philosophy of Software Design',
            'tags' => ['livre', 'dev', 'architecture'],
            'content' => <<<'MD'
            **Auteur :** John Ousterhout · **Note :** ⭐⭐⭐⭐⭐

            ## Thèse centrale

            La complexité est l'ennemi principal du développeur. Tout le design logiciel est une lutte contre la complexité.

            ## Concepts clés

            ### Deep modules
            Un bon module a une interface simple et une implémentation riche. Un module "shallow" expose presque autant de complexité qu'il en résout.

            ### Tactical vs Strategic programming
            - **Tactical** : faire marcher le code le plus vite possible → dette technique rapide
            - **Strategic** : investir du temps pour un bon design → plus lent au début, bien plus rapide ensuite

            ### Define errors out of existence
            > [!quote]
            > "The best way to reduce the complexity damage done by exception handling is to reduce the number of places where exceptions have to be handled."

            ## Citations importantes

            > "Complexity is anything related to the structure of a software system that makes it hard to understand and modify."

            > "Working code isn't enough. The primary goal must be to produce a great design."

            ## Application personnelle

            - [x] Revoir l'interface de `NoteImageService` à la lumière de ce livre
            - [ ] Appliquer le principe "deep modules" sur le prochain refacto
            - [ ] Partager avec l'équipe

            Voir [[Journal]] entrée 2026-04-15.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $livres->id,
            'position' => 1,
            'title' => 'Deep Work',
            'tags' => ['livre', 'productivité', 'focus'],
            'content' => <<<'MD'
            **Auteur :** Cal Newport · **Note :** ⭐⭐⭐⭐

            ## Thèse

            La capacité à se concentrer sans distraction sur une tâche cognitivement exigeante est de plus en plus rare et de plus en plus précieuse.

            ## Règles du Deep Work

            1. **Travailler en profondeur** — rituels, lieu dédié, durée définie
            2. **Embrasser l'ennui** — résister à l'envie de consulter son téléphone dès qu'on s'ennuie
            3. **Abandonner les réseaux sociaux** — évaluer chaque outil par ses bénéfices réels
            4. **Vider les fonds** — éliminer les tâches superficielles

            ## Ce que j'applique

            - [x] Blocs de 2h sans notifications le matin
            - [x] Téléphone en mode "ne pas déranger" pendant le travail
            - [ ] Journée sans réunions le mercredi
            - [ ] Définir une "shutdown routine" le soir

            > [!tip] Citation clé
            > "Clarity about what matters provides clarity about what does not."
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $livres->id,
            'position' => 2,
            'title' => 'The Pragmatic Programmer',
            'tags' => ['livre', 'dev', 'craft'],
            'content' => <<<'MD'
            **Auteurs :** Andrew Hunt & David Thomas · **Note :** ⭐⭐⭐⭐⭐

            ## Principes retenus

            ### DRY — Don't Repeat Yourself
            > "Every piece of knowledge must have a single, unambiguous, authoritative representation within a system."

            ### Orthogonality
            Deux composants sont orthogonaux s'ils peuvent changer indépendamment. Favorise la testabilité et la maintenabilité.

            ### Tracer Bullets
            Construire un squelette end-to-end fonctionnel avant d'étoffer — s'assure que tous les composants s'assemblent.

            ### The Broken Window Theory
            Ne jamais laisser un "mauvais code" en place. Un seul window brisée suffit à faire baisser les standards de toute une équipe.

            ## Tips favoris

            | # | Tip |
            | --- | --- |
            | 1 | Care About Your Craft |
            | 4 | Don't Live with Broken Windows |
            | 11 | DRY — Don't Repeat Yourself |
            | 17 | Program Close to the Problem Domain |
            | 36 | You Can't Write Perfect Software |

            > [!success] Toujours d'actualité
            > Écrit en 1999, mais chaque conseil reste parfaitement pertinent aujourd'hui.
            MD,
        ]);
    }

    // ── Voyages ───────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createVoyages(User $user, array $imgs): void
    {
        $voyages = $this->note($user, [
            'position' => 7,
            'title' => 'Voyages',
            'tags' => ['voyages', 'travel'],
            'content' => sprintf(<<<'MD'
            Notes de voyage — itinéraires, lieux, conseils.

            ![Voyage](%s)

            | Destination | Date | Statut |
            | --- | --- | --- |
            | [[Lisbonne]] | Avril 2025 | ✅ Fait |
            | [[Tokyo]] | — | 🗓️ Prévu 2027 |
            MD, $this->imgUrl($imgs['voyage'])),
        ]);

        $this->note($user, [
            'parent_id' => $voyages->id,
            'position' => 0,
            'title' => 'Lisbonne',
            'tags' => ['voyage', 'portugal', 'europe'],
            'content' => sprintf(<<<'MD'
            **Dates :** 3-7 avril 2025 · **Avec :** Sofia

            ![Lisbonne](%s)

            ## Quartiers visités

            - **Alfama** — le plus ancien, ruelles pavées, Fado le soir
            - **Belém** — Torre de Belém, Mosteiro dos Jerónimos, pastéis de Belém
            - **LX Factory** — marché du dimanche, street art, brunch
            - **Mouraria** — authentique, moins touristique, street food

            ## Adresses coups de cœur

            | Type | Nom | Note |
            | --- | --- | --- |
            | Restaurant | Taberna da Rua das Flores | ⭐⭐⭐⭐⭐ |
            | Café | Café A Brasileira | ⭐⭐⭐⭐ |
            | Pâtisserie | Pastéis de Belém (original) | ⭐⭐⭐⭐⭐ |
            | Bar | Park Bar (rooftop) | ⭐⭐⭐⭐ |

            ## Transports

            - Tram 28 : iconique mais bondé — y aller tôt le matin
            - Métro : efficace, couvre bien le centre
            - Tuk-tuk : à éviter (cher et touristique)

            ## Ce que je referais

            - [x] Prendre le ferry pour Cacilhas (vue magnifique + moins de monde)
            - [x] Journée à Sintra (palais de Pena = incontournable)
            - [ ] Aller jusqu'à Setúbal la prochaine fois

            > [!tip] Logement
            > Privilégier Príncipe Real ou Mouraria pour être au calme tout en étant central.
            MD, $this->imgUrl($imgs['voyage'])),
        ]);

        $this->note($user, [
            'parent_id' => $voyages->id,
            'position' => 1,
            'title' => 'Tokyo',
            'tags' => ['voyage', 'japon', 'planification'],
            'content' => <<<'MD'
            **Statut :** En cours de planification · **Cible :** Printemps 2027 (sakura)

            ## Durée envisagée

            3 semaines — Tokyo (1 semaine) + Kyoto/Nara (1 semaine) + Osaka/Hiroshima (1 semaine)

            ## Liste de souhaits

            - [ ] Tsukiji outer market (petit-déj sushi)
            - [ ] Shinjuku Gyoen pendant les sakura
            - [ ] Akihabara
            - [ ] Teamlab Borderless (réservation longtemps à l'avance)
            - [ ] Fushimi Inari (tôt le matin pour éviter la foule)
            - [ ] Nishiki Market à Kyoto
            - [ ] Château d'Osaka

            ## Budget estimé

            | Poste | Estimation |
            | --- | --- |
            | Vol A/R | ~900 € |
            | Hébergement (21 nuits) | ~1 500 € |
            | JR Pass (21 jours) | ~600 € |
            | Nourriture | ~800 € |
            | Activités | ~400 € |
            | **Total** | **~4 200 €** |

            > [!info] JR Pass
            > À acheter **avant** de partir — ne peut plus s'acheter sur place depuis 2023. Couvre Shinkansen + trains locaux JR.

            > [!question] Saison
            > Sakura = mi-mars à mi-avril selon les années. Vérifier les prévisions 6 semaines avant.
            MD,
        ]);
    }

    // ── Sport ─────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, string>  $imgs
     */
    private function createSport(User $user, array $imgs): void
    {
        $sport = $this->note($user, [
            'position' => 8,
            'title' => 'Sport',
            'tags' => ['sport', 'santé'],
            'content' => sprintf(<<<'MD'
            Suivi sportif et programmes d'entraînement.

            ![Sport](%s)

            - [[Programme musculation]] — 3 séances/semaine
            - [[Running]] — suivi des sorties

            > [!info] Objectif 2026
            > 10 km en moins de 50 min d'ici septembre.
            MD, $this->imgUrl($imgs['sport'])),
        ]);

        $this->note($user, [
            'parent_id' => $sport->id,
            'position' => 0,
            'title' => 'Programme musculation',
            'tags' => ['sport', 'musculation', 'programme'],
            'content' => <<<'MD'
            **Fréquence :** 3 séances/semaine · **Programme :** Push / Pull / Legs

            ## Séance A — Push (lundi)

            | Exercice | Séries × Reps | Charge |
            | --- | --- | --- |
            | Développé couché | 4 × 8 | 70 kg |
            | Développé incliné haltères | 3 × 10 | 26 kg |
            | Élévations latérales | 3 × 15 | 10 kg |
            | Triceps poulie | 3 × 12 | 25 kg |

            ## Séance B — Pull (mercredi)

            | Exercice | Séries × Reps | Charge |
            | --- | --- | --- |
            | Tractions | 4 × 6 | Poids du corps |
            | Rowing barre | 4 × 8 | 60 kg |
            | Curl biceps | 3 × 12 | 16 kg |
            | Face pulls | 3 × 15 | 20 kg |

            ## Séance C — Legs (vendredi)

            | Exercice | Séries × Reps | Charge |
            | --- | --- | --- |
            | Squat | 4 × 6 | 90 kg |
            | Leg press | 3 × 12 | 150 kg |
            | Fentes marchées | 3 × 10 | 20 kg haltères |
            | Mollets debout | 4 × 15 | 60 kg |

            > [!tip] Progression
            > Augmenter de 2,5 kg dès que les 4 séries sont complétées proprement.

            > [!warning] Récupération
            > Au moins 48h entre deux séances. Sommeil ≥ 7h. Protéines ≥ 1,8 g/kg/jour.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $sport->id,
            'position' => 1,
            'title' => 'Running',
            'tags' => ['sport', 'running', 'suivi'],
            'content' => <<<'MD'
            ## Objectif

            10 km en moins de 50 min avant fin septembre 2026.

            **Record actuel :** 54:12 (3 avril 2026)

            ## Sorties récentes

            | Date | Distance | Temps | Allure |
            | --- | --- | --- | --- |
            | 2026-04-19 | 8 km | 44:30 | 5:34/km |
            | 2026-04-14 | 6 km | 32:10 | 5:22/km |
            | 2026-04-08 | 10 km | 54:12 | 5:25/km |
            | 2026-03-31 | 7 km | 39:05 | 5:35/km |

            ## Plan progression

            | Semaine | Type | Objectif |
            | --- | --- | --- |
            | S1-S4 | Endurance | 3 × 6-8 km à allure confort |
            | S5-S8 | Seuil | 1 séance fractionné 5×800m/semaine |
            | S9-S12 | Spécifique | 1 sortie longue 12 km/semaine |
            | S13 | Récup | Réduction volume -30% |
            | S14 | Course | 🎯 10 km objectif |

            > [!success] Progression
            > -4 min sur le 10 km en 6 semaines. Objectif atteignable si la progression continue.

            > [!tip] Fractionné
            > 5 × 800m avec 90s de récup — courir les 800m à allure cible 10 km (5:00/km).
            MD,
        ]);
    }

    // ── Finance ───────────────────────────────────────────────────────────────

    private function createFinance(User $user): void
    {
        $finance = $this->note($user, [
            'position' => 9,
            'title' => 'Finance',
            'tags' => ['finance', 'budget', 'épargne'],
            'content' => <<<'MD'
            Notes de gestion financière personnelle.

            - [[Budget mensuel]] — suivi des dépenses
            - [[Objectifs épargne]] — projets et horizons

            > [!info] Outil principal
            > Suivi quotidien sur **Spendly** — ces notes servent à la réflexion et à la stratégie.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $finance->id,
            'position' => 0,
            'title' => 'Budget mensuel',
            'tags' => ['finance', 'budget'],
            'content' => <<<'MD'
            ## Structure du budget (mensuel)

            | Poste | Budget | Réel avril | Écart |
            | --- | --- | --- | --- |
            | Loyer + charges | 900 € | 900 € | 0 € |
            | Alimentation | 300 € | 285 € | +15 € |
            | Transport | 80 € | 75 € | +5 € |
            | Loisirs | 150 € | 180 € | -30 € |
            | Abonnements | 60 € | 58 € | +2 € |
            | Épargne | 400 € | 400 € | 0 € |
            | Divers | 100 € | 65 € | +35 € |
            | **Total** | **1 990 €** | **1 963 €** | **+27 €** |

            ## Répartition épargne

            ```
            Épargne mensuelle : 400 €
            ├── Livret A (précaution)     : 100 €/mois
            ├── Assurance-vie (long terme) : 200 €/mois
            └── Projet voyage Tokyo        : 100 €/mois
            ```

            > [!success] Avril
            > Mois positif — loisirs légèrement dépassés (restaurant impromptu) mais compensé sur les autres postes.

            > [!tip] Règle des 50/30/20
            > 50% besoins · 30% envies · 20% épargne. Actuellement à environ 47/33/20 — dans les clous.
            MD,
        ]);

        $this->note($user, [
            'parent_id' => $finance->id,
            'position' => 1,
            'title' => 'Objectifs épargne',
            'tags' => ['finance', 'épargne', 'objectifs'],
            'content' => <<<'MD'
            ## Objectifs en cours

            ### 🏦 Fonds d'urgence (6 mois de dépenses)
            **Cible :** 12 000 € · **Actuel :** 9 800 € · **Progression :** 82%

            - [x] Atteindre 5 000 €
            - [x] Atteindre 8 000 €
            - [ ] Atteindre 12 000 € (objectif final)

            ### ✈️ Voyage Tokyo 2027
            **Cible :** 4 200 € · **Actuel :** 1 400 € · **Progression :** 33%

            À raison de 100 €/mois → atteint en avril 2027. ✅

            ### 📈 Investissement long terme
            **Horizon :** 10 ans · **Véhicule :** Assurance-vie UC

            - Versement mensuel : 200 €
            - Allocation cible : 80% actions (ETF monde) / 20% obligations

            > [!info] Horizon long terme
            > Ne pas regarder les performances à court terme. Vérifier 1x/an maximum.

            ## Règles personnelles

            1. Épargner **avant** de dépenser — virement automatique le 1er du mois
            2. Tout "extra" (prime, remboursement) → 50% épargne, 50% plaisir
            3. Jamais toucher au fonds d'urgence sauf vraie urgence

            > [!quote] Warren Buffett
            > "Do not save what is left after spending, but spend what is left after saving."
            MD,
        ]);
    }

    // ── Ideas ─────────────────────────────────────────────────────────────────

    private function createIdeas(User $user): void
    {
        $this->note($user, [
            'position' => 10,
            'title' => 'Ideas',
            'tags' => ['idées', 'brainstorm'],
            'content' => <<<'MD'
            Bloc-notes libre pour les idées en vrac.

            ---

            ## App ideas

            - **Onyx Mobile** — voir [[Mobile App Idea]]
            - **Onyx Share** — partage de notes en lecture seule via lien public
            - **Onyx API** — API REST pour intégrations tierces (Zapier, Make…)

            ## Features à explorer

            > [!example] Graph amélioré
            > Filtrer le graphe par tag ou par profondeur de connexion. Afficher le poids des liens (nombre de mentions).

            > [!question] IA dans les notes ?
            > Résumé automatique, suggestions de liens, complétion de texte. À peser : vie privée vs utilité.

            > [!bug] Connu
            > Sur Safari iOS, le curseur dans le textarea saute parfois à la fin lors d'une insertion via slash command. À investiguer.

            ---

            *Voir aussi : [[Journal]] · [[Onyx Development]]*
            MD,
        ]);
    }
}
