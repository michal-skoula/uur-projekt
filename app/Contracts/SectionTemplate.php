<?php

namespace App\Contracts;

use Closure;
use Illuminate\Contracts\View\View;

interface SectionTemplate
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function prepareData(array $data): static;

    public function render(): View|Closure|string;
}
