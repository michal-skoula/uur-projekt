<?php

namespace App\Services;

use App\Contracts\ContentCollectionItem;
use App\Contracts\ContentCollectionModel;

final class PageBuilderService
{
    /**
     * Builds a list of content collections and their items.
     *
     * @param array<int, string> $savePageTitlesTo
     *
     * @return array<int, array{label: string, items: array<int, array{id: int, title: string}>}>
     */
    public static function buildAvailableGroups(array &$savePageTitlesTo): array
    {
        $groups = [];

        /** @var array<string, class-string> $collections */
        $collections = config('content-collections.collections', []);
        $disabled = config('content-collections.disabled', []);

        foreach ($collections as $slug => $class) {
            if(in_array($slug, $disabled)) {
                continue;
            }

            if (! is_a($class, ContentCollectionItem::class, true)) {
                \Log::warning('Registered ContentCollection does not extend the right model:' . $class);
                continue;
            }

            /** @var class-string<ContentCollectionModel> $class */
            $items = $class::all();

            foreach ($items as $item) {
                $savePageTitlesTo[$item->getIdentifier()] = $item->getName();
            }

            $groups[] = [
                'label' => ucfirst($slug),
                'items' => $items->map(fn(ContentCollectionModel $item): array => [
                    'id' => $item->getIdentifier(),
                    'title' => $item->getName(),
                ])->values()->all(),
            ];
        }

        return $groups;
    }

    /**
     * Recursively walks a tree of items and returns the IDs found.
     *
     * @param array<int, array{collection: string, id: int, children: array<int, mixed>}> $items
     * @return array<string, array<int>>
     */
    public static function buildCollectionListsFromTree(array $items): array
    {
        $collections = [];

        foreach ($items as $item) {
            $id = (int)$item['id'];
            $coll = (string)$item['collection'];

            $collections[$coll][] = $id;

            if (!empty($item['children'])) {
                array_merge($collections, ...self::buildCollectionListsFromTree($item['children']));
            }
        }

        return $collections;
    }
}
