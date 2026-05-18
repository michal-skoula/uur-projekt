<?php

namespace App\Livewire;

use App\Models\Page;
use App\Settings\NavMenuSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NavMenuBuilder extends Component
{
    /**
     * @var list<array{label: string, items: list<array{id: int, title: string}>}>
     */
    public array $availableGroups = [];

    /**
     * @var list<array{id: int, children: list<mixed>}>
     */
    public array $menuStructure = [];

    /**
     * @var array<int, string>
     */
    public array $pageTitles = [];

    public function mount(NavMenuSettings $settings): void
    {
        $pages = Page::orderBy('title')->get(['id', 'title']);

        $this->availableGroups = [
            [
                'label' => 'Pages',
                'items' => array_values($pages->map(fn (Page $page): array => [
                    'id' => $page->id,
                    'title' => $page->title,
                ])->all()),
            ],
        ];

        $this->pageTitles = $pages->pluck('title', 'id')->all();
        $this->menuStructure = $settings->structure;
    }

    /**
     * @param  list<array{id: int, children: list<mixed>}>  $structure
     */
    public function save(array $structure, NavMenuSettings $settings): void
    {
        $ids = [];
        $this->collectIds($structure, $ids);

        $validIds = Page::whereIn('id', $ids)->pluck('id')->all();
        $invalidIds = array_diff($ids, $validIds);

        if ($invalidIds) {
            Notification::make()
                ->title('Invalid page IDs: '.implode(', ', $invalidIds))
                ->danger()
                ->send();

            return;
        }

        $settings->structure = $structure;
        $settings->save();

        Notification::make()->title('Menu saved')->success()->send();
    }

    /**
     * @param  list<array{id: int, children: list<mixed>}>  $items
     * @param  list<int>  $ids
     */
    private function collectIds(array $items, array &$ids): void
    {
        foreach ($items as $item) {
            $ids[] = (int) $item['id'];

            if (! empty($item['children'])) {
                $this->collectIds($item['children'], $ids);
            }
        }
    }

    public function render(): View|Factory
    {
        return view('livewire.nav-menu-builder');
    }
}
