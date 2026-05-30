<section id="about" class="section lg:py-28 bg-white text-primary-darkest">
    <div class="max-content-width">

        {{-- Header row: text left, buttons right --}}
        <div class="grid md:grid-cols-2 grid-cols-1 items-end gap-8 mb-16">

            <div class="flex flex-col">
                <p class="sm:text-2xl text-xl font-semibold font-serif tracking-wider text-accent-ocean mb-1">
                    {{ $tagline }}
                </p>
                <h2 class="text-primary-darkest mb-3">{{ $title }}</h2>
                <div class="prose prose-sm max-w-none **:text-primary-darker">
                    {!! $description !!}
                </div>
            </div>

            <div class="md:ml-auto flex gap-3 items-center flex-wrap">
                @if ($buttonPrimary['text'] && $buttonPrimary['url'])
                    <a href="{{ $buttonPrimary['url'] }}" target="{{ $buttonPrimary['target'] }}" class="btn-secondary shadow-sm">
                        {{ $buttonPrimary['text'] }}
                    </a>
                @endif
                @if ($buttonSecondary['text'] && $buttonSecondary['url'])
                    <a href="{{ $buttonSecondary['url'] }}" target="{{ $buttonSecondary['target'] }}" class="btn-primary shadow-sm">
                        {{ $buttonSecondary['text'] }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Gallery + bubble --}}
        @if ($gallery)
            <div class="relative">
                <div class="grid lg:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-5">
                    @foreach ($gallery as $path)
                        <img
                            src="{{ Storage::url($path) }}"
                            alt=""
                            loading="lazy"
                            class="h-full w-full aspect-square rounded-lg object-cover"
                        />
                    @endforeach
                </div>

                @if ($bubble)
                    <div class="flex items-center justify-center text-center size-30 rounded-full bg-accent-yellow/80 rotate-[8deg] absolute -left-8 -top-8 pointer-events-none select-none">
                        <p class="text-primary-darker font-serif text-xl">{!! nl2br(e($bubble)) !!}</p>
                    </div>
                @endif
            </div>
        @endif

    </div>
</section>
