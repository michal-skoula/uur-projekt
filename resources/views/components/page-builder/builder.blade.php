<?php

use App\Contracts\ContentCollectionItem;
use App\Contracts\ContentCollectionModel;
use App\Services\PageBuilderService;
use App\Settings\NavMenuSettings;
use Filament\Notifications\Notification;
use Livewire\Component as LivewireComponent;

new class extends LivewireComponent {
    /**
     * @var array<int, array{label: string, items: array<int, array{id: int, title: string}>}>
     */
    public array $availableGroups = [];

    /**
     * @var array<int, array{id: int, children: array<int, mixed>}>
     */
    public array $menuStructure = [];

    /**
     * @var array<int, string>
     */
    public array $pageTitles = [];

    /**
     * @var int Max nesting level allowed in the picker.
     */
    public int $maxDepth;


    public function mount(NavMenuSettings $settings, int $maxDepth = 2): void
    {
        $this->availableGroups = PageBuilderService::buildAvailableGroups(savePageTitlesTo: $this->pageTitles);
        $this->maxDepth = $maxDepth;
        $this->menuStructure = $settings->structure;
    }

    /**
     * @param array<int, array{id: int, children: array<int, mixed>}> $structure
     */
    public function save(array $structure, NavMenuSettings $settings): void
    {
        $ids = PageBuilderService::findIds($structure);
        $validIds = collect();

        /** @var array<string, class-string> $collections */
        $collections = config('content-collections.collections', []);
        $disabled = config('content-collections.disabled', []);

        foreach ($collections as $slug => $class) {
            if (in_array($slug, $disabled) || !is_a($class, ContentCollectionItem::class, true)) {
                continue;
            }

            /** @var class-string<ContentCollectionModel> $class */
            $validIds = $validIds->merge($class::whereIn('id', $ids)->pluck('id'));
        }

        $invalidIds = array_diff($ids, $validIds->all());

        if ($invalidIds) {
            Notification::make()
                ->title(__('settings/nav-menu.builder.notifications.save_failed_title'))
                ->body(__('settings/nav-menu.builder.notifications.save_failed_body', [
                    'count' => count($invalidIds),
                    'ids' => implode(', ', $invalidIds),
                ]))
                ->danger()
                ->send();

            return;
        }

        $settings->structure = $structure;
        $settings->save();

        // Needed to update the menu on the frontend too, not just db
        $this->menuStructure = $structure;

        Notification::make()
            ->title(__('settings/nav-menu.builder.notifications.saved_title'))
            ->success()
            ->send();
    }


};
?>

<div x-data="menuBuilder" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Left column: Available items --}}
    <div class="lg:col-span-1">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4">
                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ __('settings/nav-menu.builder.available_items.title') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('settings/nav-menu.builder.available_items.description') }}
                </p>
            </div>
            <div class="space-y-2 px-6 pb-6">

                @foreach ($availableGroups as $group)
                    <livewire:page-builder.partials.collection-group :group="$group"/>
                @endforeach

                @if (empty($availableGroups) || collect($availableGroups)->every(fn ($g) => empty($g['items'])))
                    <p class="py-4 text-center text-sm text-gray-400 dark:text-gray-600">{{ __('settings/nav-menu.builder.available_items.empty') }}</p>
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
                        {{ __('settings/nav-menu.builder.menu.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('settings/nav-menu.builder.menu.description') }}
                    </p>
                </div>
                <button
                    type="button"
                    @click="save()"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    {{ __('settings/nav-menu.builder.menu.save') }}
                </button>
            </div>
            <div class="px-6 pb-6">
                <ul
                    id="menu-root"
                    data-menu-list
                    class="min-h-20 rounded-lg border-2 border-dashed border-gray-200 p-2 dark:border-gray-700"
                >
                    @forelse ($menuStructure as $item)
                        <x-page-builder.partials.menu-item :item="$item" :page-titles="$pageTitles"/>
                    @empty
                        <li class="flex h-16 items-center justify-center text-sm text-gray-400 dark:text-gray-600"
                            data-empty-placeholder>
                            {{ __('settings/nav-menu.builder.menu.empty') }}
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>


    {{-- Blade markup needs to render server side, this pre-renders it to be injected via JS when building the menu --}}
    <template id="nav-menu-item-template">
        <x-page-builder.partials.menu-item :item="['id' => 0, 'children' => []]" :page-titles="[0 => '']"/>
    </template>
</div>


@assets
<script src="{{ asset('js/sortable.min.js') }}"></script>
@endassets

@script
<script>
    const MAX_DEPTH = {{ $maxDepth }};

    function getListDepth(ul) {
        let depth = 0;
        let el = ul.parentElement;
        while (el) {
            if (el.matches('[data-menu-list]')) depth++;
            el = el.parentElement;
        }
        return depth;
    }

    function cloneMenuItem(pageId, pageTitle) {
        const template = document.getElementById('nav-menu-item-template');
        const li = template.content.firstElementChild.cloneNode(true);
        li.dataset.pageId = pageId;
        li.querySelector('[data-item-title]').textContent = pageTitle;
        return li;
    }

    function initNestedSortable(ul) {
        Sortable.create(ul, {
            group: {name: 'nav', pull: true, put: true},
            handle: '.drag-handle',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.6,
            ghostClass: 'menu-drop-indicator',
            onMove(evt) {
                return getListDepth(evt.to) < MAX_DEPTH;
            },
            onAdd(evt) {
                const draggedItem = evt.item;

                // checking whether the dragged object came from the left column (pageTitle is left-column specific)
                if (draggedItem.dataset.pageTitle !== undefined) {
                    const newLi = cloneMenuItem(draggedItem.dataset.pageId, draggedItem.dataset.pageTitle);
                    draggedItem.replaceWith(newLi);
                    const childList = newLi.querySelector('[data-menu-list]');
                    if (childList) initNestedSortable(childList);
                }

                // Removing placeholder when an item is dropped in
                const placeholder = ul.querySelector('[data-empty-placeholder]');
                if (placeholder) placeholder.remove();
            },
        });

        // Recursive initialization
        ul.querySelectorAll(':scope > li > [data-menu-list]').forEach(nested => {
            initNestedSortable(nested);
        });
    }

    function walkTree(ul) {
        const result = [];
        ul.querySelectorAll(':scope > li[data-page-id]').forEach(li => {
            const id = parseInt(li.dataset.pageId, 10);
            const childUl = li.querySelector(':scope > [data-menu-list]');
            result.push({id, children: childUl ? walkTree(childUl) : []});
        });
        return result;
    }

    // Note: DOMContentLoaded doesn't work here, pretty sure livewire initializes itself after all DOM content
    document.addEventListener('livewire:initialized', () => {
        initNestedSortable(document.getElementById('menu-root'));
    });

    Alpine.data('menuBuilder', () => ({
        save() {
            $wire.save(walkTree(document.getElementById('menu-root')));
        },
    }));
</script>
@endscript
