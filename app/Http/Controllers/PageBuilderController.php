<?php

namespace App\Http\Controllers;

use App\Actions\ResolvePageFromPath;
use App\Contracts\SectionTemplate;
use App\Helpers\PageBuilderHelper;
use App\Models\Page;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class PageBuilderController extends Controller
{
    public function __invoke(?string $path = null): View
    {
        $page = ResolvePageFromPath::handle($path);

        if (! $page) {
            abort(404);
        }

        /** @var Page $page */

        if (! $page->is_published) {
            abort(404); // todo: add override for admins to still be able to access
        }

        return view('page-builder.page-builder', [
            'page' => $page,
            'sections' => $this->renderSections($page),
        ]);
    }

    /**
     * @return array<View|Closure|string>
     */
    private function renderSections(Page $page): array
    {
        $rendered = [];

        if (! $page->content) {
            return [];
        }

        foreach ($page->content as $section) {
            $slug = $section['type'];
            $data = $section['data'];

            if (! PageBuilderHelper::isValidSection($slug)) {
                Log::warning("Invalid section {$slug} found on page {$page->title}");
                continue;
            }

            if (! is_array($data)) {
                Log::warning("Malformed data for section {$slug} found on page {$page->title}");
                continue;
            }

            if (PageBuilderHelper::isDisabled($slug)) {
                continue;
            }

            $templateClass = PageBuilderHelper::sectionTemplate($slug);
            if ($templateClass === null) {
                Log::warning("No template registered for section '{$slug}'");
                continue;
            }

            /** @var SectionTemplate $template */
            $template = app($templateClass);
            $rendered[] = $template->render($data);
        }

        return $rendered;
    }
}
