<script setup>
import '@css/notes/callouts.css';
import '@css/notes/checkboxes.css';
import '@css/notes/code-blocks.css';
import '@css/notes/wiki-links.css';
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppPageHeader from '@/components/ui/AppPageHeader.vue';
import { createCalloutExtension } from '@/composables/notes/markedCallouts';
import { createCheckboxRenderer, resetCheckboxCounter } from '@/composables/notes/markedCheckboxes';
import { createHighlightRenderer } from '@/composables/notes/markedHighlight';
import {
    Hash,
    Bold,
    List,
    Link2,
    Quote,
    FileCode,
    Table,
    Layers,
    Slash,
    Navigation,
    Code,
    BookOpen,
    Eye,
    ImageIcon,
} from 'lucide-vue-next';

// ── Marked setup (read-only — no wiki-link autocomplete, no embeds) ──────────
marked.use({
    extensions: [createCalloutExtension()],
    renderer: {
        ...createCheckboxRenderer(),
        ...createHighlightRenderer(),
    },
});
marked.setOptions({ breaks: true, gfm: true });

const PURIFY_CONFIG = {
    ADD_TAGS: ['svg', 'path', 'circle'],
    ADD_ATTR: ['data-checkbox-index', 'viewBox', 'fill', 'stroke', 'stroke-width', 'd', 'cx', 'cy', 'r'],
};

const { t } = useI18n();

// ── Tabs ──────────────────────────────────────────────────────────────────────
const activeTab = ref('lexicon');

