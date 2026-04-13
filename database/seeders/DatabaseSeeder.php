<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ────────────────────────────────────────────────────────────
        $roleDev  = Role::firstOrCreate(['name' => 'ROLE_DEV',  'guard_name' => 'web']);
        $roleUser = Role::firstOrCreate(['name' => 'ROLE_USER', 'guard_name' => 'web']);

        // ── Application parameters ────────────────────────────────────────────
        Artisan::call('onyx:application-parameter');

        // ── Main user (axel) ─────────────────────────────────────────────────
        $axel = User::firstOrCreate(
            ['email' => 'axel.raboit@gmail.com'],
            [
                'name'              => 'Axel',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'locale'            => 'fr',
            ]
        );
        $axel->syncRoles([$roleDev]);

        // ── Regular users ─────────────────────────────────────────────────────
        $alice = User::firstOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name'              => 'Alice Martin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $alice->syncRoles([$roleUser]);

        $bob = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name'              => 'Bob Dupont',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $bob->syncRoles([$roleUser]);

        // Unverified user
        User::firstOrCreate(
            ['email' => 'charlie@example.com'],
            [
                'name'              => 'Charlie',
                'password'          => Hash::make('password'),
                'email_verified_at' => null,
            ]
        );

        // ── Notes for axel ────────────────────────────────────────────────────
        $this->seedAxelNotes($axel);

        // ── A few notes for alice ─────────────────────────────────────────────
        Note::factory()->count(3)->create(['user_id' => $alice->id]);

        $this->command->info('✅ Fixtures loaded — axel.raboit@gmail.com / password');
    }

    private function seedAxelNotes(User $user): void
    {
        // ── Index ─────────────────────────────────────────────────────────────
        $index = Note::create([
            'user_id'  => $user->id,
            'position' => 0,
            'title'    => 'Start here',
            'tags'     => ['index', 'home'],
            'content'  => <<<'MD'
            Welcome to **Onyx** — your personal note-taking space.

            ## Navigation

            - [[Projects]] — en cours et idées
            - [[Learning]] — notes techniques
            - [[Journal]] — notes quotidiennes
            - [[Reference]] — cheat sheets et ressources
            - [[Ideas]] — brainstorm libre

            > [!tip] Raccourci
            > Tape `/` en début de ligne pour insérer un bloc, ou `[[` pour lier une note.

            ---

            > [!info] À propos d'Onyx
            > Onyx supporte le Markdown complet, les liens wiki `[[note]]`, les callouts, les blocs de code avec coloration syntaxique et les cases à cocher interactives.
            MD,
        ]);

        // ── Projects (parent) ─────────────────────────────────────────────────
        $projects = Note::create([
            'user_id'  => $user->id,
            'position' => 1,
            'title'    => 'Projects',
            'tags'     => ['projects'],
            'content'  => <<<'MD'
            Dossier de tous les projets actifs et archivés.

            | Projet | Statut | Priorité |
            | --- | --- | --- |
            | [[Onyx Development]] | 🟢 Actif | Haute |
            | [[Mobile App Idea]] | 🟡 En pause | Moyenne |

            > [!warning] Revue hebdomadaire
            > Penser à mettre à jour les statuts chaque lundi.
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $projects->id,
            'position'  => 0,
            'title'     => 'Onyx Development',
            'tags'      => ['dev', 'onyx', 'laravel'],
            'content'   => <<<'MD'
            ## Objectif

            Construire une application de notes chiffrées, inspirée d'Obsidian, avec Laravel + Vue 3 + Inertia.

            ## Tâches en cours

            - [x] Structure de base (auth, migrations, modèles)
            - [x] Éditeur Markdown avec aperçu
            - [x] Wiki-links `[[note]]` et backlinks
            - [x] Callouts Obsidian-style
            - [x] Commandes slash `/`
            - [x] Graphe de notes
            - [x] Templates
            - [ ] Recherche full-text
            - [ ] Mode hors-ligne (PWA)
            - [ ] Partage de notes

            ## Architecture

            ```
            app/
            ├── Http/Controllers/NoteController.php
            ├── Models/Note.php          ← chiffrement title + content
            └── Services/
            ```

            > [!note] Chiffrement
            > Les champs `title` et `content` sont chiffrés via le cast `encrypted` de Laravel. La clé d'application est la seule façon de déchiffrer.

            ## Stack

            ```php
            // Note.php
            protected function casts(): array
            {
                return [
                    'title'   => 'encrypted',
                    'content' => 'encrypted',
                    'tags'    => 'array',
                ];
            }
            ```

            Voir aussi : [[Learning]] pour les notes techniques associées.
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $projects->id,
            'position'  => 1,
            'title'     => 'Mobile App Idea',
            'tags'      => ['idée', 'mobile'],
            'content'   => <<<'MD'
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
            > Regarder comment Obsidian Mobile gère le sync — leur approche vault local + plugin sync payant.

            En lien avec : [[Onyx Development]]
            MD,
        ]);

        // ── Learning (parent) ─────────────────────────────────────────────────
        $learning = Note::create([
            'user_id'  => $user->id,
            'position' => 2,
            'title'    => 'Learning',
            'tags'     => ['learning'],
            'content'  => <<<'MD'
            Index des notes techniques et d'apprentissage.

            - [[Vue 3 Notes]] — Composition API, composables, patterns
            - [[Laravel Tips]] — Eloquent, queues, artisan

            > [!abstract] Méthode
            > Une note = un concept. Relier les concepts avec des `[[wiki-links]]`.
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $learning->id,
            'position'  => 0,
            'title'     => 'Vue 3 Notes',
            'tags'      => ['vue', 'javascript', 'frontend'],
            'content'   => <<<'MD'
            ## Composition API — bases

            ```javascript
            import { ref, computed, watch, onMounted } from 'vue'

            const count = ref(0)
            const double = computed(() => count.value * 2)

            watch(count, (newVal) => {
              console.log('count changed:', newVal)
            })

            onMounted(() => {
              console.log('mounted')
            })
            ```

            ## Composable pattern

            ```javascript
            // useCounter.js
            export function useCounter(initial = 0) {
              const count = ref(initial)
              function increment() { count.value++ }
              return { count, increment }
            }
            ```

            ## `<script setup>` — syntaxe courte

            ```vue
            <script setup>
            import { ref } from 'vue'
            const msg = ref('Hello')
            </script>

            <template>
              <p>{{ msg }}</p>
            </template>
            ```

            > [!tip] `defineProps` vs `withDefaults`
            > Préférer `withDefaults(defineProps<Props>(), {...})` pour les valeurs par défaut typées.

            ## Provide / Inject

            ```javascript
            // parent
            provide('theme', readonly(theme))

            // child
            const theme = inject('theme')
            ```

            Voir aussi : [[Onyx Development]] pour l'usage en production.
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $learning->id,
            'position'  => 1,
            'title'     => 'Laravel Tips',
            'tags'      => ['laravel', 'php', 'backend'],
            'content'   => <<<'MD'
            ## Eloquent — astuces utiles

            ```php
            // Eager loading conditionnel
            $notes = Note::when($search, fn($q) => $q->where('title', 'like', "%$search%"))
                ->with('user')
                ->paginate(20);

            // whereHas vs with pour filtrer
            User::whereHas('roles', fn($q) => $q->where('name', 'ROLE_DEV'))->get();
            ```

            ## Queues — bonnes pratiques

            ```php
            // Toujours implémenter ShouldQueue sur les Notifications lentes
            class AppInvitationNotification extends Notification implements ShouldQueue
            {
                use Queueable;
            }
            ```

            > [!danger] Attention aux N+1
            > Toujours vérifier avec `DB::listen()` ou Laravel Debugbar que les relations sont bien eager-loadées.

            ## Artisan commands utiles

            | Commande | Usage |
            | --- | --- |
            | `php artisan make:model Foo -mfc` | Modèle + migration + factory + controller |
            | `php artisan tinker` | REPL interactif |
            | `php artisan route:list` | Liste des routes |
            | `php artisan queue:work --tries=3` | Worker avec retry |

            ## Chiffrement Eloquent

            ```php
            protected function casts(): array
            {
                return ['secret' => 'encrypted'];
            }
            ```

            > [!warning] Rotation de clé
            > Changer `APP_KEY` invalide **toutes** les données chiffrées. Ne jamais le faire en prod sans migration préalable.
            MD,
        ]);

        // ── Journal (parent) ──────────────────────────────────────────────────
        $journal = Note::create([
            'user_id'  => $user->id,
            'position' => 3,
            'title'    => 'Journal',
            'tags'     => ['journal'],
            'content'  => <<<'MD'
            Notes quotidiennes — une note par jour.

            > [!tip] Note du jour
            > Utilise le bouton **Note du jour** dans la barre latérale pour ouvrir ou créer automatiquement la note d'aujourd'hui.
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $journal->id,
            'position'  => 0,
            'title'     => '2026-04-13',
            'tags'      => ['journal', 'daily'],
            'content'   => <<<'MD'
            ## Aujourd'hui

            - [x] Mettre en place les fixtures Onyx
            - [x] Créer la page Guide avec toutes les fonctionnalités
            - [ ] Tester le responsive mobile
            - [ ] Review du code avec l'équipe

            ## Notes

            Bonne session de dev. L'éditeur de notes commence à ressembler à quelque chose de solide.

            Travaillé sur : [[Onyx Development]]

            > [!success] Fixture seed opérationnelle
            > Le seeder crée maintenant un jeu de données complet avec des notes hiérarchiques, des wiki-links, des callouts et du code.

            ## Demain

            - [ ] Implémenter la recherche full-text
            - [ ] Tester sur mobile Safari
            MD,
        ]);

        Note::create([
            'user_id'   => $user->id,
            'parent_id' => $journal->id,
            'position'  => 1,
            'title'     => '2026-04-12',
            'tags'      => ['journal', 'daily'],
            'content'   => <<<'MD'
            ## Aujourd'hui

            - [x] Setup initial du projet Onyx
            - [x] Migrations : notes, application_parameters
            - [x] Authentification et rôles Spatie
            - [x] Éditeur Markdown de base

            ## Découvertes

            > [!info] Marked.js extensions
            > Il est possible d'écrire des extensions Marked pour transformer la syntaxe Obsidian (`> [!type]`) en HTML avec les bonnes classes CSS. Beaucoup plus propre qu'un regex post-processing.

            ```javascript
            // Extension Marked pour les callouts
            const calloutExtension = {
              name: 'callout',
              level: 'block',
              start(src) { return src.indexOf('> [!') },
              // ...
            }
            ```

            Voir [[Vue 3 Notes]] pour les détails d'implémentation.
            MD,
        ]);

        // ── Reference ─────────────────────────────────────────────────────────
        Note::create([
            'user_id'  => $user->id,
            'position' => 4,
            'title'    => 'Reference',
            'tags'     => ['reference', 'cheatsheet'],
            'content'  => <<<'MD'
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
            [[Note liée]]  ![[Note intégrée]]
            > [!tip] Callout
            ```

            Voir le [[Start here|guide de démarrage]] pour une introduction complète.
            MD,
        ]);

        // ── Ideas ─────────────────────────────────────────────────────────────
        Note::create([
            'user_id'  => $user->id,
            'position' => 5,
            'title'    => 'Ideas',
            'tags'     => ['idées', 'brainstorm'],
            'content'  => <<<'MD'
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

            *Dernière mise à jour : voir [[2026-04-13]]*
            MD,
        ]);
    }
}
