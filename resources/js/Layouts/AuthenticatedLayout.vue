<script setup>
import { ref, computed } from 'vue';
import AppLogo from '@/components/ui/AppLogo.vue';
import { useTheme } from '@/composables/ui/useTheme';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
    LayoutDashboard,
    NotebookPen,
    BookOpen,
    Shield,
    Sun,
    Moon,
    User,
    LogOut,
    Menu,
    X,
    ChevronsLeft,
    ChevronsRight,
    Mail,
} from 'lucide-vue-next';

const { t } = useI18n();
const showMobileMenu = ref(false);
const { theme, toggle: toggleTheme } = useTheme();
const page = usePage();

// ── Sidebar collapsed state (persisted) ──────────────────────────────────
const collapsed = ref(localStorage.getItem('sidebar-collapsed') === 'true');
function toggleSidebar() {
    collapsed.value = !collapsed.value;
    localStorage.setItem('sidebar-collapsed', String(collapsed.value));
}

const navItems = [
    {
        key: 'dashboard',
        route: 'dashboard',
        match: 'dashboard',
        icon: LayoutDashboard,
    },
    {
        key: 'notes',
        route: 'notes.index',
        match: 'notes.*',
        icon: NotebookPen,
    },
    {
        key: 'guide',
        route: 'guide',
        match: 'guide',
        icon: BookOpen,
    },
];

const isDev = computed(() => page.props.auth?.user?.roles?.some(r => r.name === 'ROLE_DEV') ?? false);
const isLocal = computed(() => page.props.isLocal ?? false);
const devNavItem = computed(() => {
    if (!isDev.value) return null;
    return {
        key: 'dev-dashboard',
        route: 'dev.dashboard.stats',
        match: 'dev.*',
        icon: Shield,
    };
});
</script>

