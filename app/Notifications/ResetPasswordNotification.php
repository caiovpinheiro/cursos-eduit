<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    protected function buildMailMessage($url): MailMessage
    {
        $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Redefinir sua senha — '.config('app.name'))
            ->greeting('Olá!')
            ->line('Recebemos um pedido para redefinir a senha da sua conta na '.config('app.name').'.')
            ->action('Redefinir senha', $url)
            ->line('Este link expira em '.$expire.' minutos.')
            ->line('Se você não solicitou a redefinição, ignore este e-mail. Sua senha permanece a mesma.');
    }

    protected function resetUrl($notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
