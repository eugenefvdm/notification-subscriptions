<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class SystemNotification extends BaseNotification
{
    use Queueable;

    public static ?string $category = 'system';

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

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('System Category Test')
            ->line('This is a test notification with system category.');
    }
} 