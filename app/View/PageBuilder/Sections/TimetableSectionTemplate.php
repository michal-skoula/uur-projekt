<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use App\Filament\Components\LinkInput;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

final class TimetableSectionTemplate implements SectionTemplate
{
    /** @var array{title: string, text: string} */
    public array $general = ['title' => '', 'text' => ''];

    /** @var array{title: string, text: string, button: array{text: string, url: string, target: string}} */
    public array $signup = ['title' => '', 'text' => '', 'button' => ['text' => '', 'url' => '#', 'target' => '_self']];

    /** @var array{title: string, detail: string} */
    public array $timetableSelector = ['title' => '', 'detail' => ''];

    /** @var array<int, array{name: string, imgUrl: string|null, pdfUrl: string|null}> */
    public array $timetables = [];

    public function prepareData(array $data): static
    {
        $this->general = [
            'title' => $data['general']['title'] ?? '',
            'text' => $data['general']['text'] ?? '',
        ];

        $link = LinkInput::resolve($data['signup']['button']['link'] ?? null);
        $this->signup = [
            'title' => $data['signup']['title'] ?? '',
            'text' => $data['signup']['text'] ?? '',
            'button' => [
                'text' => $data['signup']['button']['text'] ?? '',
                'url' => $link['url'],
                'target' => $link['target'],
            ],
        ];

        $this->timetableSelector = [
            'title' => $data['timetable_selector']['title'] ?? '',
            'detail' => $data['timetable_selector']['detail'] ?? '',
        ];

        $this->timetables = array_values(array_map(
            fn (array $t): array => [
                'name' => $t['name'] ?? '',
                'imgUrl' => isset($t['img']) && $t['img'] ? Storage::url($t['img']) : null,
                'pdfUrl' => isset($t['pdf']) && $t['pdf'] ? Storage::url($t['pdf']) : null,
            ],
            $data['timetable_selector']['timetables'] ?? []
        ));

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.timetable', [
            'general' => $this->general,
            'signup' => $this->signup,
            'timetableSelector' => $this->timetableSelector,
            'timetables' => $this->timetables,
        ]);
    }
}
