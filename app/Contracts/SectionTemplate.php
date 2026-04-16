<?php

namespace App\Contracts;

use Closure;
use Illuminate\Contracts\View\View;

interface SectionTemplate
{
    public function render(): View|Closure|string;
}
