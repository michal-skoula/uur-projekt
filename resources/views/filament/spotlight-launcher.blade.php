@php
    use Wezlo\FilamentSearchSpotlight\FilamentSearchSpotlightPlugin;

    $placeholder = FilamentSearchSpotlightPlugin::current()?->getPlaceholder()
        ?? __('filament-search-spotlight::spotlight.placeholder');
@endphp

<button
    type="button"
    x-data
    x-on:click="
        const spotlight = window.Livewire.all().find((component) => component.name === 'filament-search-spotlight');

        if (spotlight) {
            window.Alpine.$data(spotlight.el).open();
        }
    "
    class="
        flex items-center sm:gap-x-20 gap-x-6 rounded-lg px-4 py-2.5 text-sm text-gray-500 ring-1 ring-gray-950/10 transition
        duration-75 hover:text-gray-700 hover:ring-gray-950/20 dark:text-gray-400 dark:ring-white/10
        dark:hover:text-gray-300 dark:hover:ring-white/20"
>

    <div class="flex gap-2">
        <x-filament::icon
            icon="heroicon-m-magnifying-glass"
            class="size-5"
        />
        <span>{{ $placeholder }}</span>
    </div>


    <kbd
        x-text="navigator.platform?.toLowerCase().includes('mac') ? '⌘K' : 'Ctrl K'"
        class="ms-6 rounded-md bg-gray-100 px-1.5 py-0.5 font-sans text-xs text-gray-400 dark:bg-white/10 dark:text-gray-400"
    ></kbd>
</button>