// ── Demo notes ────────────────────────────────────────────────────────────────
const DEMO_NOTES = [
    {
        id: 1,
        title: 'Bienvenue dans Onyx',
        tags: ['guide'],
        date: '13 avr. 2025',
        content: `# Bienvenue dans Onyx

Lorem ipsum dolor sit amet, **consectetur adipiscing** elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.

## Lorem ipsum

- [[Fonctionnalités de l'éditeur]] — lorem ipsum dolor sit amet
- [[Projet Atlas]] — consectetur adipiscing elit sed do eiusmod
- [[Stack technique]] — tempor incididunt ut labore et dolore

## Dolor sit amet

*Lorem ipsum* dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud exercitation ullamco. Les backlinks sont automatiquement calculés — cette note apparaît dans les backlinks de [[Projet Atlas]].

On peut aussi donner un alias : \`[[Projet Atlas|voir le projet]]\`.

![Aperçu de l'interface|600](/demo/demo-preview.jpg)

---

> [!tip] Lorem ipsum
> Cliquez sur un lien wiki pour naviguer entre les notes de cette démo.

> [!info] Dolor sit amet
> Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.`,
    },
    {
        id: 2,
        title: "Fonctionnalités de l'éditeur",
        tags: ['guide', 'markdown'],
        date: '12 avr. 2025',
        content: `# Fonctionnalités de l'éditeur

← Retour vers [[Bienvenue dans Onyx]]

## Mise en forme

Voici du texte **gras**, de l'*italique*, du ~~barré~~ et du \`code inline\`.
On peut combiner : ***gras et italique***, ou **\`gras avec code\`**.

## Listes

### À puces
- Lorem ipsum dolor sit amet
- Consectetur **adipiscing** elit
- Sed do eiusmod tempor

### Numérotée
1. Lorem ipsum dolor
2. Consectetur adipiscing
3. Sed do eiusmod

### Cases à cocher
- [x] Lorem ipsum dolor sit amet
- [x] Consectetur adipiscing elit
- [x] Sed do eiusmod tempor
- [ ] Incididunt ut labore

## Tableau

| Lorem | Ipsum | Dolor |
| --- | --- | --- |
| Sit amet | ✅ Consectetur | Haute |
| Adipiscing | ✅ Elit sed | Moyenne |
| Do eiusmod | ✅ Tempor | Moyenne |
| Incididunt | ⏳ Labore | Haute |

---

## Callouts (12 types)

> [!note] Lorem ipsum
> Dolor sit amet consectetur adipiscing elit sed do eiusmod tempor.

> [!tip] Consectetur
> Lorem ipsum dolor sit amet, consectetur adipiscing elit.

> [!warning] Adipiscing
> Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.

> [!danger] Dolor
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod.

> [!success] Sit amet
> Lorem ipsum dolor : **117/117** consectetur adipiscing elit.

> [!bug] Eiusmod
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod.

> [!question] Tempor
> Lorem ipsum dolor sit amet — consectetur adipiscing elit sed do eiusmod ?

> [!example] Exemple
> Voir la section **Bloc de code** de [[Stack technique]] pour un exemple concret.

> [!info] Info
> Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.

> [!quote] Lorem
> "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
> — Cicéron (adapté)

> [!todo] À faire
> - [ ] Lorem ipsum dolor sit amet
> - [ ] Consectetur adipiscing elit
> - [x] Sed do eiusmod tempor

> [!failure] Incididunt
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor.`,
    },
    {
        id: 3,
        title: 'Projet Atlas',
        tags: ['projet'],
        date: '11 avr. 2025',
        content: `# Projet Atlas

Lorem ipsum dolor sit amet consectetur adipiscing elit. Voir [[Réunion du 10 avril]] pour le dernier compte-rendu et [[Stack technique]] pour les choix technologiques.

## Avancement

- [x] Lorem ipsum dolor sit amet
- [x] Consectetur adipiscing elit
- [x] Sed do eiusmod tempor
- [ ] Incididunt ut labore
- [ ] Dolore magna aliqua
- [ ] Ut enim ad minim

## Équipe

| Membre | Rôle |
| --- | --- |
| Alice | Lorem ipsum |
| Bob | Dolor sit amet |
| Charlie | Consectetur elit |

---

> [!todo] Cette semaine
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor.

> [!warning] Point de vigilance
> Consectetur adipiscing — sed do eiusmod tempor incididunt ut labore et dolore.

> [!danger] Blocage
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor.`,
    },
    {
        id: 4,
        title: 'Réunion du 10 avril',
        tags: ['projet', 'réunion'],
        date: '10 avr. 2025',
        content: `# Réunion du 10 avril

Lorem ipsum dolor sit amet consectetur adipiscing — [[Projet Atlas]].

## Participants

- Alice (lorem ipsum)
- Bob (dolor sit)
- Charlie (amet)

## Points abordés

1. Lorem ipsum dolor sit amet consectetur
2. Adipiscing elit sed do eiusmod tempor
3. Incididunt ut labore et [[Stack technique]]

---

## Décisions

> [!note] Lorem ipsum
> Dolor sit amet consectetur adipiscing elit. Voir [[Stack technique]] pour le détail.

> [!warning] Consectetur
> Lorem ipsum — sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.

## Actions

- [ ] Alice : lorem ipsum dolor sit amet
- [ ] Bob : consectetur adipiscing elit lundi
- [x] Charlie : sed do eiusmod tempor

## Citation

> "Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor."`,
    },
    {
        id: 5,
        title: 'Stack technique',
        tags: ['dev', 'technique'],
        date: '9 avr. 2025',
        content: `# Stack technique

Lorem ipsum dolor sit amet pour [[Projet Atlas]]. Validées en [[Réunion du 10 avril]].

## Architecture

\`\`\`
Lorem ipsum  →  Dolor sit  →  Amet
                                ↓
                          Consectetur 4
\`\`\`

## Exemple — Lorem ipsum

\`\`\`javascript
export function useLoremIpsum(initial = 0) {
    const count = ref(initial)
    const double = computed(() => count.value * 2)
    function increment() { count.value++ }
    return { count, double, increment }
}
\`\`\`

## Choix structurants

| Couche | Choix | Raison |
| --- | --- | --- |
| Lorem | Ipsum 13 | Dolor sit amet |
| Consectetur | Adipiscing 3 | Sed do eiusmod |
| Tempor | Incididunt 4 | Ut labore et |
| Dolore | Magna aliqua | Ut enim ad |

---

> [!tip] Lorem ipsum
> Dolor sit amet consectetur adipiscing elit — sed do eiusmod tempor incididunt.

> [!bug] Connu
> Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor.

> [!success] Résultat
> Lorem ipsum validé lors de la [[Réunion du 10 avril]].`,
    },
];

const activeNoteId = ref(1);
const activeNote = computed(() => DEMO_NOTES.find((n) => n.id === activeNoteId.value) ?? DEMO_NOTES[0]);

