import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { useNoteFilters } from '@/composables/notes/useNoteFilters';

const mockGet = vi.hoisted(() => vi.fn());

vi.mock('@inertiajs/vue3', () => ({
    router: { get: mockGet },
}));

globalThis.route = (name) => `/${name.replace('.', '/')}`;

describe('useNoteFilters', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        mockGet.mockClear();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('initialises filters from initial values', () => {
        const { filters } = useNoteFilters({ q: 'hello', tag: 'vue' });

        expect(filters.q).toBe('hello');
        expect(filters.tag).toBe('vue');
    });

    it('defaults to empty strings when no initial filters are given', () => {
        const { filters } = useNoteFilters({});

        expect(filters.q).toBe('');
        expect(filters.tag).toBe('');
    });

    it('triggers router.get after 300ms debounce on search', () => {
        const { filters, search } = useNoteFilters({ q: '', tag: '' });
        filters.q = 'my note';

        search();
        expect(mockGet).not.toHaveBeenCalled();

        vi.advanceTimersByTime(300);
        expect(mockGet).toHaveBeenCalledOnce();
    });

    it('debounces multiple rapid search calls into one request', () => {
        const { search } = useNoteFilters({});

        search();
        search();
        search();
        vi.advanceTimersByTime(300);

        expect(mockGet).toHaveBeenCalledOnce();
    });

    it('omits empty filter values from the router request', () => {
        const { filters, search } = useNoteFilters({ q: '', tag: '' });
        filters.q = 'note';

        search();
        vi.advanceTimersByTime(300);

        const [, params] = mockGet.mock.calls[0];
        expect(params).toEqual({ q: 'note' });
        expect(params).not.toHaveProperty('tag');
    });

    it('toggles a tag off when the same tag is selected again', () => {
        const { filters, filterByTag } = useNoteFilters({ q: '', tag: 'vue' });

        filterByTag('vue');

        expect(filters.tag).toBe('');
    });

    it('sets a new tag when a different tag is selected', () => {
        const { filters, filterByTag } = useNoteFilters({ q: '', tag: 'react' });

        filterByTag('vue');

        expect(filters.tag).toBe('vue');
    });

    it('hasFilters returns true when at least one filter is set', () => {
        const { filters, hasFilters } = useNoteFilters({});
        filters.q = 'something';

        expect(hasFilters()).toBe(true);
    });

    it('hasFilters returns false when all filters are empty', () => {
        const { hasFilters } = useNoteFilters({ q: '', tag: '' });

        expect(hasFilters()).toBe(false);
    });

    it('reset clears filters and calls router.get immediately', () => {
        const { filters, reset } = useNoteFilters({ q: 'note', tag: 'vue' });

        reset();

        expect(filters.q).toBe('');
        expect(filters.tag).toBe('');
        expect(mockGet).toHaveBeenCalledOnce();
    });
});
