<?php

use Livewire\Component as LivewireComponent;

new class extends LivewireComponent
{
    public array $group;

    public function mount(array $group): void
    {
        $this->group = $group;
    }
};
?>

<div x-data="{ open: true }">
    <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
    >
        <span>{{ $group['label'] }}</span>
        {{-- Chevron Icon --}}
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
            <livewire:page-builder.partials.collection-item
                :collection="$item['collection']"
                :item-id="$item['id']"
                :title="$item['title']"
            />
        @endforeach
    </ul>
</div>
