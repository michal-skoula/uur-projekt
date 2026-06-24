<?php

namespace App\Observers;

use App\Contracts\ConfiguresNavBuilder;
use App\Contracts\ContentCollectionModel;
use Illuminate\Support\Facades\Config;

class ContentCollectionObserver
{
    public function deleted(ContentCollectionModel $contentCollectionModel): void
    {
        $this->removeDeletedPageFromPageBuilder($contentCollectionModel);
    }

    private function removeDeletedPageFromPageBuilder(ContentCollectionModel $record): void
    {
        /** @var list<class-string<ConfiguresNavBuilder>> $navBuilderSettings */
        $navBuilderSettings = Config::array('settings.menu_configuration_settings');

        foreach ($navBuilderSettings as $settingClass) {
            $setting = app($settingClass);

            if (! $setting->containsItem($record)) {
                continue;
            }

            $setting->removeItem($record);
        }
    }
}
