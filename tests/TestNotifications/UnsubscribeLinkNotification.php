<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Eugenefvdm\NotificationSubscriptions\BaseNotification;

class UnsubscribeLinkNotification extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->markdown('test::notification.unsubscribe-link', [
            'subscription' => $this->getSubscriptionFromNotifiable($notifiable)
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
