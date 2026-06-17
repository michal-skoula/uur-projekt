<?php

namespace App\Rules;

use App\Models\Page;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Ensures only a single homepage (a page with an empty slug) can exist.
 *
 * The homepage is represented by an empty slug, which Laravel's native
 * `unique` rule cannot validate — it treats empty strings as "not present"
 * and skips non-implicit rules. Marking this rule implicit forces it to run
 * for the empty slug. Non-empty slugs are left to the standard `unique` rule.
 */
class UniqueHomepageSlug implements ValidationRule
{
    public bool $implicit = true;

    public function __construct(private readonly ?int $ignoreId = null) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== '') {
            return;
        }

        $query = Page::query()->where('slug', '');

        if ($this->ignoreId !== null) {
            $query->whereKeyNot($this->ignoreId);
        }

        if ($query->exists()) {
            $fail('validation.unique')->translate();
        }
    }
}
