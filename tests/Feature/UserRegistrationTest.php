<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Register;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panel_allows_users_to_create_an_account(): void
    {
        $this->assertTrue(Filament::getPanel('admin')->hasRegistration());
    }

    public function test_registration_page_is_publicly_available(): void
    {
        $response = $this->get('/admin/register');

        $response->assertSuccessful();
    }

    public function test_registration_sends_a_completion_notification(): void
    {
        $register = new Register;
        $hook = new ReflectionMethod(Register::class, 'afterRegister');
        $hook->setAccessible(true);

        $hook->invoke($register);

        $notification = session('filament.notifications.0');

        $this->assertSame('Registration complete', $notification['title']);
        $this->assertSame('success', $notification['status']);
    }
}
