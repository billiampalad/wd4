<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.')
            ->action('Atur Ulang Kata Sandi', $resetUrl)
            ->line('Tautan ini berlaku selama '.config('auth.passwords.users.expire').' menit dan hanya dapat digunakan satu kali.')
            ->line('Jika Anda tidak meminta perubahan kata sandi, abaikan email ini.');
    }
}
