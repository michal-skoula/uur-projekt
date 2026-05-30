<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

final class GallerySectionTemplate implements SectionTemplate
{
    public string $heading = '';

    public string $description = '';

    /** @var string[] */
    public array $gallery = [];

    /** @var string[] Full public URLs, passed to the Alpine lightbox. */
    public array $galleryUrls = [];

    public function prepareData(array $data): static
    {
        $this->heading = $data['heading'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->gallery = $data['gallery'] ?? [];
        $this->galleryUrls = array_map(
            fn (string $path): string => Storage::url($path),
            $this->gallery,
        );

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.gallery', [
            'heading' => $this->heading,
            'description' => $this->description,
            'gallery' => $this->gallery,
            'galleryUrls' => $this->galleryUrls,
        ]);
    }
}
