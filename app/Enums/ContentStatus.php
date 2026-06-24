<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContentStatus: string implements HasColor, HasLabel
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case DISABLED = 'disabled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PUBLISHED => __('enums/content-status.published'),
            self::DRAFT => __('enums/content-status.draft'),
            self::DISABLED => __('enums/content-status.disabled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::DRAFT => 'gray',
            self::DISABLED => 'danger',
        };
    }
}
