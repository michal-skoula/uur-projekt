<div
    x-data="navMenuBuilder()"
    x-init="init()"
    class="grid grid-cols-1 gap-6 lg:grid-cols-3"
>
    {{-- Left column: Available items --}}
    <div class="lg:col-span-1">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4">
                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Available Pages
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Drag pages into the menu.
                </p>
            </div>
            <div class="space-y-2 px-6 pb-6">
                @foreach ($availableGroups as $group)
                    @include('livewire.nav-menu-builder.partials.available-group', ['group' => $group])
                @endforeach

                @if (empty($availableGroups) || collect($availableGroups)->every(fn ($g) => empty($g['items'])))
                    <p class="py-4 text-center text-sm text-gray-400 dark:text-gray-600">No pages found.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Right column: Nav menu tree --}}
    <div class="lg:col-span-2">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-start justify-between px-6 py-4">
                <div>
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Navigation Menu
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Reorder and nest items by dragging. Items nested inside others appear as dropdown children.
                    </p>
                </div>
                <button
                    type="button"
                    @click="save()"
                    class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    Save Menu
                </button>
            </div>
            <div class="px-6 pb-6">
                <ul
                    x-ref="menuRoot"
                    data-menu-list
                    class="min-h-20 space-y-1 rounded-lg border-2 border-dashed border-gray-200 p-2 dark:border-gray-700"
                >
                    @forelse ($menuStructure as $item)
                        @include('livewire.nav-menu-builder.partials.menu-item', [
                            'item'       => $item,
                            'pageTitles' => $pageTitles,
                        ])
                    @empty
                        <li class="flex h-16 items-center justify-center text-sm text-gray-400 dark:text-gray-600" data-empty-placeholder>
                            Drag pages here to build your menu.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@assets
<script src="{{ asset('js/sortable.min.js') }}"></script>
@endassets

@script
<script>
    function buildMenuItemHtml(pageId, pageTitle) {
        const escaped = pageTitle
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
                <span class="flex-1 text-sm font-medium text-gray-950 dark:text-white">${escaped}</span>
                <button type="button" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400" onclick="this.closest('li').remove()" aria-label="Remove">
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
            group: { name: 'nav', pull: true, put: true },
            handle: '.drag-handle',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onAdd(evt) {
                const li = evt.item;
                if (li.dataset.pageTitle !== undefined) {
                    li.innerHTML = buildMenuItemHtml(li.dataset.pageId, li.dataset.pageTitle);
                    li.classList.add('nav-menu-item');
                    const childList = li.querySelector('[data-menu-list]');
                    if (childList) initNestedSortable(childList);
                }
                const placeholder = ul.querySelector('[data-empty-placeholder]');
                if (placeholder) placeholder.remove();
            },
        });
        ul.querySelectorAll(':scope > li > [data-menu-list]').forEach(nested => {
            initNestedSortable(nested);
        });
    }

    function walkTree(ul) {
        const result = [];
        ul.querySelectorAll(':scope > li[data-page-id]').forEach(li => {
            const id = parseInt(li.dataset.pageId, 10);
            const childUl = li.querySelector(':scope > [data-menu-list]');
            result.push({ id, children: childUl ? walkTree(childUl) : [] });
        });
        return result;
    }

    Alpine.data('navMenuBuilder', () => ({
        init() {
            this.$nextTick(() => initNestedSortable(this.$refs.menuRoot));
        },
        save() {
            $wire.save(walkTree(this.$refs.menuRoot));
        },
    }));
</script>
@endscript
