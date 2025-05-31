<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use Illuminate\Support\Carbon;

class DelayedNotification extends BaseNotification
{
    use Queueable;

    public static ?RepeatFrequency $repeatFrequency = RepeatFrequency::Weekly;
    public static ?int $repeatInterval = 2;
    public static ?int $maxRepeats = 2;

    public static ?Carbon $initialDelay = null;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        self::$initialDelay = Carbon::now()->addWeek();
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
            ->subject('Delayed Notification Test')
            ->line('This is a test notification for a delayed reminder.');
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
