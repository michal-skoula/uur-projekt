<?php

namespace App\Filament\Components;

use App\Models\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * A reusable link picker that handles three variants:
 *   - 'page'     → searchable dropdown of CMS pages; resolves to /{slug}, target _self
 *   - 'external' → free-text URL (https://...); resolves as-is, target _blank
 *   - 'local'    → free-text path, anchors supported (/page#section); target _self
 *
 * Usage in a schema:
 *   LinkInput::make('button_primary.link')
 *
 * Stored shape: { type: 'page'|'external'|'local', url: <int id> | <string url> }
 * The `url` key holds a page ID when type = 'page', a URL string otherwise.
 */
final class LinkInput
{
    /**
     * Returns a two-column Grid that looks like a single joined input.
     *
     * The type Select sits in column 1, the URL input/page picker in column 2.
     * Gap is zeroed on the Grid and the touching border-radii are removed so
     * the two controls appear as one compound field.
     *
     * Both the page Select and URL TextInput share the `{$name}.url` statePath.
     * Only one is visible at a time. CSS Grid removes display:none items from
     * flow, so the visible input always occupies column 2 regardless of which
     * variant is active.
     */
    public static function make(string $name): Grid
    {
        return Grid::make(3)
            ->columnSpanFull()
            // Joining only applies at lg+ where Filament activates its multi-column grid.
            // Below that the fields stack normally with full rounding.
            ->extraAttributes(['class' => 'lg:[&_.fi-grid]:!gap-0 lg:[&_.fi-grid]:items-end'])
            ->schema([
                // Type select occupies 1 of 3 columns; the URL input gets the remaining 2
                // so it appears wider and the pair reads as a single compound control.
                Select::make("{$name}.type")
                    ->label(__('components/link.type_label'))
                    ->native(false)
                    ->options([
                        'page' => __('components/link.type_page'),
                        'external' => __('components/link.type_external'),
                        'local' => __('components/link.type_local'),
                    ])
                    ->default('page')
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set("{$name}.url", null))
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'lg:!rounded-r-none']),

                // Page picker — stores the page ID (integer) as the url value.
                // -ml-px overlaps the shared border so it doesn't render double.
                Select::make("{$name}.url")
                    ->label(__('components/link.page'))
                    ->options(fn (): array => Page::pluck('title', 'id')->all())
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get("{$name}.type") === 'page')
                    ->columnSpan(2)
                    ->extraAttributes(['class' => 'lg:!rounded-l-none']),

                // URL text input — shares statePath with page picker above (safe: only one visible).
                // Label adapts to type: https:// hint for external, /path#anchor hint for local.
                TextInput::make("{$name}.url")
                    ->label(fn (Get $get): string => $get("{$name}.type") === 'external'
                        ? __('components/link.url_external')
                        : __('components/link.url_local'))
                    ->visible(fn (Get $get): bool => in_array($get("{$name}.type"), ['external', 'local'], true))
                    ->columnSpan(2)
                    ->extraAttributes(['class' => 'lg:!rounded-l-none']),
            ]);
    }

    /**
     * Resolve stored link data to a ['url', 'target'] pair ready for Blade.
     *
     * The `url` key holds either a page ID (when type = 'page') or a URL string.
     * External links receive target="_blank"; all others get target="_self".
     *
     * @param  array{type?: string, url?: int|string|null}|null  $data
     * @return array{url: string, target: string}
     */
    public static function resolve(?array $data): array
    {
        $fallback = ['url' => '#', 'target' => '_self'];

        if (empty($data['type'])) {
            return $fallback;
        }

        return match ($data['type']) {
            'page' => ['url' => self::pageUrl($data['url'] ?? null), 'target' => '_self'],
            'external' => ['url' => (string) ($data['url'] ?? '#'), 'target' => '_blank'],
            'local' => ['url' => (string) ($data['url'] ?? '#'), 'target' => '_self'],
            default => $fallback,
        };
    }

    /**
     * Look up a page by ID and return its root-relative URL.
     * Falls back to '#' if the page was deleted after the link was saved.
     */
    private static function pageUrl(int|string|null $id): string
    {
        if ($id === null || $id === '') {
            return '#';
        }

        $page = Page::find((int) $id);

        return $page ? '/'.ltrim((string) $page->slug, '/') : '#';
    }
}
