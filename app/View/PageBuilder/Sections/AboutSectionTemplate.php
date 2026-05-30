<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class AboutSectionTemplate implements SectionTemplate
{
    public string $tagline = '';

    public string $title = '';

    public string $description = '';

    public ?string $bubble = null;

    /** @var array{text: string, url: string} */
    public array $buttonPrimary = ['text' => '', 'url' => ''];

    /** @var array{text: string, url: string} */
    public array $buttonSecondary = ['text' => '', 'url' => ''];

    /** @var string[] */
    public array $gallery = [];

    public function prepareData(array $data): static
    {
        $this->tagline = $data['tagline'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->bubble = $data['bubble'] ?? null;
        $this->buttonPrimary = [
            'text' => $data['button_primary']['text'] ?? '',
            'url' => $data['button_primary']['url'] ?? '',
        ];
        $this->buttonSecondary = [
            'text' => $data['button_secondary']['text'] ?? '',
            'url' => $data['button_secondary']['url'] ?? '',
        ];
        $this->gallery = $data['gallery'] ?? [];

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.about', [
            'tagline' => $this->tagline,
            'title' => $this->title,
            'description' => $this->description,
            'bubble' => $this->bubble,
            'buttonPrimary' => $this->buttonPrimary,
            'buttonSecondary' => $this->buttonSecondary,
            'gallery' => $this->gallery,
        ]);
    }
}
