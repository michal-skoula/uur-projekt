<?php

namespace App\Contracts;

use Closure;
use Illuminate\Contracts\View\View;

interface SectionTemplate
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): View|Closure|string;
}
