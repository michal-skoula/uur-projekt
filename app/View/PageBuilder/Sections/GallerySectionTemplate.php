<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;

final class GallerySectionTemplate implements SectionTemplate
{
    public string $heading = '';

    public string $description = '';

    /** @var list<Media> */
    public array $gallery = [];

    /** @var list<string> Full public URLs, passed to the Alpine lightbox. */
    public array $galleryUrls = [];

    public function prepareData(array $data): static
    {
        $this->heading = $data['heading'] ?? '';
        $this->description = $data['description'] ?? '';

        $mediaIds = $data['gallery'] ?? [];
        $mediaById = Media::whereIn('id', $mediaIds)->get()->keyBy('id');

        $gallery = [];
        foreach ($mediaIds as $id) {
            $media = $mediaById->get($id);
            if ($media instanceof Media) {
                $gallery[] = $media;
            }
        }

        $this->gallery = $gallery;
        $this->galleryUrls = array_map(fn (Media $media): string => $media->url, $this->gallery);

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
