<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppPageHeader from '@/components/ui/AppPageHeader.vue';
import AppPagination from '@/components/ui/AppPagination.vue';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';
import EmptyState from '@/components/ui/EmptyState.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Activity, Check, Mail, NotebookText, Pencil, Shield, Trash2, UserRound, Users, X } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { onMounted, ref } from 'vue';

const { t: translate } = useI18n();

const props = defineProps({
    tab: { type: String, default: 'stats' },
    stats: { type: Object, default: null },
    users: { type: Object, default: null },
    search: { type: String, default: '' },
    parameters: { type: Array, default: () => [] },
    parameterUpdatePath: { type: String, default: '' },
});

// ── Tab nav scroll ───────────────────────────────────────────────────────────

const tabNav = ref(null);
onMounted(() => {
    const active = tabNav.value?.querySelector('[aria-current="page"]');
    active?.scrollIntoView({ block: 'nearest', inline: 'center' });
});

// ── Users tab ────────────────────────────────────────────────────────────────

const searchInput = ref(props.search);
const performSearch = () => {
    router.get(route('dev.dashboard.users'), { search: searchInput.value });
};

const pendingToggleUser = ref(null);
const doToggleRole = () => {
    if (!pendingToggleUser.value) return;
    useForm({}).post(route('dev.dashboard.users.toggle-role', pendingToggleUser.value.id));
    pendingToggleUser.value = null;
};

const pendingDeleteUser = ref(null);
const doDeleteUser = () => {
    if (!pendingDeleteUser.value) return;
    useForm({}).delete(route('dev.dashboard.users.destroy', pendingDeleteUser.value.id));
    pendingDeleteUser.value = null;
};

// ── Parameters tab ───────────────────────────────────────────────────────────

const editingKey = ref(null);
const editingValue = ref('');
const editSaving = ref(false);

const startEdit = (param) => {
    editingKey.value = param.key;
    editingValue.value = param.value ?? '';
};

const cancelEdit = () => {
    editingKey.value = null;
};

const saveParameter = async (param) => {
    if (editSaving.value) return;
    editSaving.value = true;
    const url = props.parameterUpdatePath.replace('__key__', encodeURIComponent(param.key));
    try {
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ value: editingValue.value }),
        });
        if (response.ok) {
            param.value = editingValue.value || null;
            editingKey.value = null;
        }
    } finally {
        editSaving.value = false;
    }
};

// ── Invitations tab ──────────────────────────────────────────────────────────

const invitationForm = useForm({
    email: '',
    message: '',
    credential_email: '',
    credential_password: '',
});

const submitInvitation = () => {
    invitationForm.post(route('dev.dashboard.invitations.send'), {
        onSuccess: () => invitationForm.reset(),
    });
};
</script>

