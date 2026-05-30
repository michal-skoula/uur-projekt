<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class MapSectionTemplate implements SectionTemplate
{
    public string $title = '';

    public string $text = '';

    public string $mapUrl = '';

    public function prepareData(array $data): static
    {
        $this->title = $data['title'] ?? '';
        $this->text = $data['text'] ?? '';
        $this->mapUrl = $data['map_url'] ?? '';

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.map', [
            'title' => $this->title,
            'text' => $this->text,
            'mapUrl' => $this->mapUrl,
        ]);
    }
}
