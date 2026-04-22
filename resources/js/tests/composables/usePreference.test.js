import { describe, it, expect, vi, beforeEach } from 'vitest';
import { nextTick } from 'vue';

function makeLocalStorage() {
    let store = {};
    return {
        getItem: (key) => store[key] ?? null,
        setItem: (key, val) => {
            store[key] = String(val);
        },
        removeItem: (key) => {
            delete store[key];
        },
        clear: () => {
            store = {};
        },
    };
}

describe('usePreference', () => {
    beforeEach(() => {
        vi.stubGlobal('localStorage', makeLocalStorage());
    });

    it('returns the default value when nothing is stored', async () => {
        const { usePreference } = await import('@/composables/usePreference');
        const pref = usePreference('sidebar-open', true);

        expect(pref.value).toBe(true);
    });

    it('returns false as default value when not specified', async () => {
        const { usePreference } = await import('@/composables/usePreference');
        const pref = usePreference('some-key');

        expect(pref.value).toBe(false);
    });

    it('reads an existing true value from localStorage', async () => {
        localStorage.setItem('sidebar-open', 'true');
        const { usePreference } = await import('@/composables/usePreference');

        const pref = usePreference('sidebar-open', false);

        expect(pref.value).toBe(true);
    });

    it('reads an existing false value from localStorage', async () => {
        localStorage.setItem('sidebar-open', 'false');
        const { usePreference } = await import('@/composables/usePreference');

        const pref = usePreference('sidebar-open', true);

        expect(pref.value).toBe(false);
    });

    it('persists a changed value to localStorage', async () => {
        const { usePreference } = await import('@/composables/usePreference');
        const pref = usePreference('sidebar-open', false);

        pref.value = true;
        await nextTick();

        expect(localStorage.getItem('sidebar-open')).toBe('true');
    });

    it('persists false to localStorage when toggled off', async () => {
        localStorage.setItem('sidebar-open', 'true');
        const { usePreference } = await import('@/composables/usePreference');
        const pref = usePreference('sidebar-open', true);

        pref.value = false;
        await nextTick();

        expect(localStorage.getItem('sidebar-open')).toBe('false');
    });

    it('uses separate storage keys for separate preferences', async () => {
        const { usePreference } = await import('@/composables/usePreference');
        const a = usePreference('key-a', false);
        usePreference('key-b', false);

        a.value = true;
        await nextTick();

        expect(localStorage.getItem('key-a')).toBe('true');
        expect(localStorage.getItem('key-b')).toBeNull();
    });
});