<template>
    <Head :title="translate('admin.title')" />

    <AuthenticatedLayout>
        <template #header>
            <AppPageHeader :title="translate('admin.title')" />
        </template>

        <div class="space-y-6">
            <!-- Tabs -->
            <div class="border-b border-line overflow-x-auto">
                <nav ref="tabNav" class="flex gap-6 sm:gap-8 whitespace-nowrap min-w-max">
                    <Link
                        :href="route('dev.dashboard.stats')"
                        :aria-current="tab === 'stats' ? 'page' : undefined"
                        class="py-3 px-1 border-b-2 transition-colors text-sm font-medium flex items-center gap-1.5"
                        :class="tab === 'stats' ? 'border-indigo-500 text-primary' : 'border-transparent text-secondary hover:text-primary'"
                    >
                        <Activity class="w-3.5 h-3.5" :stroke-width="2" />
                        {{ translate('admin.stats.title') }}
                    </Link>
                    <Link
                        :href="route('dev.dashboard.users')"
                        :aria-current="tab === 'users' ? 'page' : undefined"
                        class="py-3 px-1 border-b-2 transition-colors text-sm font-medium flex items-center gap-1.5"
                        :class="tab === 'users' ? 'border-indigo-500 text-primary' : 'border-transparent text-secondary hover:text-primary'"
                    >
                        <Users class="w-3.5 h-3.5" :stroke-width="2" />
                        {{ translate('admin.users.title') }}
                    </Link>
                    <Link
                        :href="route('dev.dashboard.invitations')"
                        :aria-current="tab === 'invitations' ? 'page' : undefined"
                        class="py-3 px-1 border-b-2 transition-colors text-sm font-medium flex items-center gap-1.5"
                        :class="tab === 'invitations' ? 'border-indigo-500 text-primary' : 'border-transparent text-secondary hover:text-primary'"
                    >
                        <Mail class="w-3.5 h-3.5" :stroke-width="2" />
                        {{ translate('admin.invitations.title') }}
                    </Link>
                    <Link
                        :href="route('dev.dashboard.parameters')"
                        :aria-current="tab === 'parameters' ? 'page' : undefined"
                        class="py-3 px-1 border-b-2 transition-colors text-sm font-medium flex items-center gap-1.5"
                        :class="tab === 'parameters' ? 'border-indigo-500 text-primary' : 'border-transparent text-secondary hover:text-primary'"
                    >
                        <Shield class="w-3.5 h-3.5" :stroke-width="2" />
                        {{ translate('admin.parameters.title') }}
                    </Link>
                </nav>
            </div>

            <!-- Stats tab -->
            <div v-if="tab === 'stats' && stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium text-secondary uppercase tracking-wide">{{ translate('admin.stats.usersTotal') }}</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/10 flex items-center justify-center">
                            <Users class="w-4 h-4 text-indigo-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-indigo-400">{{ stats.users.total }}</p>
                    <p class="text-xs text-secondary mt-1.5">
                        <span class="text-indigo-400 font-medium">+{{ stats.users.newThisMonth }}</span>
                        {{ ' ' + translate('admin.stats.usersNewThisMonth').toLowerCase() }}
                    </p>
                </div>

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium text-secondary uppercase tracking-wide">{{ translate('admin.stats.notesTotal') }}</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/10 flex items-center justify-center">
                            <NotebookText class="w-4 h-4 text-indigo-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-indigo-400">{{ stats.notes }}</p>
                    <p class="text-xs text-secondary mt-1.5">{{ translate('admin.stats.notes') }}</p>
                </div>
            </div>

            <!-- Users tab -->
            <div v-if="tab === 'users'" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input
                        v-model="searchInput"
                        type="text"
                        :placeholder="translate('admin.users.searchPlaceholder')"
                        class="flex-1 px-4 py-2 rounded-lg bg-surface-2 border border-line text-primary placeholder-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        v-on:keyup.enter="performSearch"
                    >
                    <button
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors text-sm font-medium"
                        v-on:click="performSearch"
                    >
                        {{ translate('admin.users.search') }}
                    </button>
                </div>

                <EmptyState v-if="users && users.data.length === 0" icon="search" :message="translate('admin.users.empty')" />

                <template v-if="users && users.data.length > 0">
                    <!-- Mobile cards -->
                    <div class="sm:hidden space-y-3">
                        <div v-for="user in users.data" :key="user.id" class="bg-surface border border-line rounded-lg p-4 space-y-3">
                            <div class="min-w-0">
                                <p class="font-medium text-primary truncate">{{ user.name }}</p>
                                <p class="text-xs text-secondary truncate">{{ user.email }}</p>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <AppBadge v-for="role in user.roles" :key="role.id" variant="indigo">
                                    {{ role.name }}
                                </AppBadge>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-line">
                                <p class="text-xs text-muted">{{ new Date(user.created_at).toLocaleDateString() }}</p>
                                <div class="flex items-center gap-1">
                                    <button
                                        class="p-1.5 rounded text-muted transition-colors"
                                        :class="user.roles?.some((role) => role.name === 'ROLE_DEV') ? 'hover:text-indigo-400' : 'hover:text-rose-400'"
                                        v-on:click="pendingToggleUser = user"
                                    >
                                        <component :is="user.roles?.some((role) => role.name === 'ROLE_DEV') ? UserRound : Shield" class="w-4 h-4" />
                                    </button>
                                    <button class="p-1.5 rounded text-muted hover:text-red-400 transition-colors" v-on:click="pendingDeleteUser = user">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden sm:block bg-surface border border-line rounded-lg overflow-x-auto">
                        <table class="w-full min-w-[560px]">
                            <thead class="bg-surface-2 border-b border-line">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-primary">{{ translate('admin.users.name') }}</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-primary hidden sm:table-cell">{{ translate('admin.users.email') }}</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-primary hidden md:table-cell">{{ translate('admin.users.roles') }}</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-primary hidden lg:table-cell">{{ translate('admin.users.created') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-primary">{{ translate('admin.users.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-surface-2/50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-primary">{{ user.name }}</td>
                                    <td class="px-4 py-3 text-sm text-secondary hidden sm:table-cell">{{ user.email }}</td>
                                    <td class="px-4 py-3 hidden md:table-cell">
                                        <div class="flex flex-wrap gap-1">
                                            <AppBadge v-for="role in user.roles" :key="role.id" variant="indigo">
                                                {{ role.name }}
                                            </AppBadge>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-secondary hidden lg:table-cell">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                class="p-1.5 rounded text-muted transition-colors"
                                                :class="user.roles?.some((role) => role.name === 'ROLE_DEV') ? 'hover:text-indigo-400' : 'hover:text-rose-400'"
                                                :title="user.roles?.some((role) => role.name === 'ROLE_DEV') ? translate('admin.users.makeUser') : translate('admin.users.makeDev')"
                                                v-on:click="pendingToggleUser = user"
                                            >
                                                <component :is="user.roles?.some((role) => role.name === 'ROLE_DEV') ? UserRound : Shield" class="w-4 h-4" />
                                            </button>
                                            <button
                                                class="p-1.5 rounded text-muted hover:text-red-400 transition-colors"
                                                :title="translate('admin.users.deleteUser', { name: user.name })"
                                                v-on:click="pendingDeleteUser = user"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="px-4 py-3 border-t border-line">
                            <AppPagination :meta="users" />
                        </div>
                    </div>
                </template>
            </div>

            <!-- Invitations tab -->
            <div v-if="tab === 'invitations'" class="max-w-lg space-y-4">
                <p class="text-sm text-secondary">{{ translate('admin.invitations.description') }}</p>

                <form class="space-y-4" v-on:submit.prevent="submitInvitation">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-primary">{{ translate('admin.invitations.email') }}</label>
                        <input
                            v-model="invitationForm.email"
                            type="email"
                            :placeholder="translate('admin.invitations.emailPlaceholder')"
                            class="w-full px-4 py-2 rounded-lg bg-surface-2 border border-line text-primary placeholder-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                        <p v-if="invitationForm.errors.email" class="text-xs text-red-400">{{ invitationForm.errors.email }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-primary">{{ translate('admin.invitations.message') }}</label>
                        <textarea
                            v-model="invitationForm.message"
                            rows="5"
                            :placeholder="translate('admin.invitations.messagePlaceholder')"
                            class="w-full px-4 py-2 rounded-lg bg-surface-2 border border-line text-primary placeholder-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                        />
                        <p v-if="invitationForm.errors.message" class="text-xs text-red-400">{{ invitationForm.errors.message }}</p>
                    </div>

                    <div class="border border-line rounded-lg p-4 space-y-3 bg-surface-2/50">
                        <p class="text-xs text-secondary">{{ translate('admin.invitations.credentialsHint') }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-primary">{{ translate('admin.invitations.credentialEmail') }}</label>
                                <input
                                    v-model="invitationForm.credential_email"
                                    type="email"
                                    :placeholder="translate('admin.invitations.emailPlaceholder')"
                                    class="w-full px-4 py-2 rounded-lg bg-surface border border-line text-primary placeholder-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                <p v-if="invitationForm.errors.credential_email" class="text-xs text-red-400">{{ invitationForm.errors.credential_email }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-primary">{{ translate('admin.invitations.credentialPassword') }}</label>
                                <input
                                    v-model="invitationForm.credential_password"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-lg bg-surface border border-line text-primary placeholder-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                <p v-if="invitationForm.errors.credential_password" class="text-xs text-red-400">{{ invitationForm.errors.credential_password }}</p>
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="invitationForm.processing || !invitationForm.email"
                        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors text-sm font-medium"
                    >
                        <Mail class="w-4 h-4" />
                        {{ invitationForm.processing ? translate('admin.invitations.sending') : translate('admin.invitations.send') }}
                    </button>
                </form>
            </div>

            <!-- Parameters tab -->
            <div v-if="tab === 'parameters'" class="space-y-3">
                <EmptyState v-if="parameters.length === 0" icon="settings" :message="translate('admin.parameters.empty')" />

                <template v-else>
                    <!-- Mobile cards -->
                    <div class="sm:hidden space-y-3">
                        <div v-for="param in parameters" :key="param.key" class="bg-surface border border-line rounded-lg p-4 space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-mono text-sm text-indigo-400 font-medium break-all">{{ param.key }}</p>
                                <button v-if="editingKey !== param.key" class="p-1.5 text-muted hover:text-primary transition-colors shrink-0" v-on:click="startEdit(param)">
                                    <Pencil class="w-3.5 h-3.5" />
                                </button>
                            </div>
                            <template v-if="editingKey === param.key">
                                <input
                                    v-model="editingValue"
                                    class="w-full bg-surface-2 border border-line rounded-lg px-2.5 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    autofocus
                                    v-on:keydown.enter="saveParameter(param)"
                                    v-on:keydown.esc="cancelEdit"
                                >
                                <div class="flex gap-2">
                                    <button :disabled="editSaving" class="flex-1 py-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg transition-colors" v-on:click="saveParameter(param)">
                                        {{ translate('common.save') }}
                                    </button>
                                    <button class="flex-1 py-1.5 text-sm text-secondary hover:text-primary border border-line rounded-lg transition-colors" v-on:click="cancelEdit">
                                        {{ translate('common.cancel') }}
                                    </button>
                                </div>
                            </template>
                            <p v-else class="text-sm font-medium text-primary">{{ param.value ?? '—' }}</p>
                            <p v-if="param.description" class="text-xs text-secondary">{{ param.description }}</p>
                        </div>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden sm:block bg-surface border border-line rounded-xl overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-surface-2 border-b border-line">
                                <tr>
                                    <th class="px-5 py-3 text-left text-sm font-semibold text-primary w-1/3">{{ translate('admin.parameters.key') }}</th>
                                    <th class="px-5 py-3 text-left text-sm font-semibold text-primary w-1/4">{{ translate('admin.parameters.value') }}</th>
                                    <th class="px-5 py-3 text-left text-sm font-semibold text-primary">{{ translate('admin.parameters.description') }}</th>
                                    <th class="px-4 py-3 w-16" />
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                <tr
                                    v-for="param in parameters"
                                    :key="param.key"
                                    class="group hover:bg-surface-2/50 transition-colors"
                                >
                                    <td class="px-5 py-3 font-mono text-sm text-indigo-400 font-medium w-1/3">{{ param.key }}</td>
                                    <td class="px-5 py-3 w-1/4">
                                        <template v-if="editingKey === param.key">
                                            <input
                                                v-model="editingValue"
                                                class="w-full bg-surface-2 border border-line rounded-lg px-2.5 py-1 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                autofocus
                                                v-on:keydown.enter="saveParameter(param)"
                                                v-on:keydown.esc="cancelEdit"
                                            >
                                        </template>
                                        <span v-else class="text-sm font-medium text-primary">{{ param.value ?? '—' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-secondary">{{ param.description ?? '' }}</td>
                                    <td class="px-4 py-3 w-16">
                                        <div class="flex items-center gap-1 justify-end">
                                            <template v-if="editingKey === param.key">
                                                <button
                                                    :disabled="editSaving"
                                                    class="p-1.5 text-muted hover:text-emerald-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    v-on:click="saveParameter(param)"
                                                >
                                                    <Check class="w-3.5 h-3.5" />
                                                </button>
                                                <button class="p-1.5 text-muted hover:text-rose-400 transition-colors" v-on:click="cancelEdit">
                                                    <X class="w-3.5 h-3.5" />
                                                </button>
                                            </template>
                                            <button
                                                v-else
                                                class="p-1.5 text-muted hover:text-primary transition-colors opacity-0 group-hover:opacity-100"
                                                v-on:click="startEdit(param)"
                                            >
                                                <Pencil class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmModal
        :show="!!pendingToggleUser"
        :message="pendingToggleUser ? translate('admin.users.confirmToggle', { name: pendingToggleUser.name }) : ''"
        :confirm-label="pendingToggleUser?.roles?.some((role) => role.name === 'ROLE_DEV') ? translate('admin.users.makeUser') : translate('admin.users.makeDev')"
        confirm-variant="indigo"
        v-on:confirm="doToggleRole"
        v-on:cancel="pendingToggleUser = null"
    />

    <ConfirmModal
        :show="!!pendingDeleteUser"
        :message="pendingDeleteUser ? translate('admin.users.confirmDelete', { name: pendingDeleteUser.name }) : ''"
        confirm-variant="danger"
        v-on:confirm="doDeleteUser"
        v-on:cancel="pendingDeleteUser = null"
    />
</template>
