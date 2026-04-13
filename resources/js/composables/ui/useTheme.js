import { ref, watch } from 'vue';

const STORAGE_KEY = 'onyx-theme';
const DARK = 'dark';
const LIGHT = 'light';

function getInitial() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === DARK || stored === LIGHT) return stored;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK : LIGHT;
}

function apply(t) {
    document.documentElement.classList.add('theme-switching');
    document.documentElement.classList.toggle('dark', t === DARK);
    window.setTimeout(() => document.documentElement.classList.remove('theme-switching'), 300);
}

// Singleton state — shared across all composable calls
const theme = ref(getInitial());
apply(theme.value);

export function useTheme() {
    watch(theme, (t) => {
        apply(t);
        localStorage.setItem(STORAGE_KEY, t);
    });

    function toggle() {
        theme.value = theme.value === DARK ? LIGHT : DARK;
    }

    function setTheme(t) {
        theme.value = t;
    }

    return { theme, toggle, setTheme };
}
