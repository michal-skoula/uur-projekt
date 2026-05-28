import Sortable from 'sortablejs';

window.Sortable = Sortable;

// Expose globally so Blade partials' x-init expressions can access it
window.Sortable = Sortable;

function buildMenuItemHtml(pageId, pageTitle) {
    const escapedTitle = pageTitle
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    return `
        <div class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                </svg>
            </span>
            <span class="flex-1 text-sm font-medium text-gray-950 dark:text-white">${escapedTitle}</span>
            <button
                type="button"
                class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                onclick="this.closest('li').remove()"
                aria-label="Remove"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <ul class="ml-6 mt-1 min-h-4 space-y-1" data-menu-list></ul>
    `;
}

function initNestedSortable(ul) {
    Sortable.create(ul, {
        group: {
            name: 'nav',
            pull: true,
            put: true,
        },
        handle: '.drag-handle',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onAdd(evt) {
            const li = evt.item;
            const pageId = li.dataset.pageId;
            const pageTitle = li.dataset.pageTitle;

            // Item dragged from the left panel — transform clone into a full menu item
            if (pageTitle !== undefined) {
                li.innerHTML = buildMenuItemHtml(pageId, pageTitle);
                li.classList.add('nav-menu-item');

                const childList = li.querySelector('[data-menu-list]');
                if (childList) {
                    initNestedSortable(childList);
                }
            }

            // Remove the empty-state placeholder if it exists
            const placeholder = ul.querySelector('[data-empty-placeholder]');
            if (placeholder) {
                placeholder.remove();
            }
        },
    });

    // Recurse into already-rendered children (for server-rendered items on load)
    ul.querySelectorAll(':scope > li > [data-menu-list]').forEach(nested => {
        initNestedSortable(nested);
    });
}

function walkTree(ul) {
    const result = [];

    ul.querySelectorAll(':scope > li[data-page-id]').forEach(li => {
        const id = parseInt(li.dataset.pageId, 10);
        const childUl = li.querySelector(':scope > [data-menu-list]');
        const children = childUl ? walkTree(childUl) : [];

        result.push({ id, children });
    });

    return result;
}

document.addEventListener('alpine:init', () => {
    Alpine.data('navMenuBuilder', () => ({
        init() {
            this.$nextTick(() => initNestedSortable(this.$refs.menuRoot));
        },

        save() {
            const structure = walkTree(this.$refs.menuRoot);
            this.$wire.save(structure);
        },
    }));
});
