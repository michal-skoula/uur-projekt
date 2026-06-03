<?php

namespace App\Http\Controllers;

use App\Exceptions\PageBuilderException;
use App\Helpers\CmsSectionsHelper;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class CmsPageController extends Controller
{
    // todo: fix '/' root file not working
    public function __invoke(?string $path)
    {

        $user = auth()->user();

        $slugs = explode('/', $path);
        $pages = [];

        foreach ($slugs as $slug) {
            $page = Page::where('slug', $slug)->first();

            if (! $page) {
                abort(404);
            }

            $pages[] = $page;
        }

        $page = last($pages);

        if (! $user || ! $page->canView($user)) {
            abort(403);
        }

        return view('page-builder.page-builder', [
            'page' => $page,
            'breadcrumbs' => $pages,
            'sections' => $this->renderSections($page),
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

            if (! is_array($data)) {
                report(new PageBuilderException("Malformed data for section {$slug} found on page {$page->title}"));

                continue;
            }

            if ($sectionsHelper::isDisabled($slug)) {
                continue;
            }

            try {
                $rendered[] = $sectionsHelper::renderSection($slug, $data);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $rendered;
    }
}
