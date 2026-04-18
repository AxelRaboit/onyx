import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const preventDefault = (e) => e.preventDefault();

export function useNoteImageUpload(editContent, textareaRef) {
    const isUploading = ref(false);
    const isDragOver = ref(false);

    onMounted(() => {
        document.addEventListener('dragover', preventDefault);
        document.addEventListener('drop', preventDefault);
    });

    onUnmounted(() => {
        document.removeEventListener('dragover', preventDefault);
        document.removeEventListener('drop', preventDefault);
    });

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

    function getImageFile(dataTransfer) {
        // Files array is populated for OS drags; items for browser-to-browser drags
        if (dataTransfer.files?.length) {
            return dataTransfer.files[0];
        }
        if (dataTransfer.items?.length) {
            const item = [...dataTransfer.items].find(
                (transferItem) => transferItem.kind === 'file' && transferItem.type.startsWith('image/')
            );
            return item?.getAsFile() ?? null;
        }
        return null;
    }

    function onDragOver() {
        isDragOver.value = true;
    }

    function onDragLeave() {
        isDragOver.value = false;
    }

    function onDrop(event) {
        isDragOver.value = false;
        const file = getImageFile(event.dataTransfer);
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
