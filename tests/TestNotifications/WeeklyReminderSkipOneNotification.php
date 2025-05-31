<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;

class WeeklyReminderSkipOneNotification extends BaseNotification
{
    use Queueable;

    public static ?string $repeatFrequency = 'weekly';
    public static ?int $repeatInterval = 2;

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
            ->subject('Weekly Reminder Skip One Notification Test')
            ->line('This is a test notification for a weekly reminder skip one.');
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
