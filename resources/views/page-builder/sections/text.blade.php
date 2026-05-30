<section id="text" class="section bg-primary-darkest text-gray">
    <div class="max-content-width">

        @if ($tagline)
            <p class="sm:text-2xl text-xl font-semibold font-serif tracking-wider text-gradient-secondary mb-2">
                {{ $tagline }}
            </p>
        @endif

        <h2 class="mb-6">{{ $heading }}</h2>

        <div class="post-container-styles">
            {!! $body !!}
        </div>

    </div>
</section>
