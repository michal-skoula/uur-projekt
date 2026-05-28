
## Page Builder & Content Management
- [ ] Artisan command to clear non-existent sections in prod DB
- [ ] Rules for how to make global changes, aka loading fonts and other stuff for section builders. I think this is something that should be designed by a "Design Architect" agent and not a lowly section builder, but figure it out in general. Likely use the frontend design skill by anthropic and some other stuff

## SEO
- [ ] **XML Sitemap** — `/sitemap.xml` route + Blade XML view. `SitemapService` already builds the page tree; add a `xmlEntries()` method that walks it and yields `[loc, lastmod]` for published pages. For content collections: add a `HasSitemapUrls` interface with a static `sitemapUrls(): iterable` method; `SitemapService` reads `config('content-collections.collections')`, auto-adds the index URL (`/slug`), and calls `sitemapUrls()` on models that implement it. No spatie needed.
- [ ] **robots.txt** — add a `/robots.txt` route (or static file) that includes `Sitemap:` pointer.
- [ ] **JSON-LD structured data** — use `@push('schema')` / `@stack('schema')` Blade stack in `website.blade.php` `<head>`. Each section view that has relevant schema (FAQ, Course, AggregateRating, etc.) pushes its own `<script type="application/ld+json">` block. Data is already passed to sections so just render as needed.
- [ ] **`<meta name="description">` + canonical** — add overridable fields on `Page`; auto-generate description at publish time (observer on `is_published` flipping true) using Claude Haiku — pass title + first ~500 chars of stripped body text.
- [ ] **Auto alt-text** — hook into the existing webp image conversion pipeline; send image to Haiku vision API, store result back on the media record. One-time cost per upload (~fractions of a cent). Ensure original image bytes are available before the pipeline discards them. Use claude/haiku-latest on open router
- [ ] **Images and multimedia** - Use *Filament Curator* by adam weston for this, also when accessing a URL directly like `site.com/assets/...`, maybe have a http header that checks if you should only get the file, or if you are sharing the file in which case show a "download or preview page" 
- [ ] **Filament SEO health badges** — show warning on pages/media missing description or alt text; add bulk "Generate missing SEO" action for backfilling legacy content.

## Refactoring into composer modules
- [ ] 

## Testing
- [ ] Each future composer module should be tested:
  - [ ] **Unit:** Do the helpers and other unit-testable pieces work as expected
  - [ ] **Integration:** Whether the Artisan commands generate the right output as well as testing filament, livewire and the other stuff, all possible with filament testing facades and helper methods.
  - [ ] **End-to-end:** Avoid when possible, most stuff should be doable with filament, livewire test methods.