<template>
    <div class="min-h-screen bg-bg">
        <!-- Desktop sidebar -->
        <aside
            class="hidden lg:flex flex-col fixed inset-y-0 left-0 bg-surface border-r border-base z-30 transition-all duration-200"
            :class="collapsed ? 'w-16' : 'w-60'"
        >
            <div
                class="flex items-center h-16 border-b border-base shrink-0 transition-all duration-200"
                :class="collapsed ? 'justify-center px-0' : 'justify-between px-4'"
            >
                <Link v-if="!collapsed" :href="route('dashboard')" class="flex items-center gap-2.5 min-w-0">
                    <AppLogo :size="32" />
                    <div class="flex flex-col min-w-0">
                        <span class="text-primary font-bold text-lg tracking-tight truncate leading-tight">Onyx</span>
                        <span class="text-xs text-muted/50 leading-none">{{ $page.props.appVersion }}</span>
                    </div>
                </Link>
                <Link v-else :href="route('dashboard')">
                    <AppLogo :size="32" />
                </Link>

                <button
                    class="p-1.5 rounded-lg text-muted hover:text-primary hover:bg-surface-2 transition-colors shrink-0"
                    :class="collapsed ? 'hidden' : 'ml-2'"
                    v-on:click="toggleSidebar"
                >
                    <ChevronsLeft class="w-4 h-4" />
                </button>
            </div>

            <div v-if="!collapsed" class="border-b border-base px-4 py-3 shrink-0">
                <p class="text-sm font-medium text-primary truncate">{{ $page.props.auth.user.name }}</p>
                <p class="text-xs text-muted truncate">{{ $page.props.auth.user.email }}</p>
            </div>

            <nav class="flex-1 py-4 space-y-0.5" :class="collapsed ? 'px-2' : 'px-3 overflow-y-auto'">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="route(item.route)"
                    class="flex items-center rounded-lg text-sm font-medium transition-colors group relative"
                    :class="[
                        collapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3 py-2.5',
                        route().current(item.match)
                            ? 'bg-indigo-600/15 text-indigo-400'
                            : 'text-secondary hover:text-primary hover:bg-surface-2',
                    ]"
                >
                    <component
                        :is="item.icon"
                        class="w-5 h-5 shrink-0"
                        :class="route().current(item.match) ? 'text-indigo-400' : 'text-muted'"
                    />
                    <span v-if="!collapsed" class="truncate">{{ t('nav.' + item.key) }}</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        {{ t('nav.' + item.key) }}
                    </span>
                </Link>

                <Link
                    v-if="devNavItem"
                    :href="route(devNavItem.route)"
                    class="flex items-center rounded-lg text-sm font-medium transition-colors group relative"
                    :class="[
                        collapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3 py-2.5',
                        route().current(devNavItem.match)
                            ? 'bg-rose-600/15 text-rose-400'
                            : 'text-secondary hover:text-primary hover:bg-surface-2',
                    ]"
                >
                    <component
                        :is="devNavItem.icon"
                        class="w-5 h-5 shrink-0"
                        :class="route().current(devNavItem.match) ? 'text-rose-400' : 'text-muted'"
                    />
                    <span v-if="!collapsed" class="truncate">{{ t('nav.dev-dashboard') }}</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        {{ t('nav.dev-dashboard') }}
                    </span>
                </Link>
            </nav>

            <div class="shrink-0 border-t border-base py-3 space-y-0.5" :class="collapsed ? 'px-2' : 'px-3'">
                <button
                    v-if="collapsed"
                    class="flex items-center justify-center w-full py-2.5 rounded-lg text-muted hover:text-primary hover:bg-surface-2 transition-colors"
                    v-on:click="toggleSidebar"
                >
                    <ChevronsRight class="w-4 h-4" />
                </button>

                <a
                    v-if="isLocal"
                    href="http://localhost:8025"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center rounded-lg text-sm font-medium text-secondary hover:text-amber-400 hover:bg-amber-500/10 transition-colors group relative"
                    :class="collapsed ? 'justify-center py-2.5' : 'gap-3 px-3 py-2.5'"
                >
                    <Mail class="w-5 h-5 shrink-0 text-muted group-hover:text-amber-400 transition-colors" />
                    <span v-if="!collapsed">Mailpit</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        Mailpit
                    </span>
                </a>

                <button
                    class="flex items-center rounded-lg text-sm font-medium text-secondary hover:text-primary hover:bg-surface-2 transition-colors w-full group relative"
                    :class="collapsed ? 'justify-center py-2.5' : 'gap-3 px-3 py-2.5'"
                    v-on:click="toggleTheme"
                >
                    <Sun v-if="theme === 'dark'" class="w-5 h-5 text-muted shrink-0" />
                    <Moon v-else class="w-5 h-5 text-muted shrink-0" />
                    <span v-if="!collapsed">{{ theme === 'dark' ? t('nav.lightMode') : t('nav.darkMode') }}</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        {{ theme === 'dark' ? t('nav.lightMode') : t('nav.darkMode') }}
                    </span>
                </button>

                <Link
                    :href="route('profile.edit')"
                    class="flex items-center rounded-lg text-sm font-medium transition-colors group relative"
                    :class="[
                        collapsed ? 'justify-center py-2.5' : 'gap-3 px-3 py-2.5',
                        route().current('profile.*') ? 'bg-indigo-600/15 text-indigo-400' : 'text-secondary hover:text-primary hover:bg-surface-2',
                    ]"
                >
                    <User class="w-5 h-5 shrink-0 text-muted" />
                    <span v-if="!collapsed" class="truncate">{{ t('nav.profile') }}</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        {{ t('nav.profile') }}
                    </span>
                </Link>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center rounded-lg text-sm font-medium text-secondary hover:text-rose-400 hover:bg-rose-500/10 transition-colors w-full group relative"
                    :class="collapsed ? 'justify-center py-2.5' : 'gap-3 px-3 py-2.5'"
                >
                    <LogOut class="w-5 h-5 shrink-0 text-muted group-hover:text-rose-400 transition-colors" />
                    <span v-if="!collapsed">{{ t('nav.logout') }}</span>
                    <span
                        v-if="collapsed"
                        class="absolute left-full ml-3 px-2.5 py-1.5 rounded-md bg-surface-3 border border-base text-xs font-medium text-primary whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 shadow-lg"
                    >
                        {{ t('nav.logout') }}
                    </span>
                </Link>
            </div>
        </aside>

        <!-- Mobile top bar -->
        <div class="lg:hidden fixed top-0 inset-x-0 h-14 bg-surface border-b border-base z-30 flex items-center justify-between px-4">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <AppLogo :size="28" />
                <span class="text-primary font-bold text-base tracking-tight">Onyx</span>
            </Link>
            <button
                class="p-2 rounded-lg text-secondary hover:text-primary hover:bg-surface-2 transition-colors"
                v-on:click="showMobileMenu = true"
            >
                <Menu class="w-5 h-5" />
            </button>
        </div>

        <!-- Mobile menu overlay -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showMobileMenu" class="lg:hidden fixed inset-0 z-50 flex">
                <div class="absolute inset-0 bg-black/60" v-on:click="showMobileMenu = false" />
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="-translate-x-full"
                    enter-to-class="translate-x-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="translate-x-0"
                    leave-to-class="-translate-x-full"
                >
                    <div v-if="showMobileMenu" class="relative w-60 max-w-[85vw] bg-surface h-full flex flex-col shadow-2xl">
                        <div class="flex items-center justify-between px-4 h-16 border-b border-base shrink-0">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <AppLogo :size="32" class="shrink-0" />
                                <span class="text-primary font-bold text-lg tracking-tight truncate leading-tight">Onyx</span>
                            </div>
                            <button class="p-1.5 text-muted hover:text-primary" v-on:click="showMobileMenu = false">
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="px-4 py-3 border-b border-base shrink-0">
                            <p class="text-sm font-medium text-primary">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-muted truncate">{{ $page.props.auth.user.email }}</p>
                        </div>

                        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                            <Link
                                v-for="item in navItems"
                                :key="item.key"
                                :href="route(item.route)"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                                :class="route().current(item.match)
                                    ? 'bg-indigo-600/15 text-indigo-400'
                                    : 'text-secondary hover:text-primary hover:bg-surface-2'"
                                v-on:click="showMobileMenu = false"
                            >
                                <component
                                    :is="item.icon"
                                    class="w-5 h-5 shrink-0"
                                    :class="route().current(item.match) ? 'text-indigo-400' : 'text-muted'"
                                />
                                {{ t('nav.' + item.key) }}
                            </Link>

                            <Link
                                v-if="devNavItem"
                                :href="route(devNavItem.route)"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                                :class="route().current(devNavItem.match)
                                    ? 'bg-rose-600/15 text-rose-400'
                                    : 'text-secondary hover:text-primary hover:bg-surface-2'"
                                v-on:click="showMobileMenu = false"
                            >
                                <component
                                    :is="devNavItem.icon"
                                    class="w-5 h-5 shrink-0"
                                    :class="route().current(devNavItem.match) ? 'text-rose-400' : 'text-muted'"
                                />
                                {{ t('nav.dev-dashboard') }}
                            </Link>
                        </nav>

                        <div class="shrink-0 border-t border-base px-3 py-3 space-y-1">
                            <a
                                v-if="isLocal"
                                href="http://localhost:8025"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-secondary hover:text-amber-400 hover:bg-amber-500/10 transition-colors"
                                v-on:click="showMobileMenu = false"
                            >
                                <Mail class="w-5 h-5 text-muted shrink-0" />
                                Mailpit
                            </a>
                            <button
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-secondary hover:text-primary hover:bg-surface-2 transition-colors"
                                v-on:click="toggleTheme"
                            >
                                <Sun v-if="theme === 'dark'" class="w-5 h-5 text-muted shrink-0" />
                                <Moon v-else class="w-5 h-5 text-muted shrink-0" />
                                {{ theme === 'dark' ? t('nav.lightMode') : t('nav.darkMode') }}
                            </button>
                            <Link
                                :href="route('profile.edit')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-secondary hover:text-primary hover:bg-surface-2 transition-colors"
                                v-on:click="showMobileMenu = false"
                            >
                                <User class="w-5 h-5 text-muted shrink-0" />
                                {{ t('nav.profile') }}
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-secondary hover:text-rose-400 hover:bg-rose-500/10 transition-colors"
                            >
                                <LogOut class="w-5 h-5 text-muted shrink-0" />
                                {{ t('nav.logout') }}
                            </Link>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Main content area -->
        <div
            class="transition-all duration-200 pt-14 lg:pt-0"
            :class="collapsed ? 'lg:pl-16' : 'lg:pl-60'"
        >
            <header v-if="$slots.header" class="bg-surface border-b border-base h-16 flex items-center lg:sticky lg:top-0 lg:z-20">
                <div class="px-4 sm:px-6 lg:px-8 w-full">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <div class="px-4 sm:px-6 lg:px-8 py-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
