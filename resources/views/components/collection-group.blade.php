<?php

use App\Concerns\ContentCollectionItem;
use App\Concerns\ContentCollectionModel;
use App\Exceptions\ContentCollectionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component as LivewireComponent;

new class extends LivewireComponent
{
    public string $heading;

    /**
     * @var Collection<ContentCollectionItem>|null
     */
    protected ?Collection $items;

    /**
     * @var array{'id': int, 'collection': string, 'children': array}|null
     */
    protected ?array $pagesTree;

    /**
     * @param string $heading
     * @param Collection<ContentCollectionItem>|null $items
     * @param array|null $pagesTree
     */
    public function mount(string $heading, ?Collection $items = null, bool $preloadAll = false, ?array $pagesTree = null): void
    {
        $this->heading = $heading;
        $this->items = $items;
        $this->pagesTree = $pagesTree;
    }

    /**
     * @return array<string,array{'name': string, 'id': int, 'collection': string}>
     */
    public function buildCollectionGroupsArray(): array
    {
        $collectionGroups = [];

        foreach ($this->items as $item) {
            if (!($item instanceof ContentCollectionItem)) {
                report(new ContentCollectionException("Item not implementing ContentCollectionItem found in items array: " . get_class($item)));
                continue;
            }

            $disabledCollections = Config::array('content-collections.disabled');
            if (in_array($item->getCollectionSlug(), $disabledCollections)) {
                \Log::warning("Attempting to display disabled ContentCollection item in the menu builder: " . $item->getCollectionSlug());
                continue;
            }

            $collectionGroups[$item->getCollectionSlug()][] = [
                'name' => $item->getName(),
                'id' => $item->getIdentifier(),
                'collection' => $item->getCollectionSlug(),
            ];
        }

        return $collectionGroups;
    }

    /**
     * @return ContentCollectionModel[] All successfully found pages resolved from the tree
     */
    public function getAllPages(): array
    {
        $pagesResult = [];

        if (!$this->pagesTree) {
            \Log::warning("Empty navigation page tree, add a page!");
            return [];
        }

        // Recursively walks the tree looking for IDs
        foreach ($this->pagesTree as $rootPage) {
            $this->getPageIds($rootPage, $pagesResult);
        }

        $allPages = [];

        foreach ($pagesResult as $collectionSlug => $ids) {
            $collections = Config::array('content-collections.collections');
            if (!array_key_exists($collectionSlug, $collections)) {
                report(new ContentCollectionException("Unregistered collection detected: {$collectionSlug}, unresolvable."));
                continue; // If the section isn't found, there's no model to search for and collection can be skipped.
            }

            /** @var class-string<ContentCollectionModel> $class */
            $class = config("content-collections.collections.{$collectionSlug}");
            // todo: guard against models that dont implement necessary stuff to be a page, idk what that is yet though

            $disabled = Config::array('content-collections.disabled');
            if (in_array($class, $disabled, strict: true)) {
                \Log::warning("Attempting to load pages from disabled collection {$collectionSlug}");
                continue; // Disabled sections should not be displayed, as they don't display on the frontend either.
            }

            $class::query()
                ->findMany($ids)
                ->each(function (ContentCollectionModel $page) use (&$allPages, $collectionSlug): void {
                    $allPages[$collectionSlug][] = $page;
                });
        }

        return $allPages;
    }

    /**
     * @param array{'id': int, 'collection': string, 'children': array} $page
     * @param array $results
     * @return void
     */
    private function getPageIds(array $page, array &$results): void
    {
        $id = $page['id'];
        $collection = $page['collection'];
        $results[$collection][] = $id;

        if (count($page['children']) === 0) {
            return;
        }

        foreach ($page['children'] as $child) {
            $this->getPageIds($child, $results);
        }
    }
};
?>

<div
    x-init="
        function showMore() {

        }

    "
    x-data="{open: true}"
>
    {{-- Header --}}
    <div>
        {{ $heading }}
    </div>

{{--    @php echo "allPages:"; dump($this->getAllPages()) @endphp--}}
{{--    @php echo "collectionGroups: "; dump($this->buildCollectionGroupsArray()) @endphp--}}
    {{--    <div>{{ $heading }}</div>--}}

    {{--    @foreach(/** @var $items Collection<ContentCollectionItem>  */$items as $item)--}}


    {{--    @endforeach--}}
</div>
