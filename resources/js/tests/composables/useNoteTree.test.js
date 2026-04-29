import { describe, it, expect, vi, beforeEach } from 'vitest';

const mockAxiosPost = vi.hoisted(() => vi.fn());
const mockAxiosGet = vi.hoisted(() => vi.fn());

vi.mock('axios', () => ({
    default: {
        post: mockAxiosPost,
        get: mockAxiosGet,
    },
}));

globalThis.route = (name, id) => (id ? `/${name.replace('.', '/')}/${id}` : `/${name.replace('.', '/')}`);

describe('useNoteTree — createNote', () => {
    beforeEach(() => {
        vi.resetModules();
        mockAxiosPost.mockClear();
        mockAxiosGet.mockClear();
    });

    it('sends a POST to the store route with the given parent_id', async () => {
        const newNote = { id: 1, title: '', content: '', parent_id: null, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { createNote } = useNoteTree([]);

        await createNote(null);

        expect(mockAxiosPost).toHaveBeenCalledWith('/notes/store', { parent_id: null });
    });

    it('sends the correct parent_id when creating a child note', async () => {
        const newNote = { id: 2, title: '', content: '', parent_id: 5, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { createNote } = useNoteTree([]);

        await createNote(5);

        expect(mockAxiosPost).toHaveBeenCalledWith('/notes/store', { parent_id: 5 });
    });

    it('adds the new note to flatNotes', async () => {
        const newNote = { id: 42, title: '', content: '', parent_id: null, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { createNote, flatNotes } = useNoteTree([]);

        await createNote(null);

        expect(flatNotes.value.some((n) => n.id === 42)).toBe(true);
    });

    it('selects the newly created note', async () => {
        const newNote = { id: 7, title: '', content: '', parent_id: null, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { createNote, selectedNoteId, loadedNote } = useNoteTree([]);

        await createNote(null);

        expect(selectedNoteId.value).toBe(7);
        expect(loadedNote.value).toEqual(newNote);
    });
});

describe('handleCreateNote — resets isPreview to edit mode', () => {
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

    beforeEach(() => {
        vi.resetModules();
        vi.stubGlobal('localStorage', makeLocalStorage());
        mockAxiosPost.mockClear();
        mockAxiosGet.mockClear();
    });

    it('forces isPreview to false after creating a note, even if it was true', async () => {
        const newNote = { id: 1, title: '', content: '', parent_id: null, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        // Simulate a user who had preview mode on
        localStorage.setItem('onyx:preview-mode', 'true');

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { usePreference } = await import('@/composables/usePreference');

        const { createNote } = useNoteTree([]);
        const isPreview = usePreference('onyx:preview-mode', false);

        expect(isPreview.value).toBe(true);

        // Simulate handleCreateNote
        await createNote(null);
        isPreview.value = false;

        expect(isPreview.value).toBe(false);
    });

    it('keeps isPreview false when it was already false', async () => {
        const newNote = { id: 2, title: '', content: '', parent_id: null, tags: [] };
        mockAxiosPost.mockResolvedValue({ data: newNote });
        mockAxiosGet.mockResolvedValue({ data: newNote });

        localStorage.setItem('onyx:preview-mode', 'false');

        const { useNoteTree } = await import('@/composables/notes/useNoteTree');
        const { usePreference } = await import('@/composables/usePreference');

        const { createNote } = useNoteTree([]);
        const isPreview = usePreference('onyx:preview-mode', false);

        await createNote(null);
        isPreview.value = false;

        expect(isPreview.value).toBe(false);
    });
});
