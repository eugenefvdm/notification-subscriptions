<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;

class EveryFourDaysNotification extends BaseNotification
{
    use Queueable;

    public static ?string $repeatFrequency = 'daily';
    public static ?int $repeatInterval = 4;
    public static ?int $maxRepeats = 3;

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
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Every Four Days Notification Test')
            ->line('This is a test notification for a every four days reminder.');
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
