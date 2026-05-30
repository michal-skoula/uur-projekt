<section {{ $attributes->merge(['class' => 'section bg-primary-darkest **:text-gray']) }}>
    <div class="max-content-width post-container-styles">
        {{ $slot }}
    </div>
</section>
