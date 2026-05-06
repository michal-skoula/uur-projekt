<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

class NavMenuBuilder extends Component
{
    public function render(): View|Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.nav-menu-builder');
    }
}
