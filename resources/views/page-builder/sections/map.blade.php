<section id="map" class="section bg-primary-darkest text-white">
    <div class="max-content-width">
        <div class="grid md:grid-cols-[2fr_3fr] grid-cols-1 lg:gap-12 md:gap-8 gap-6 rounded-lg bg-primary-darker border border-primary-outline lg:p-8 md:p-6 p-4">

            <div>
                <h2 class="mb-3 text-gradient-secondary">{{ $title }}</h2>
                <div class="text-gray post-container-styles">
                    {!! $text !!}
                </div>
            </div>

            <div>
                @if ($mapUrl)
                    <iframe
                        src="{{ $mapUrl }}"
                        class="rounded-lg w-full aspect-video"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                @else
                    <p class="text-gray">Chybí odkaz na mapu</p>
                @endif
            </div>

        </div>
    </div>
</section>
