<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppPageHeader from '@/components/ui/AppPageHeader.vue';
import AppPagination from '@/components/ui/AppPagination.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Shield, Trash2, UserRound } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

const { t } = useI18n();

const props = defineProps({
    tab: { type: String, default: 'stats' },
    stats: { type: Object, default: null },
    users: { type: Object, default: null },
    search: { type: String, default: '' },
});

const searchQuery = ref(props.search);

function searchUsers() {
    router.get(route('dev.dashboard.users'), { search: searchQuery.value }, { preserveState: true });
}

function toggleRole(user) {
    router.post(route('dev.dashboard.users.toggle-role', user.id));
}

const deleteForm = useForm({});
function destroyUser(user) {
    if (confirm(`Delete user ${user.name}?`)) {
        deleteForm.delete(route('dev.dashboard.users.destroy', user.id));
    }
}
</script>

<template>
    <Head title="Dev Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <AppPageHeader title="Dev Dashboard" />
        </template>

        <div class="space-y-6">
            <!-- Tabs -->
            <div class="flex gap-2 border-b border-base">
                <Link
                    :href="route('dev.dashboard.stats')"
                    class="px-4 py-2 text-sm font-medium transition-colors"
                    :class="tab === 'stats' ? 'text-indigo-400 border-b-2 border-indigo-400' : 'text-secondary hover:text-primary'"
                >
                    Stats
                </Link>
                <Link
                    :href="route('dev.dashboard.users')"
                    class="px-4 py-2 text-sm font-medium transition-colors"
                    :class="tab === 'users' ? 'text-indigo-400 border-b-2 border-indigo-400' : 'text-secondary hover:text-primary'"
                >
                    Users
                </Link>
            </div>

            <!-- Stats tab -->
            <div v-if="tab === 'stats' && stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-surface rounded-xl p-5 shadow">
                    <div class="flex items-center gap-3">
                        <UserRound class="w-5 h-5 text-indigo-400" />
                        <div>
                            <p class="text-xs text-muted uppercase tracking-wider">Total Users</p>
                            <p class="text-2xl font-bold text-primary">{{ stats.users }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users tab -->
            <div v-if="tab === 'users'">
                <div class="mb-4 flex gap-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search users..."
                        class="flex-1 rounded-lg border border-base bg-surface-2 px-3 py-2 text-sm text-primary placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        v-on:keydown.enter="searchUsers"
                    />
                    <button
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors"
                        v-on:click="searchUsers"
                    >
                        Search
                    </button>
                </div>

                <div v-if="users" class="bg-surface rounded-xl overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-subtle">
                        <thead>
                            <tr class="bg-surface-2/50">
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Roles</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Joined</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-muted">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-subtle">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-surface-2/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-primary">{{ user.name }}</td>
                                <td class="px-4 py-3 text-sm text-secondary">{{ user.email }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <AppBadge v-for="role in user.roles" :key="role.id" variant="primary">
                                            {{ role.name }}
                                        </AppBadge>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            class="text-indigo-400 hover:text-indigo-300 transition-colors"
                                            :title="user.roles?.some(r => r.name === 'ROLE_DEV') ? 'Remove ROLE_DEV' : 'Grant ROLE_DEV'"
                                            v-on:click="toggleRole(user)"
                                        >
                                            <Shield class="w-4 h-4" />
                                        </button>
                                        <button
                                            class="text-rose-400 hover:text-rose-300 transition-colors"
                                            title="Delete user"
                                            v-on:click="destroyUser(user)"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-4 py-3 border-t border-subtle">
                        <AppPagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
