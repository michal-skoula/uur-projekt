<section
    id="gallery"
    class="section"
    x-data="{
        open: false,
        index: 0,
        images: @js($galleryUrls),
        show(i) {
            this.index = i;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            document.body.style.overflow = '';
        },
        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; },
        next() { this.index = (this.index + 1) % this.images.length; },
    }"
    @keydown.escape.window="open && close()"
    @keydown.arrow-left.window="open && prev()"
    @keydown.arrow-right.window="open && next()"
>
    <div class="max-content-width">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-1">{{ $heading }}</h2>
            @if ($description)
                <p>{{ $description }}</p>
            @endif
        </div>

        {{-- Grid --}}
        @if ($gallery)
            <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-6">
                @foreach ($gallery as $i => $mediaItem)
                    <button
                        type="button"
                        class="relative rounded-lg overflow-clip cursor-zoom-in focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-yellow"
                        @click="show({{ $i }})"
                        :aria-label="'Zobrazit fotografii {{ $i + 1 }}'"
                    >
                        <img
                            src="{{ $mediaItem->medium_url }}"
                            srcset="{{ $mediaItem->thumbnail_url }} 200w, {{ $mediaItem->medium_url }} 640w"
                            sizes="(min-width: 640px) 250px, 100vw"
                            alt="{{ $mediaItem->alt ?? '' }}"
                            loading="lazy"
                            class="h-full w-full object-cover aspect-square"
                        >
                    </button>
                @endforeach
            </div>
        @endif

    </div>

    {{-- Lightbox overlay --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
        @click.self="close()"
        :inert="!open"
        aria-modal="true"
        role="dialog"
    >
        {{-- Prev --}}
        <button
            type="button"
            class="absolute left-4 top-1/2 -translate-y-1/2 p-3 text-white/70 hover:text-white transition-colors"
            @click="prev()"
            aria-label="Předchozí fotografie"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-8">
                <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/>
            </svg>
        </button>

        {{-- Image --}}
        <img
            :src="images[index]"
            :alt="'Fotografie ' + (index + 1) + ' z ' + images.length"
            class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >

        {{-- Next --}}
        <button
            type="button"
            class="absolute right-4 top-1/2 -translate-y-1/2 p-3 text-white/70 hover:text-white transition-colors"
            @click="next()"
            aria-label="Následující fotografie"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-8">
                <path d="M400-240 160-480l240-240 56 56-184 184 184 184-56 56Zm264 0L424-480l240-240 56 56-184 184 184 184-56 56Z"/>
            </svg>
        </button>

        {{-- Close --}}
        <button
            type="button"
            class="absolute top-4 right-4 p-2 text-white/70 hover:text-white transition-colors"
            @click="close()"
            aria-label="Zavřít"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-7">
                <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
            </svg>
        </button>

        {{-- Counter --}}
        <p class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/60 text-sm tabular-nums" x-text="(index + 1) + ' / ' + images.length"></p>
    </div>

</section>
