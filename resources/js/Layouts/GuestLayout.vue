<script setup>
import AppLogo from '@/components/ui/AppLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';

const page = usePage();
const appVersion = page.props.appVersion;
const isLocal = page.props.isLocal ?? false;
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-bg px-4 py-12 transition-colors duration-200">
        <div>
            <Link href="/" class="flex flex-col items-center gap-2">
                <AppLogo :size="64" />
                <span class="text-primary font-bold text-2xl">Onyx</span>
                <span v-if="appVersion" class="text-xs text-secondary">{{ appVersion }}</span>
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-surface border border-line px-6 py-4 shadow-md rounded-lg sm:max-w-md">
            <slot />
        </div>

        <a
            v-if="isLocal"
            href="http://localhost:8025"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-6 flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-muted hover:text-amber-400 hover:bg-amber-500/10 transition-colors"
        >
            <Mail class="w-3.5 h-3.5" />
            Mailpit
        </a>
    </div>
</template>
