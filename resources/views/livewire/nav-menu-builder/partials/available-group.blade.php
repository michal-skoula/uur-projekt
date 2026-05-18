<div x-data="{ open: true }">
    <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
    >
        <span>{{ $group['label'] }}</span>
        <svg
            class="h-4 w-4 transition-transform duration-150"
            :class="{ 'rotate-180': open }"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <ul
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-init="Sortable.create($el, {
            group: { name: 'nav', pull: 'clone', put: false },
            sort: false,
            animation: 150,
        })"
        class="mt-1 space-y-1"
    >
        @foreach ($group['items'] as $item)
            <li
                data-page-id="{{ $item['id'] }}"
                data-page-title="{{ $item['title'] }}"
                class="flex cursor-grab select-none items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800"
            >
                <svg class="h-3.5 w-3.5 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                </svg>
                {{ $item['title'] }}
            </li>
        @endforeach
    </ul>
</div>
