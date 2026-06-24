<?php

namespace App\Observers;

use App\Contracts\ContentCollectionModel;
use App\Contracts\ConfiguresNavBuilder;
use Illuminate\Support\Facades\Config;

class ContentCollectionObserver
{

    public function deleted(ContentCollectionModel $contentCollectionModel): void
    {
        $this->removeDeletedPageFromPageBuilder($contentCollectionModel);
    }

    private function removeDeletedPageFromPageBuilder(ContentCollectionModel $record): void
    {
        /** @var ConfiguresNavBuilder[] $navBuilderSettings */
        $navBuilderSettings = Config::array('settings.menu_configuration_settings');

        foreach($navBuilderSettings as $setting)
        {
            if(! $setting->containsPage($record))
                continue;

            $setting->removePage($record);
        }

    }
}
