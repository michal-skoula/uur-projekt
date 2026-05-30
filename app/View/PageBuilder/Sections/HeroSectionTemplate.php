<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;

final class HeroSectionTemplate implements SectionTemplate
{
    public string $title = '';

    public string $description = '';

    public ?string $backgroundImg = null;

    public ?string $backgroundVideo = null;

    public ?string $bubble = null;

    /** @var array{text: string, url: string} */
    public array $buttonPrimary = ['text' => '', 'url' => ''];

    /** @var array{text: string, url: string} */
    public array $buttonSecondary = ['text' => '', 'url' => ''];

    public function prepareData(array $data): static
    {
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->backgroundImg = $data['background']['img'] ?? null;
        $this->backgroundVideo = $data['background']['video'] ?? null;
        $this->bubble = $data['bubble'] ?? null;
        $this->buttonPrimary = [
            'text' => $data['button_primary']['text'] ?? '',
            'url' => $data['button_primary']['url'] ?? '',
        ];
        $this->buttonSecondary = [
            'text' => $data['button_secondary']['text'] ?? '',
            'url' => $data['button_secondary']['url'] ?? '',
        ];

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.hero', [
            'title' => $this->title,
            'description' => $this->description,
            'backgroundImg' => $this->backgroundImg,
            'backgroundVideo' => $this->backgroundVideo,
            'bubble' => $this->bubble,
            'buttonPrimary' => $this->buttonPrimary,
            'buttonSecondary' => $this->buttonSecondary,
        ]);
    }
}
