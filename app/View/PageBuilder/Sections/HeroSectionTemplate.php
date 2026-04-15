<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class HeroSectionTemplate implements SectionTemplate
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): View
    {
        // fixme: i hate how this looks is done everything just ewwww ugly as FUCK
        //        $cta = is_array($data['cta'] ?? null) ? $data['cta'] : [];
        //        $image = is_string($data['image'] ?? null) ? $data['image'] : null;
        //
        return view('page-builder.sections.hero', [
            'heading' => is_string($data['heading'] ?? null) ? $data['heading'] : null,
            'description' => is_string($data['description'] ?? null) ? $data['description'] : null,
            'imageUrl' => 'img url',
            'ctaLabel' => 'label',
            'ctaUrl' => 'url',
        ]);
    }
}
