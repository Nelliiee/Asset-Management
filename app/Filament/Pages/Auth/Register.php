<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        $response = parent::register();

        if ($response === null) {
            return null;
        }

        Filament::auth()->logout();

        return new class implements RegistrationResponse
        {
            public function toResponse($request): RedirectResponse|Redirector
            {
                return redirect()->to(Filament::getLoginUrl());
            }
        };
    }

    protected function afterRegister(): void
    {
        Notification::make()
            ->title('Registration complete')
            ->success()
            ->send();
    }
}
