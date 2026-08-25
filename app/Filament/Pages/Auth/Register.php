<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Notifications\Notification;

class Register extends BaseRegister
{
    protected function afterRegister(): void
    {
        Notification::make()
            ->title('Registration complete')
            ->success()
            ->send();
    }
}
