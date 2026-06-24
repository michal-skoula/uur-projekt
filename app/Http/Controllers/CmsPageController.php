<?php

namespace App\Http\Controllers;

use App\Exceptions\PageBuilderException;
use App\Helpers\CmsSectionsHelper;
use App\Models\Page;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class CmsPageController extends ContentCollectionController
{
    public function __invoke(?string $path, ?User $user = null): View
    {
        $pages = [];

        // Root page
        if (! $path || $path === '/') {
            $lastPage = Page::where('slug', '')->first();
        } else {
            $slugs = explode('/', $path);

            foreach ($slugs as $slug) {
                $page = Page::where('slug', $slug)->first();

                if (! $page) {
                    abort(404);
                }

                $pages[] = $page;
            }

            $lastPage = last($pages);
        }

        if (empty($lastPage)) {
            abort(404);
        }

        if (! $lastPage->canView($user)) {
            abort(403);
        }

        $this->guardStatus($lastPage);

        return view('page-builder.page-builder', [
            'page' => $lastPage,
            'breadcrumbs' => $pages,
            'sections' => $this->renderSections($lastPage),
        ]);
    }

    /**
     * @return array<View|HtmlString>
     */
    private function renderSections(Page $page): array
    {
        $sectionsHelper = app(CmsSectionsHelper::class);
        $rendered = [];

        if (! $page->content) {
            return [];
        }

        foreach ($page->content as $section) {
            $slug = $section['type'];
            $data = $section['data'];

            if (! $sectionsHelper::isValidSection($slug)) {
                report(new PageBuilderException("Invalid section {$slug} found on page {$page->title}"));

                continue;
            }

            if ($sectionsHelper::isDisabled($slug)) {
                continue;
            }

            try {
                $rendered[] = $sectionsHelper::renderSection($slug, $data);
            } catch (\Exception $e) {
                \Log::error($e->getMessage(), $e->getTrace());
                report($e);
            }
        }

        return $rendered;
    }
}
