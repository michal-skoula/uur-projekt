<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class DanceSectionTemplate implements SectionTemplate
{
    public string $heading = '';

    public string $motto = '';

    /** @var string[] */
    public array $danceStyles = [];

    public string $textLeft = '';

    public string $textRight = '';

    public function prepareData(array $data): static
    {
        $this->heading = $data['heading'] ?? '';
        $this->motto = $data['motto'] ?? '';
        $this->danceStyles = $data['dance_styles'] ?? [];
        $this->textLeft = $data['text_left'] ?? '';
        $this->textRight = $data['text_right'] ?? '';

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.dance', [
            'heading' => $this->heading,
            'motto' => $this->motto,
            'danceStyles' => $this->danceStyles,
            'textLeft' => $this->textLeft,
            'textRight' => $this->textRight,
        ]);
    }
}
