<section class="py-12 md:py-16 lg:py-24 px-4 sm:px-6 md:px-8 lg:px-12">
    <div class="max-w-6xl mx-auto grid gap-10 md:gap-14 {{ $imageUrl ? 'md:grid-cols-2 md:items-center' : '' }}">
        <div>
            @if ($heading)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight">
                    {!! $heading !!}
                </h1>
            @endif

            @if ($description)
                <p class="mt-6 text-lg text-gray-600 whitespace-pre-line">
                    {{ $description }}
                </p>
            @endif

            @if ($ctaLabel && $ctaUrl)
                <a href="{{ $ctaUrl }}"
                   class="mt-8 inline-flex items-center rounded-lg px-6 py-3 bg-gray-900 text-white font-medium hover:bg-gray-800 transition">
                    {{ $ctaLabel }}
                </a>
            @endif
        </div>

        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="w-full rounded-2xl shadow-lg">
        @endif
    </div>
</section>
