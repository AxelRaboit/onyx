export function decodeHtml(html) {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = html;
    return textarea.value;
}

export function truncate(text, maxLength) {
    return text.length > maxLength ? text.slice(0, maxLength) + '…' : text;
}

export function slugify(text) {
    return text
        .toLowerCase()
        .replace(/<[^>]+>/g, '')
        .replace(/[^\w]+/g, '-')
        .replace(/(^-|-$)/g, '');
}
