<section id="dance" class="section bg-white text-primary-darker">
    <div class="max-w-5xl mx-auto">

        <h2 class="mb-6 text-center text-primary-darkest">{{ $heading }}</h2>

        {{-- Dance style pills --}}
        @if ($danceStyles)
            <div class="sm:flex sm:flex-wrap sm:justify-center max-sm:grid xs:grid-cols-2 grid-cols-1 gap-3 items-center mb-16">
                @foreach ($danceStyles as $style)
                    <p class="px-4 py-3 text-center rounded-3xl bg-primary-dark text-white!">
                        {{ $style }}
                    </p>
                @endforeach
            </div>

            {{-- Two-column rich text --}}
            <div class="grid md:grid-cols-2 grid-cols-1 gap-x-16 gap-y-4">
                <div class="post-container-styles">
                    {!! $textLeft !!}
                </div>

                <div class="post-container-styles">
                    {!! $textRight !!}
                    @if ($motto)
                        <p class="mt-3 font-serif font-bold text-accent-ocean text-2xl">{{ $motto }}</p>
                    @endif
                </div>
            </div>
        @endif

    </div>
</section>
