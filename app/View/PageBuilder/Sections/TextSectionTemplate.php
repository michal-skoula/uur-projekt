<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class TextSectionTemplate implements SectionTemplate
{
    public string $tagline = '';

    public string $heading = '';

    public string $body = '';

    public function prepareData(array $data): static
    {
        $this->tagline = $data['tagline'] ?? '';
        $this->heading = $data['heading'] ?? '';
        $this->body = $data['body'] ?? '';

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.text', [
            'tagline' => $this->tagline,
            'heading' => $this->heading,
            'body' => $this->body,
        ]);
    }
}
