<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Notifications\Notification;
use Filament\Support\Actions\Action;

class Register extends BaseRegister
{
    protected function afterRegister(): void
    {
        Notification::make()
            ->title('Registration complete')
            ->success()
            ->send();

        $this->redirect($this->getLoginUrl());
    }
}
