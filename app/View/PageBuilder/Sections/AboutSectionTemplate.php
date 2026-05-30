<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use App\Filament\Components\LinkInput;
use Illuminate\Contracts\View\View;

final class AboutSectionTemplate implements SectionTemplate
{
    public string $tagline = '';

    public string $title = '';

    public string $description = '';

    public ?string $bubble = null;

    /** @var array{text: string, url: string, target: string} */
    public array $buttonPrimary = ['text' => '', 'url' => '#', 'target' => '_self'];

    /** @var array{text: string, url: string, target: string} */
    public array $buttonSecondary = ['text' => '', 'url' => '#', 'target' => '_self'];

    /** @var string[] */
    public array $gallery = [];

    public function prepareData(array $data): static
    {
        $this->tagline = $data['tagline'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->bubble = $data['bubble'] ?? null;
        $this->gallery = $data['gallery'] ?? [];

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
