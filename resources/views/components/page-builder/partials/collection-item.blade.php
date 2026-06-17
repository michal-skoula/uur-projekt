<?php

use Livewire\Component as LivewireComponent;

new class extends LivewireComponent
{
    public string $collection;
    public int $itemId;
    public string $title;

    public function mount(string $collection, int $itemId, string $title): void
    {
        $this->collection = $collection;
        $this->itemId = $itemId;
        $this->title = $title;
    }
};

?>

<li
    data-collection="{{ $collection }}"
    data-page-id="{{ $itemId }}"
    data-page-title="{{ $title }}"
    class="flex cursor-grab select-none items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800"
>
    {{-- Draggable handle icon --}}
    <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
         viewBox="0 0 24 24" aria-hidden="true">
        <path
            d="M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM16 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
    </svg>

    {{ $title }}
</li>
