<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('login');
        $dashboardUrl = route('web.dashboard');

        return (new MailMessage)
            ->subject('Bem-vindo(a) à '.config('app.name'))
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Sua conta foi criada com sucesso na '.config('app.name').'.')
            ->line('Agora você pode explorar cursos, acompanhar seu progresso e conquistar certificados.')
            ->action('Acessar minha conta', $dashboardUrl)
            ->line('Se preferir, faça login em: '.$loginUrl)
            ->line('Obrigado por fazer parte da nossa plataforma!');
    }
}
