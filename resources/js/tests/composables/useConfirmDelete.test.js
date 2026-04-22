import { describe, it, expect, vi } from 'vitest';
import { ref } from 'vue';
import { useConfirmDelete } from '@/composables/ui/useConfirmDelete';

const mockDelete = vi.hoisted(() => vi.fn());

vi.mock('@inertiajs/vue3', () => ({
    router: { delete: mockDelete },
}));

describe('useConfirmDelete', () => {
    it('starts with the modal closed', () => {
        const { isOpen } = useConfirmDelete();

        expect(isOpen.value).toBe(false);
    });

    it('uses the default message when none is provided', () => {
        const { message } = useConfirmDelete();

        expect(message.value).toBe('Êtes-vous sûr de vouloir supprimer cet élément ?');
    });

    it('accepts a custom string message', () => {
        const { message } = useConfirmDelete('Supprimer cette note ?');

        expect(message.value).toBe('Supprimer cette note ?');
    });

    it('accepts a ref as message', () => {
        const msg = ref('Supprimer ce tag ?');
        const { message } = useConfirmDelete(msg);

        expect(message.value).toBe('Supprimer ce tag ?');
    });

    it('opens the modal and stores the url on confirmDelete', () => {
        const { isOpen, confirmDelete } = useConfirmDelete();

        confirmDelete('/notes/1');

        expect(isOpen.value).toBe(true);
    });

    it('calls router.delete with the pending url on confirm', () => {
        mockDelete.mockClear();
        const { confirmDelete, onConfirm } = useConfirmDelete();

        confirmDelete('/notes/42');
        onConfirm();

        expect(mockDelete).toHaveBeenCalledWith('/notes/42');
    });

    it('closes the modal after confirm', () => {
        const { isOpen, confirmDelete, onConfirm } = useConfirmDelete();

        confirmDelete('/notes/1');
        onConfirm();

        expect(isOpen.value).toBe(false);
    });

    it('closes the modal and does not call router.delete on cancel', () => {
        mockDelete.mockClear();
        const { isOpen, confirmDelete, onCancel } = useConfirmDelete();

        confirmDelete('/notes/1');
        onCancel();

        expect(isOpen.value).toBe(false);
        expect(mockDelete).not.toHaveBeenCalled();
    });
});
