<?php

namespace App\Http\Controllers;

use App\Actions\ResolvePageFromPath;
use App\Contracts\SectionTemplate;
use App\Helpers\PageBuilder;
use App\Models\Page;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class PageBuilderController extends Controller
{
    public function __invoke(?string $path = null): View
    {
        $page = ResolvePageFromPath::handle($path);

        abort_if($page === null || ! $page->is_published, 404);

        return view('page-builder.page', [
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

        if(! $page->content) {
            return $rendered;
        }

        foreach ($page->content as [$slug => $section]) {
            if(! PageBuilder::isValidSection($slug)) {
                Log::warning("Invalid section {$slug} found on page {$page->title}");
            }


            if (! is_array($section) || ! isset($section['type']) || ! is_string($section['type'])) {
                Log::warning('Malformed section in page '.$page->id, ['section' => $section]);

                continue;
            }

            $slug = $section['type'];
            $data = is_array($section['data'] ?? null) ? $section['data'] : [];

            if (PageBuilder::isDisabled($slug)) {
                continue;
            }

            $templateClass = PageBuilder::sectionTemplate($slug);
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
