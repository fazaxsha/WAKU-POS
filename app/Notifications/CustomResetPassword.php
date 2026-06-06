<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable), $notifiable);
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @param  mixed   $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url, $notifiable = null)
    {
        $appName = config('app.name');

        $greeting = $notifiable && $notifiable->name
            ? 'Halo, ' . $notifiable->name . '!'
            : 'Halo!';

        return (new MailMessage)
            ->subject(Lang::get('Reset Password - WAKU-POS'))
            ->greeting($greeting)
            ->line(Lang::get('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda di sistem ' . $appName . '.'))
            ->line(Lang::get('Password ' . $appName . ' anda dapat direset dengan mengklik tombol di bawah ini, jika Anda tidak pernah meminta reset password, abaikan saja email ini.'))
            ->action(Lang::get('Reset Password'), $url)
            ->salutation("Salam interaksi 🙏,  \n" . $appName);
    }
}
