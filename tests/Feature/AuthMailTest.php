<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_welcome_notification(): void
    {
        Notification::fake();

        $email = 'novo'.uniqid().'@teste.com';

        $this->post('/register', [
            'name' => 'Novo Aluno',
            'email' => $email,
            'cpf' => (string) random_int(10000000000, 99999999999),
            'phone' => '11999999999',
            'education_level' => 'superior_completo',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_google_registration_completion_sends_welcome_notification(): void
    {
        Notification::fake();

        $email = 'google'.uniqid().'@teste.com';

        $this->withSession([
            'pending_google_signup' => [
                'name' => 'Google User',
                'email' => $email,
                'google_id' => 'google-id-'.uniqid(),
            ],
        ])->post('/register/google/complete', [
            'cpf' => (string) random_int(10000000000, 99999999999),
            'phone' => '11988887777',
            'education_level' => 'superior_completo',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'Aluno Teste',
            'email' => 'aluno'.uniqid().'@teste.com',
            'password' => bcrypt('password'),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_notification_uses_web_reset_route(): void
    {
        $user = User::create([
            'name' => 'Aluno Teste',
            'email' => 'aluno'.uniqid().'@teste.com',
            'password' => bcrypt('password'),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);

        $notification = new ResetPasswordNotification('test-token');
        $mail = $notification->toMail($user);
        $rendered = $mail->render();

        $this->assertStringContainsString(route('password.reset', [
            'token' => 'test-token',
            'email' => $user->email,
        ], false), $rendered);
    }
}
