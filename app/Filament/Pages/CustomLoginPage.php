<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLoginPage;

class CustomLoginPage extends BaseLoginPage
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'admin@example.com',
            'password' => 'PlsPlsChciJednicku1',
            'remember' => true,
        ]);
    }
}