function renderDemoNote(content) {
    resetCheckboxCounter();

    // Parse markdown first — marked v18 escapes raw HTML so wiki-links must be
    // replaced AFTER parsing, directly in the resulting HTML string.
    const parsed = marked.parse(content);

    const linked = parsed.replace(/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/g, (_, title, alias) => {
        const display      = (alias ?? title).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const escapedTitle = title.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const exists       = DEMO_NOTES.some((n) => n.title.toLowerCase() === title.trim().toLowerCase());
        return exists
            ? `<a class="wiki-link demo-wiki-link" data-demo-note="${escapedTitle}">${display}</a>`
            : `<span class="wiki-link wiki-link-missing">${display}</span>`;
    });

    return DOMPurify.sanitize(linked, {
        ...PURIFY_CONFIG,
        ADD_ATTR: [...(PURIFY_CONFIG.ADD_ATTR ?? []), 'data-demo-note'],
    });
}

const renderedActiveNote = computed(() => renderDemoNote(activeNote.value.content));

function handlePreviewClick(event) {
    const link = event.target.closest('.demo-wiki-link');
    if (!link) return;
    event.preventDefault();
    const title = link.dataset.demoNote;
    const target = DEMO_NOTES.find((n) => n.title.toLowerCase() === title.toLowerCase());
    if (target) activeNoteId.value = target.id;
}

// ── Lexicon data ──────────────────────────────────────────────────────────────
const calloutTypes = [
    { type: 'note',     icon: '📝', label: 'note',     aliases: [] },
    { type: 'info',     icon: 'ℹ️',  label: 'info',     aliases: ['abstract', 'summary'] },
    { type: 'tip',      icon: '💡', label: 'tip',      aliases: ['hint'] },
    { type: 'success',  icon: '✅', label: 'success',  aliases: [] },
    { type: 'warning',  icon: '⚠️', label: 'warning',  aliases: ['caution'] },
    { type: 'danger',   icon: '🔴', label: 'danger',   aliases: [] },
    { type: 'bug',      icon: '🐛', label: 'bug',      aliases: [] },
    { type: 'example',  icon: '📌', label: 'example',  aliases: [] },
    { type: 'question', icon: '❓', label: 'question', aliases: ['faq'] },
    { type: 'quote',    icon: '❝',  label: 'quote',    aliases: ['cite'] },
    { type: 'todo',     icon: '☐',  label: 'todo',     aliases: [] },
    { type: 'failure',  icon: '❌', label: 'failure',  aliases: ['fail', 'missing'] },
];

const slashCommands = [
    { icon: 'H1',  label: 'Heading 1',     syntax: '# '           },
    { icon: 'H2',  label: 'Heading 2',     syntax: '## '          },
    { icon: 'H3',  label: 'Heading 3',     syntax: '### '         },
    { icon: '•',   label: 'Bullet list',   syntax: '- '           },
    { icon: '1.',  label: 'Numbered list', syntax: '1. '          },
    { icon: '☐',   label: 'Checkbox',      syntax: '- [ ] '       },
    { icon: '❝',   label: 'Quote',         syntax: '> '           },
    { icon: '—',   label: 'Divider',       syntax: '---'          },
    { icon: '</>',  label: 'Code block',   syntax: '``` … ```'    },
    { icon: '!',   label: 'Callout',       syntax: '> [!info] …'  },
    { icon: '[[',  label: 'Wiki link',     syntax: '[[note]]'     },
    { icon: 'B',   label: 'Bold',          syntax: '**text**'     },
    { icon: 'I',   label: 'Italic',        syntax: '*text*'       },
    { icon: 'S̶',   label: 'Strikethrough', syntax: '~~text~~'     },
    { icon: '⊞',   label: 'Table',         syntax: '| Col | …'   },
];

const navItems = [
    { key: 'backlinks',        icon: '←'  },
    { key: 'unlinkedMentions', icon: '~'  },
    { key: 'outline',          icon: '≡'  },
    { key: 'graph',            icon: '◉'  },
    { key: 'templates',        icon: '⎘'  },
    { key: 'dailyNote',        icon: '📅' },
    { key: 'tags',             icon: '#'  },
    { key: 'search',           icon: '🔍' },
];

