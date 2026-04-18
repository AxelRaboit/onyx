import { watchEffect } from 'vue';

/**
 * Adds a drag-to-resize handle on every image in the note preview.
 * On drag end, updates editContent to reflect the new width using
 * Obsidian syntax: ![alt|300](url)
 */
export function useNoteImageResize(previewContainer, renderedContent, editContent) {
    // Track both the container (becomes available when preview opens)
    // and the content (changes when note changes) so handles are always attached.
    watchEffect(() => {
        const container = previewContainer.value;
        void renderedContent.value; // declare dependency
        if (!container) return;
        setTimeout(() => attachHandles(), 0);
    });

    function attachHandles() {
        const container = previewContainer.value;
        if (!container) return;

        container.querySelectorAll('img').forEach((img) => {
            if (img.dataset.resizeAttached) return;
            img.dataset.resizeAttached = 'true';

            // Wrap in a relative container if not already
            if (img.parentElement?.dataset.resizeWrapper !== 'true') {
                const wrapper = document.createElement('span');
                wrapper.dataset.resizeWrapper = 'true';
                wrapper.style.cssText = 'position:relative; display:inline-block; line-height:0;';
                img.parentNode.insertBefore(wrapper, img);
                wrapper.appendChild(img);
            }

            const handle = document.createElement('span');
            handle.title = 'Redimensionner';
            handle.style.cssText = `
                position: absolute;
                bottom: 4px;
                right: 4px;
                width: 14px;
                height: 14px;
                background: rgba(99,102,241,0.85);
                border-radius: 3px;
                cursor: se-resize;
                opacity: 0;
                transition: opacity 150ms;
                z-index: 10;
            `;

            img.parentElement.appendChild(handle);

            // Show handle on hover
            img.parentElement.addEventListener('mouseenter', () => {
                handle.style.opacity = '1';
            });
            img.parentElement.addEventListener('mouseleave', () => {
                handle.style.opacity = '0';
            });

            handle.addEventListener('mousedown', (event) => onHandleMousedown(event, img));
        });
    }

    function onHandleMousedown(event, img) {
        event.preventDefault();
        event.stopPropagation();

        const startX = event.clientX;
        const startWidth = img.naturalWidth && img.width ? img.width : img.getBoundingClientRect().width;

        function onMousemove(moveEvent) {
            const newWidth = Math.max(40, Math.round(startWidth + moveEvent.clientX - startX));
            img.width = newWidth;
        }

        function onMouseup() {
            document.removeEventListener('mousemove', onMousemove);
            document.removeEventListener('mouseup', onMouseup);
            updateMarkdown(img);
        }

        document.addEventListener('mousemove', onMousemove);
        document.addEventListener('mouseup', onMouseup);
    }

    function updateMarkdown(img) {
        const src = img.getAttribute('src');
        const newWidth = img.width;

        if (!src || !editContent.value) return;

        // Match ![alt|oldDim](src) or ![alt](src) and replace with ![alt|newWidth](src)
        const escaped = src.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const pattern = new RegExp(`!\\[([^\\]]*)\\]\\(${escaped}\\)`, 'g');

        editContent.value = editContent.value.replace(pattern, (_, alt) => {
            // Strip any existing dimension from alt: "myalt|200" → "myalt"
            const cleanAlt = alt.replace(/\|[\dx]+$/, '');
            return `![${cleanAlt}|${newWidth}](${src})`;
        });
    }
}
