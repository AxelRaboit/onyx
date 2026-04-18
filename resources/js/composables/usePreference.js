import { ref, watch } from 'vue';

export function usePreference(key, defaultValue = false) {
    const stored = localStorage.getItem(key);
    const value = ref(stored !== null ? stored === 'true' : defaultValue);

    watch(value, (newValue) => {
        localStorage.setItem(key, String(newValue));
    });

    return value;
}
