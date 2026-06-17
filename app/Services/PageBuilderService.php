<?php

namespace App\Services;

use App\Contracts\ContentCollectionItem;
use App\Contracts\ContentCollectionModel;

final class PageBuilderService
{
    /**
     * Builds a list of content collections and their items.
     *
     * @param  array<string, string>  $savePageTitlesTo  Keyed by "{collection}:{id}".
     * @return array<int, array{label: string, slug: string, items: array<int, array{collection: string, id: int, title: string}>}>
     */
    public static function buildAvailableGroups(array &$savePageTitlesTo): array
    {
        $groups = [];

        /** @var array<string, class-string> $collections */
        $collections = config('content-collections.collections', []);
        $disabled = config('content-collections.disabled', []);

        foreach ($collections as $slug => $class) {
            if (in_array($slug, $disabled)) {
                continue;
            }

            if (! is_a($class, ContentCollectionItem::class, true)) {
                \Log::warning('Registered ContentCollection does not extend the right model:'.$class);

                continue;
            }

            /** @var class-string<ContentCollectionModel> $class */
            $items = $class::all();

            foreach ($items as $item) {
                $savePageTitlesTo[$slug.':'.$item->getIdentifier()] = $item->getName();
            }

            $groups[] = [
                'label' => ucfirst($slug),
                'slug' => $slug,
                'items' => $items->map(fn (ContentCollectionModel $item): array => [
                    'collection' => $slug,
                    'id' => $item->getIdentifier(),
                    'title' => $item->getName(),
                ])->values()->all(),
            ];
        }

        return $groups;
    }

    /**
     * Recursively walks a tree of items and groups the IDs found by collection slug.
     *
     * @param  array<int, array{collection: string, id: int, children: array<int, mixed>}>  $items
     * @return array<string, array<int, int>>
     */
    public static function buildCollectionListsFromTree(array $items): array
    {
        $collections = [];

        foreach ($items as $item) {
            $collections[(string) $item['collection']][] = (int) $item['id'];

            if (! empty($item['children'])) {
                foreach (self::buildCollectionListsFromTree($item['children']) as $slug => $ids) {
                    $collections[$slug] = array_merge($collections[$slug] ?? [], $ids);
                }
            }
        }

        return $collections;
    }
}
