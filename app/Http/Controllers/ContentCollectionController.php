<?php

namespace App\Http\Controllers;

use App\Contracts\ContentCollectionItem;
use App\Enums\ContentStatus;
use Illuminate\Support\Facades\Auth;

/**
 * Centralizes how each publication status is handled so the individual controllers
 * only deal with resolving and rendering their own content.
 */
abstract class ContentCollectionController extends Controller
{
    /*
     * todo: Universal controller ideas: currently, each collection has its own controller
     *       because the query and view differ (pages run the section pipeline, news
     *       paginates). A single config-driven controller could resolve the collection
     *       class from `content-collections.collections` via a route parameter, bind the
     *       item by slug, call `guardStatus()`, and render a view discovered by
     *       convention (e.g. `collections.{slug}.show`). The `guardStatus()` and
     *       `scopePublished()` primitives added here are exactly what it would reuse.
     */

    /**
     * Enforces publication status for the public frontend. Published content is
     * always visible; Draft and Disabled are hidden behind a 404 unless the
     * viewer is an authenticated admin previewing the live site.
     */
    protected function guardStatus(ContentCollectionItem $item): void
    {
        if ($item->getStatus() === ContentStatus::PUBLISHED) {
            return;
        }

        if ($this->viewerCanPreviewDrafts()) {
            return;
        }

        abort(404);
    }

    /**
     * Whether the current viewer may see non-published content on the live site.
     */
    protected function viewerCanPreviewDrafts(): bool
    {
        // todo: Update this to use dedicated level-based access, for instance
        //       only editors should be able to edit drafts, not visitors.
        //       currently there is only authed/unauthed, so this is sufficient.
        return Auth::check();
    }
}
