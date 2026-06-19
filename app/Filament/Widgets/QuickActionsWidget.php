<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Settings\NavMenuSettingsPage;
use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\Pages\Pages\CreatePage;
use Awcodes\Curator\Resources\Media\MediaResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static bool $isDiscovered = false;

    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Action>
     */
    public function getWidgetActions(): array
    {
        return [
            Action::make('create_news')
                ->label(__('dashboard.quick_actions.create_news'))
                ->icon(Heroicon::PlusCircle)
                ->color('gray')
                ->button()
                ->color('primary')
                ->url(CreateNews::getUrl()),

            Action::make('create_page')
                ->label(__('dashboard.quick_actions.create_page'))
                ->icon(Heroicon::DocumentPlus)
                ->button()
                ->color('gray')
                ->url(CreatePage::getUrl()),

            Action::make('view_files')
                ->label(__('dashboard.quick_actions.view_files'))
                ->icon(Heroicon::Folder)
                ->color('gray')
                ->button()
                ->url(MediaResource::getUrl('index')),

            Action::make('edit_navigation')
                ->label(__('dashboard.quick_actions.edit_navigation'))
                ->icon(Heroicon::Bars3)
                ->color('gray')
                ->button()
                ->url(NavMenuSettingsPage::getUrl()),
        ];
    }
}
