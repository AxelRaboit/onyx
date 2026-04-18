import { ref } from 'vue';

/**
 * Generic drag-and-drop reorder composable.
 *
 * @param {import('vue').Ref<Array>} items - reactive array of items (must have `id`)
 * @param {(ids: number[]) => void} onReorder - callback with new ordered IDs after drop
 */
export function useDragDrop(items, onReorder) {
    const draggingId = ref(null);
    const dragOverId = ref(null);

    function onDragStart(event, item) {
        draggingId.value = item.id;
        event.dataTransfer.effectAllowed = 'move';
    }

    function onDragEnd() {
        draggingId.value = null;
        dragOverId.value = null;
    }

    function onDragOver(event, item) {
        event.preventDefault();
        if (item.id !== draggingId.value) {
            dragOverId.value = item.id;
        }
    }

    function onDrop(event, targetItem) {
        event.preventDefault();
        const fromId = draggingId.value;
        if (!fromId || fromId === targetItem.id) return;

        const list = [...items.value];
        const fromIndex = list.findIndex((item) => item.id === fromId);
        const toIndex = list.findIndex((item) => item.id === targetItem.id);
        const [moved] = list.splice(fromIndex, 1);
        list.splice(toIndex, 0, moved);
        items.value = list;

        draggingId.value = null;
        dragOverId.value = null;

        onReorder(list.map((item) => item.id));
    }

    return { draggingId, dragOverId, onDragStart, onDragEnd, onDragOver, onDrop };
}
