import { describe, it, expect, vi } from 'vitest';
import { ref } from 'vue';
import { useDragDrop } from '@/composables/ui/useDragDrop';

function makeEvent(overrides = {}) {
    return { preventDefault: vi.fn(), dataTransfer: { effectAllowed: null }, ...overrides };
}

describe('useDragDrop', () => {
    it('sets draggingId on drag start', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const { draggingId, onDragStart } = useDragDrop(items, vi.fn());

        onDragStart(makeEvent(), { id: 1 });

        expect(draggingId.value).toBe(1);
    });

    it('sets dataTransfer.effectAllowed to move on drag start', () => {
        const items = ref([{ id: 1 }]);
        const { onDragStart } = useDragDrop(items, vi.fn());
        const event = makeEvent();

        onDragStart(event, { id: 1 });

        expect(event.dataTransfer.effectAllowed).toBe('move');
    });

    it('clears draggingId and dragOverId on drag end', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const { draggingId, dragOverId, onDragStart, onDragOver, onDragEnd } = useDragDrop(items, vi.fn());

        onDragStart(makeEvent(), { id: 1 });
        onDragOver(makeEvent(), { id: 2 });
        onDragEnd();

        expect(draggingId.value).toBeNull();
        expect(dragOverId.value).toBeNull();
    });

    it('sets dragOverId when hovering a different item', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const { dragOverId, onDragStart, onDragOver } = useDragDrop(items, vi.fn());

        onDragStart(makeEvent(), { id: 1 });
        onDragOver(makeEvent(), { id: 2 });

        expect(dragOverId.value).toBe(2);
    });

    it('does not update dragOverId when hovering the dragged item itself', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const { dragOverId, onDragStart, onDragOver } = useDragDrop(items, vi.fn());

        onDragStart(makeEvent(), { id: 1 });
        onDragOver(makeEvent(), { id: 1 });

        expect(dragOverId.value).toBeNull();
    });

    it('reorders items on drop and calls onReorder with new ids', () => {
        const items = ref([{ id: 1 }, { id: 2 }, { id: 3 }]);
        const onReorder = vi.fn();
        const { onDragStart, onDrop } = useDragDrop(items, onReorder);

        onDragStart(makeEvent(), { id: 1 });
        onDrop(makeEvent(), { id: 3 });

        expect(items.value.map((i) => i.id)).toEqual([2, 3, 1]);
        expect(onReorder).toHaveBeenCalledWith([2, 3, 1]);
    });

    it('does nothing on drop when source and target are the same', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const onReorder = vi.fn();
        const { onDragStart, onDrop } = useDragDrop(items, onReorder);

        onDragStart(makeEvent(), { id: 1 });
        onDrop(makeEvent(), { id: 1 });

        expect(items.value.map((i) => i.id)).toEqual([1, 2]);
        expect(onReorder).not.toHaveBeenCalled();
    });

    it('clears draggingId and dragOverId after a successful drop', () => {
        const items = ref([{ id: 1 }, { id: 2 }]);
        const { draggingId, dragOverId, onDragStart, onDrop } = useDragDrop(items, vi.fn());

        onDragStart(makeEvent(), { id: 1 });
        onDrop(makeEvent(), { id: 2 });

        expect(draggingId.value).toBeNull();
        expect(dragOverId.value).toBeNull();
    });
});
