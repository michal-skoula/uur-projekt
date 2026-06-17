<section id="hero" class="bg-primary-darkest text-white">
    <div class="mx-auto max-w-screen-2xl max-h-280 h-[85vh] min-h-160 md:min-h-200 sm:px-8 sm:py-8 px-3 py-4">
        <div class="relative h-full w-full border-2 border-primary-outline rounded-2xl overflow-clip">

            {{-- Background media --}}
            <div class="absolute inset-0">
                @if ($backgroundVideo)
                    <video
                        fetchpriority="high"
                        autoplay
                        muted
                        loop
                        playsinline
                        tabindex="-1"
                        @if ($backgroundImg) poster="{{ $backgroundImg->url }}" @endif
                        class="w-full h-full object-cover"
                    >
                        <source src="{{ $backgroundVideo->url }}">
                    </video>
                @elseif ($backgroundImg)
                    <img
                        fetchpriority="high"
                        loading="eager"
                        src="{{ $backgroundImg->url }}"
                        srcset="{{ $backgroundImg->medium_url }} 640w, {{ $backgroundImg->large_url }} 1024w, {{ $backgroundImg->url }} 1920w"
                        sizes="100vw"
                        alt="{{ $backgroundImg->alt ?? '' }}"
                        width="{{ $backgroundImg->width }}"
                        height="{{ $backgroundImg->height }}"
                        class="w-full h-full object-cover"
                    >
                @endif
            </div>

            {{-- Darkening overlay --}}
            <div class="absolute inset-0 bg-black/50"></div>

            {{-- Content --}}
            <div class="absolute inset-0 flex sm:p-8 p-3">
                <div class="w-full self-end flex items-end flex-wrap gap-6 justify-between">
                    <div>
                        <h1 class="xl:text-title sm:text-6xl text-4xl tracking-wide leading-[100%] text-gradient-secondary mb-3">
                            {!! nl2br(e($title)) !!}
                        </h1>
                        <p class="text-gray">{{ $description }}</p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a
                            href="{{ $buttonPrimary['url'] }}"
                            target="{{ $buttonPrimary['target'] }}"
                            class="btn-primary md:px-10 md:py-5 py-4 md:motion-safe:hover:px-14 md:motion-safe:focus:px-14"
                        >
                            {{ $buttonPrimary['text'] }}
                        </a>

                        <a
                            href="{{ $buttonSecondary['url'] }}"
                            target="{{ $buttonSecondary['target'] }}"
                            class="btn-secondary md:px-10 md:py-5 py-4 md:motion-safe:hover:px-14 md:motion-safe:focus:px-14"
                        >
                            {{ $buttonSecondary['text'] }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bubble badge --}}
            @if ($bubble)
                <p class="size-40 inline-flex items-center justify-center text-center rounded-full bg-accent-aquamarine/80 text-primary-darker font-serif sm:text-2xl text-xl rotate-12 md:top-1/3 md:-left-4 top-1/7 -right-4 absolute pointer-events-none">
                    {!! nl2br(e($bubble)) !!}
                </p>
            @endif

        </div>
    </div>
</section>
