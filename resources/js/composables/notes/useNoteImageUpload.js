import { ref } from 'vue';
import axios from 'axios';

export function useNoteImageUpload(editContent, textareaRef) {
    const isUploading = ref(false);
    const isDragOver = ref(false);

    async function uploadAndInsert(file) {
        if (!file || !file.type.startsWith('image/')) return;

        isUploading.value = true;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const { data } = await axios.post(route('notes.images.upload'), formData);

            const textarea = textareaRef.value;
            const cursor = textarea?.selectionStart ?? editContent.value.length;
            const name = file.name.replace(/\.[^.]+$/, '');
            const markdown = `![${name}](${data.url})`;

            editContent.value = editContent.value.slice(0, cursor) + markdown + editContent.value.slice(cursor);
        } catch (err) {
            console.error('[ImageUpload] failed:', err?.response?.data ?? err);
        } finally {
            isUploading.value = false;
        }
    }

    function onDragOver(event) {
        event.preventDefault();
        isDragOver.value = true;
    }

    function onDragLeave() {
        isDragOver.value = false;
    }

    function onDrop(event) {
        event.preventDefault();
        isDragOver.value = false;
        const file = event.dataTransfer?.files?.[0];
        if (file) uploadAndInsert(file);
    }

    function onPaste(event) {
        const file = event.clipboardData?.files?.[0];
        if (file?.type.startsWith('image/')) {
            event.preventDefault();
            uploadAndInsert(file);
        }
    }

    return { isUploading, isDragOver, onDragOver, onDragLeave, onDrop, onPaste };
}
