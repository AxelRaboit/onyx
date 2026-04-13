<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-primary">
                {{ t('dashboard.title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-surface shadow-sm rounded-lg">
                    <div class="flex items-center gap-4 p-6 text-primary">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-badge-primary-bg text-lg font-semibold text-badge-primary-text select-none">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span>{{ t('dashboard.welcome', { name: user.name }) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
