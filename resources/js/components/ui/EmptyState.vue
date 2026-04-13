<script setup>
import { FileText, Folder, Tag, Calendar, Settings, Search, List, Star } from 'lucide-vue-next';

defineProps({
    message: { type: String, required: true },
    compact: { type: Boolean, default: false },
    icon: { type: String, default: null },
});

const icons = {
    file: FileText,
    folder: Folder,
    tag: Tag,
    calendar: Calendar,
    settings: Settings,
    search: Search,
    list: List,
    star: Star,
};
</script>

<template>
    <div class="flex flex-col items-center justify-center text-muted" :class="compact ? 'py-4 text-xs gap-1' : 'py-8 text-sm gap-2'">
        <component
            :is="icons[icon]"
            v-if="icon && icons[icon]"
            class="opacity-30"
            :class="compact ? 'w-5 h-5' : 'w-8 h-8'"
            :stroke-width="1.5"
        />
        {{ message }}
    </div>
</template>
