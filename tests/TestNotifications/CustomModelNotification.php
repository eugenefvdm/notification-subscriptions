<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Eugenefvdm\NotificationSubscriptions\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;

class CustomModelNotification extends BaseNotification
{
    use Queueable;

    protected static ?RepeatFrequency $repeatFrequency = RepeatFrequency::Weekly;

    public ?Model $customModel;

    /**
     * Create a new notification instance.
     */
    public function __construct(Model $model)
    {
        $this->customModel = $model;
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
            ->subject('Custom Model Notification')
            ->line('This is a test notification for a custom model. The model ID is: ' . $this->customModel->id);
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
