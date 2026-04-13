<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class TextSectionTemplate implements SectionTemplate
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): View
    {
        return view('page-builder.sections.text', [
            'content' => $data['content'] ?? null,
        ]);
    }
}
