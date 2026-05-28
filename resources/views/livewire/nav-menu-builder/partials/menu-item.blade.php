<li data-page-id="{{ $item['id'] }}" class="nav-menu-item">
    <div class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
            </svg>
        </span>
        <span class="flex-1 text-sm font-medium text-gray-950 dark:text-white">
            {{ $pageTitles[$item['id']] ?? 'Unknown' }}
        </span>
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
    <ul class="ml-6 mt-1 min-h-4 space-y-1" data-menu-list>
        @foreach ($item['children'] ?? [] as $child)
            @include('livewire.nav-menu-builder.partials.menu-item', [
                'item'       => $child,
                'pageTitles' => $pageTitles,
            ])
        @endforeach
    </ul>
</li>
