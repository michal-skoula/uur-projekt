<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use App\Filament\Components\LinkInput;
use Illuminate\Contracts\View\View;

final class HeroSectionTemplate implements SectionTemplate
{
    public string $title = '';

    public string $description = '';

    public ?string $backgroundImg = null;

    public ?string $backgroundVideo = null;

    public ?string $bubble = null;

    /** @var array{text: string, url: string, target: string} */
    public array $buttonPrimary = ['text' => '', 'url' => '#', 'target' => '_self'];

    /** @var array{text: string, url: string, target: string} */
    public array $buttonSecondary = ['text' => '', 'url' => '#', 'target' => '_self'];

    public function prepareData(array $data): static
    {
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->backgroundImg = $data['background']['img'] ?? null;
        $this->backgroundVideo = $data['background']['video'] ?? null;
        $this->bubble = $data['bubble'] ?? null;

        $primaryLink = LinkInput::resolve($data['button_primary']['link'] ?? null);
        $this->buttonPrimary = [
            'text' => $data['button_primary']['text'] ?? '',
            'url' => $primaryLink['url'],
            'target' => $primaryLink['target'],
        ];

        $secondaryLink = LinkInput::resolve($data['button_secondary']['link'] ?? null);
        $this->buttonSecondary = [
            'text' => $data['button_secondary']['text'] ?? '',
            'url' => $secondaryLink['url'],
            'target' => $secondaryLink['target'],
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
