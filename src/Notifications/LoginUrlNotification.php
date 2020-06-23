<?php

namespace Wingsline\PasswordlessAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class LoginUrlNotification extends Notification
{
    use Queueable;

    public $url;

    /**
     * LoginTokenNotification constructor.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user' => $notifiable,
            'url' => $this->url,
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(Lang::get('Sign in to :name', ['name' => config('app.name')]))
            ->line(Lang::get('Click the link below to sign in to your :name account.', ['name' => config('app.name')]))
            ->action(Lang::get('Sign In'), $this->url)
            ->line(Lang::get('If you did not make this request, you can safely ignore this email.'));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
}