const shortcuts = [
    { key: 'Ctrl+S', descKey: 'save'     },
    { key: 'Ctrl+B', descKey: 'bold'     },
    { key: 'Ctrl+I', descKey: 'italic'   },
    { key: '/…',     descKey: 'slash'    },
    { key: '[[',     descKey: 'wikiLink' },
];
</script>

<template>
    <Head :title="t('guide.title')" />

    <AuthenticatedLayout>
        <template #header>
            <AppPageHeader :title="t('guide.title')" :subtitle="t('guide.subtitle')" />
        </template>

        <!-- ── Tab bar ──────────────────────────────────────────────────────── -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="flex items-center gap-1 bg-surface border border-base rounded-xl p-1 w-fit">
                <button
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="activeTab === 'lexicon'
                        ? 'bg-indigo-600/15 text-indigo-400'
                        : 'text-secondary hover:text-primary hover:bg-surface-2'"
                    v-on:click="activeTab = 'lexicon'"
                >
                    <BookOpen class="w-4 h-4" />
                    {{ t('guide.tabs.lexicon') }}
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="activeTab === 'preview'
                        ? 'bg-indigo-600/15 text-indigo-400'
                        : 'text-secondary hover:text-primary hover:bg-surface-2'"
                    v-on:click="activeTab = 'preview'"
                >
                    <Eye class="w-4 h-4" />
                    {{ t('guide.tabs.preview') }}
                </button>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════
             TAB : LEXICON
         ════════════════════════════════════════════════════════════════ -->
        <div v-if="activeTab === 'lexicon'" class="max-w-4xl mx-auto">
            <div class="space-y-10 pb-8">
                <!-- Text formatting -->
                <section>
                    <h2 class="section-title">
                        <Bold class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.formatting') }}
                    </h2>
                    <div class="card overflow-x-auto">
                        <table class="w-full text-sm min-w-[320px]">
                            <thead>
                                <tr class="border-b border-base bg-surface-2">
                                    <th class="th">{{ t('guide.col.syntax') }}</th>
                                    <th class="th">{{ t('guide.col.result') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base">
                                <tr>
                                    <td class="td"><code class="ci">**bold**</code></td>
                                    <td class="td font-bold text-primary">bold</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">*italic*</code></td>
                                    <td class="td italic text-primary">italic</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">~~strikethrough~~</code></td>
                                    <td class="td line-through text-secondary">strikethrough</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">`inline code`</code></td>
                                    <td class="td"><code class="ci">inline code</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Headings -->
                <section>
                    <h2 class="section-title">
                        <Hash class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.headings') }}
                    </h2>
                    <div class="card overflow-x-auto">
                        <table class="w-full text-sm min-w-[280px]">
                            <thead>
                                <tr class="border-b border-base bg-surface-2">
                                    <th class="th">{{ t('guide.col.syntax') }}</th>
                                    <th class="th">{{ t('guide.col.result') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base">
                                <tr>
                                    <td class="td"><code class="ci"># Heading 1</code></td>
                                    <td class="td text-2xl font-bold text-primary leading-tight">Heading 1</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">## Heading 2</code></td>
                                    <td class="td text-xl font-bold text-primary leading-tight">Heading 2</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">### Heading 3</code></td>
                                    <td class="td text-lg font-semibold text-primary leading-tight">Heading 3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Lists -->
                <section>
                    <h2 class="section-title">
                        <List class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.lists') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="card p-4 space-y-2">
                            <p class="label">{{ t('guide.listTypes.bullet') }}</p>
                            <pre class="code-pre">- Item A
- Item B
- Item C</pre>
                            <ul class="list-disc list-inside text-sm text-secondary space-y-0.5 pl-1">
                                <li>Item A</li>
                                <li>Item B</li>
                                <li>Item C</li>
                            </ul>
                        </div>
                        <div class="card p-4 space-y-2">
                            <p class="label">{{ t('guide.listTypes.numbered') }}</p>
                            <pre class="code-pre">1. First
2. Second
3. Third</pre>
                            <ol class="list-decimal list-inside text-sm text-secondary space-y-0.5 pl-1">
                                <li>First</li>
                                <li>Second</li>
                                <li>Third</li>
                            </ol>
                        </div>
                        <div class="card p-4 space-y-2">
                            <p class="label">{{ t('guide.listTypes.checkbox') }}</p>
                            <pre class="code-pre">- [ ] Todo
- [x] Done</pre>
                            <div class="space-y-1 text-sm text-secondary">
                                <label class="flex items-center gap-2 cursor-default">
                                    <input type="checkbox" disabled class="w-3.5 h-3.5 accent-indigo-500 shrink-0">
                                    Todo
                                </label>
                                <label class="flex items-center gap-2 cursor-default">
                                    <input type="checkbox" checked disabled class="w-3.5 h-3.5 accent-indigo-500 shrink-0">
                                    <span class="line-through text-muted">Done</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Links & embeds -->
                <section>
                    <h2 class="section-title">
                        <Link2 class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.links') }}
                    </h2>
                    <div class="card overflow-x-auto">
                        <table class="w-full text-sm min-w-[340px]">
                            <thead>
                                <tr class="border-b border-base bg-surface-2">
                                    <th class="th">{{ t('guide.col.syntax') }}</th>
                                    <th class="th">{{ t('guide.col.description') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base">
                                <tr>
                                    <td class="td"><code class="ci">[[Note title]]</code></td>
                                    <td class="td text-secondary">{{ t('guide.linkDesc.wikiLink') }}</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">[[Note|alias]]</code></td>
                                    <td class="td text-secondary">{{ t('guide.linkDesc.wikiLinkAlias') }}</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">![[Note title]]</code></td>
                                    <td class="td text-secondary">{{ t('guide.linkDesc.embed') }}</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">[text](https://…)</code></td>
                                    <td class="td text-secondary">{{ t('guide.linkDesc.externalLink') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Images -->
                <section>
                    <h2 class="section-title">
                        <ImageIcon class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.images') }}
                    </h2>
                    <div class="card overflow-x-auto mb-4">
                        <table class="w-full text-sm min-w-[340px]">
                            <thead>
                                <tr class="border-b border-base bg-surface-2">
                                    <th class="th">{{ t('guide.col.syntax') }}</th>
                                    <th class="th">{{ t('guide.col.description') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base">
                                <tr>
                                    <td class="td"><code class="ci">![alt](url)</code></td>
                                    <td class="td text-secondary">{{ t('guide.imageDesc.basic') }}</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">![alt|300](url)</code></td>
                                    <td class="td text-secondary">{{ t('guide.imageDesc.width') }}</td>
                                </tr>
                                <tr>
                                    <td class="td"><code class="ci">![alt|300x200](url)</code></td>
                                    <td class="td text-secondary">{{ t('guide.imageDesc.dimensions') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="card p-4 flex items-start gap-3">
                            <span class="w-8 h-8 flex items-center justify-center rounded-md bg-indigo-500/10 text-indigo-400 text-base shrink-0">⬆</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-primary">{{ t('guide.imageFeatures.dragdrop.title') }}</p>
                                <p class="text-xs text-secondary mt-0.5">{{ t('guide.imageFeatures.dragdrop.desc') }}</p>
                            </div>
                        </div>
                        <div class="card p-4 flex items-start gap-3">
                            <span class="w-8 h-8 flex items-center justify-center rounded-md bg-indigo-500/10 text-indigo-400 text-base shrink-0">⇧</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-primary">{{ t('guide.imageFeatures.paste.title') }}</p>
                                <p class="text-xs text-secondary mt-0.5">{{ t('guide.imageFeatures.paste.desc') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Blockquote & divider -->
                <section>
                    <h2 class="section-title">
                        <Quote class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.blocks') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="card p-4 space-y-3">
                            <p class="label">{{ t('guide.blockTypes.quote') }}</p>
                            <code class="block text-xs text-indigo-400 font-mono">&gt; This is a quote</code>
                            <blockquote class="border-l-4 border-indigo-400/40 pl-3 text-sm text-secondary italic">
                                This is a quote
                            </blockquote>
                        </div>
                        <div class="card p-4 space-y-3">
                            <p class="label">{{ t('guide.blockTypes.divider') }}</p>
                            <code class="block text-xs text-indigo-400 font-mono">---</code>
                            <hr class="border-base">
                        </div>
                    </div>
                </section>

                <!-- Code blocks -->
                <section>
                    <h2 class="section-title">
                        <FileCode class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.code') }}
                    </h2>
                    <div class="card p-4 space-y-3">
                        <div class="overflow-x-auto">
                            <pre class="code-pre">```javascript
function hello(name) {
  return `Hello, ${name}!`;
}
```</pre>
                        </div>
                        <p class="text-xs text-muted">{{ t('guide.codeHint') }}</p>
                    </div>
                </section>

                <!-- Tables -->
                <section>
                    <h2 class="section-title">
                        <Table class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.tables') }}
                    </h2>
                    <div class="card p-4 space-y-3">
                        <div class="overflow-x-auto">
                            <pre class="code-pre">| Name   | Role    |
| ------ | ------- |
| Alice  | Admin   |
| Bob    | User    |</pre>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="text-sm border-collapse min-w-[160px]">
                                <thead>
                                    <tr>
                                        <th class="border border-base px-3 py-1.5 text-left font-semibold text-primary bg-surface-2">Name</th>
                                        <th class="border border-base px-3 py-1.5 text-left font-semibold text-primary bg-surface-2">Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-base px-3 py-1.5 text-secondary">Alice</td>
                                        <td class="border border-base px-3 py-1.5 text-secondary">Admin</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-base px-3 py-1.5 text-secondary">Bob</td>
                                        <td class="border border-base px-3 py-1.5 text-secondary">User</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Callouts -->
                <section>
                    <h2 class="section-title">
                        <Layers class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.callouts') }}
                    </h2>
                    <div class="card p-4 mb-4 space-y-2">
                        <p class="text-xs text-muted">{{ t('guide.calloutSyntax') }}</p>
                        <div class="overflow-x-auto">
                            <pre class="code-pre">&gt; [!warning] Optional title
&gt; Callout body text here.</pre>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="c in calloutTypes"
                            :key="c.type"
                            class="callout"
                            :class="`callout-${c.type}`"
                        >
                            <div class="callout-header">
                                <span class="callout-icon text-base leading-none">{{ c.icon }}</span>
                                <span class="callout-title">{{ c.label }}</span>
                                <span v-if="c.aliases.length" class="text-xs font-normal opacity-60 ml-auto shrink-0">
                                    {{ c.aliases.join(', ') }}
                                </span>
                            </div>
                            <div class="callout-body">
                                <code class="text-xs opacity-70 font-mono">&gt; [!{{ c.label }}]</code>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Slash commands -->
                <section>
                    <h2 class="section-title">
                        <Slash class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.slash') }}
                    </h2>
                    <p class="text-sm text-secondary mb-4">{{ t('guide.slashHint') }}</p>
                    <div class="card divide-y divide-base">
                        <div
                            v-for="cmd in slashCommands"
                            :key="cmd.label"
                            class="flex items-center gap-3 px-4 py-3"
                        >
                            <span class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-2 border border-base text-xs font-bold text-indigo-400 shrink-0 font-mono">
                                {{ cmd.icon }}
                            </span>
                            <div class="flex items-center justify-between gap-4 min-w-0 flex-1">
                                <p class="text-sm font-medium text-primary leading-tight truncate">{{ cmd.label }}</p>
                                <code class="text-xs text-muted font-mono shrink-0">{{ cmd.syntax }}</code>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Navigation & tools -->
                <section>
                    <h2 class="section-title">
                        <Navigation class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.navigation') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div
                            v-for="item in navItems"
                            :key="item.key"
                            class="card p-4 flex items-start gap-3"
                        >
                            <span class="w-8 h-8 flex items-center justify-center rounded-md bg-indigo-500/10 text-indigo-400 text-base shrink-0">
                                {{ item.icon }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-primary">{{ t('guide.nav.' + item.key + '.title') }}</p>
                                <p class="text-xs text-secondary mt-0.5">{{ t('guide.nav.' + item.key + '.desc') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Keyboard shortcuts -->
                <section>
                    <h2 class="section-title">
                        <Code class="w-4 h-4 text-indigo-400 shrink-0" />
                        {{ t('guide.sections.shortcuts') }}
                    </h2>
                    <div class="card overflow-x-auto">
                        <table class="w-full text-sm min-w-[280px]">
                            <thead>
                                <tr class="border-b border-base bg-surface-2">
                                    <th class="th">{{ t('guide.col.shortcut') }}</th>
                                    <th class="th">{{ t('guide.col.description') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base">
                                <tr v-for="s in shortcuts" :key="s.key">
                                    <td class="td whitespace-nowrap">
                                        <kbd class="px-2 py-0.5 rounded bg-surface-2 border border-base text-xs font-mono text-primary">{{ s.key }}</kbd>
                                    </td>
                                    <td class="td text-secondary">{{ t('guide.shortcuts.' + s.descKey) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════
             TAB : PREVIEW — mini Notes interface
         ════════════════════════════════════════════════════════════════ -->
        <div v-if="activeTab === 'preview'" class="pb-8">
            <!-- Two-pane layout -->
            <div class="flex border border-base rounded-2xl overflow-hidden bg-surface" style="height: calc(100vh - 280px); min-height: 480px;">
                <!-- ── Sidebar : note list ──────────────────────────────── -->
                <div class="w-56 sm:w-64 shrink-0 border-r border-base flex flex-col bg-surface-2 overflow-hidden">
                    <div class="px-4 py-3 border-b border-base flex items-center justify-between">
                        <span class="text-xs font-semibold text-muted uppercase tracking-wide">Notes</span>
                        <span class="text-xs text-muted">{{ DEMO_NOTES.length }}</span>
                    </div>
                    <div class="flex-1 overflow-y-auto scrollbar-thin">
                        <button
                            v-for="note in DEMO_NOTES"
                            :key="note.id"
                            class="w-full text-left px-4 py-3 border-b border-base transition-colors last:border-0"
                            :class="activeNoteId === note.id
                                ? 'bg-indigo-600/10 border-l-2 border-l-indigo-500'
                                : 'hover:bg-surface border-l-2 border-l-transparent'"
                            v-on:click="activeNoteId = note.id"
                        >
                            <p class="text-sm font-medium text-primary truncate leading-snug">{{ note.title }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ note.date }}</p>
                            <div v-if="note.tags.length" class="flex flex-wrap gap-1 mt-1.5">
                                <span
                                    v-for="tag in note.tags"
                                    :key="tag"
                                    class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-500/10 text-indigo-400"
                                >
                                    #{{ tag }}
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- ── Main panel : rendered note ──────────────────────── -->
                <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                    <!-- Note header -->
                    <div class="px-6 sm:px-10 pt-7 pb-4 border-b border-base shrink-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-primary leading-tight">{{ activeNote.title }}</h1>
                        <div class="flex items-center flex-wrap gap-2 mt-2">
                            <span class="text-xs text-muted">{{ activeNote.date }}</span>
                            <span
                                v-for="tag in activeNote.tags"
                                :key="tag"
                                class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-500/10 text-indigo-400"
                            >
                                #{{ tag }}
                            </span>
                        </div>
                    </div>

                    <!-- Rendered content -->
                    <div
                        class="flex-1 overflow-y-auto scrollbar-thin px-6 sm:px-10 py-6"
                        v-on:click="handlePreviewClick"
                    >
                        <div
                            class="prose prose-sm dark:prose-invert max-w-none text-primary select-text wiki-link-preview"
                            v-html="renderedActiveNote"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.section-title {
    @apply text-base font-semibold text-primary mb-4 flex items-center gap-2;
}
.card {
    @apply bg-surface border border-base rounded-xl;
}
.label {
    @apply text-xs font-semibold text-muted uppercase tracking-wide;
}
.th {
    @apply text-left px-4 py-2.5 font-medium text-secondary;
}
.td {
    @apply px-4 py-2.5;
}
.ci {
    @apply bg-surface-2 border border-base rounded px-1.5 py-0.5 text-xs font-mono text-indigo-300;
}
.code-pre {
    @apply text-xs text-indigo-400 font-mono leading-relaxed;
    white-space: pre;
}
/* Demo wiki-link missing (no matching note in the set) */
:deep(.wiki-link-missing) {
    color: #94a3b8;
    text-decoration: underline;
    text-decoration-style: dotted;
    text-underline-offset: 2px;
    cursor: default;
}
</style>
