<?php

namespace Eugenefvdm\NotificationSubscriptions\Notifications;

use Eugenefvdm\NotificationSubscriptions\Models\NotificationSubscription;
use Eugenefvdm\NotificationSubscriptions\Models\NotificationTemplate;
use App\Models\User;
use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?NotificationSubscription $subscription = null;    
    public ?Model $customModel = null;

    // Default repeat variables, can be overridden by child classes
    protected static ?RepeatFrequency $repeatFrequency = null;
    protected static ?int $repeatInterval = null;
    protected static ?int $maxRepeats = null;
    protected static ?Carbon $initialDelay = null;
    protected static ?string $category = 'default';
    
    // Get the repeat frequency from the base class
    public static function getRepeatFrequency(): ?RepeatFrequency
    {
        return static::$repeatFrequency ?? null;
    }

    // Get the repeat interval from the base class
    public static function getRepeatInterval(): ?int
    {
        return static::$repeatInterval ?? null;
    }

    // Get the max repeats from the base class
    public static function getMaxRepeats(): ?int
    {
        return static::$maxRepeats ?? null;
    }

    // Get the initial delay from the base class
    public static function getInitialDelay(): ?Carbon
    {
        return static::$initialDelay ?? null;
    }

    // Get the category from the base class
    public static function getCategory(): string
    {
        return static::$category ?? 'default';
    }

    /**
     * Get the notification subscription.
     */
    public function getSubscription(): ?NotificationSubscription
    {
        return $this->subscription;
    }

    /**
     * Get the subscription based on a notifiable.
     */
    public function getSubscriptionFromNotifiable(object $notifiable): ?NotificationSubscription
    {
        $query = NotificationSubscription::where('user_id', $notifiable->id)
            ->where('notification_template_id', self::getTemplate()->id);

        // If this notification is for a specific model, include it in the query
        if ($this->customModel) {
            $query->where('notifiable_type', get_class($this->customModel))
                ->where('notifiable_id', $this->customModel->id);
        }

        return $query->first();
    }

    /**
     * Get the notification template for this notification class.
     */
    public static function getTemplate(): ?NotificationTemplate
    {
        return NotificationTemplate::where('notification_class', basename(static::class))->firstOrFail();
    }

    /**
     * Get or create the notification template for this notification class.
     */
    public static function getOrCreateTemplate(
        string $name,
        string $category = 'default',
        ?RepeatFrequency $repeatFrequency = null,
        ?int $repeatInterval = null,
        ?int $maxRepeats = null,
        ?Carbon $initialDelay = null
    ): NotificationTemplate {
        $class = static::class;

        $template = NotificationTemplate::where('notification_class', $class)->first();

        if (!$template) {
            $template = NotificationTemplate::create([
                'name' => $name,
                'category' => $category,
                'notification_class' => $class,
                'repeat_frequency' => $repeatFrequency,
                'repeat_interval' => $repeatInterval,
                'max_repeats' => $maxRepeats,
                'initial_delay' => $initialDelay,
            ]);
        }

        return $template;
    }

    /**
     * Subscribe a user to this notification.
     */
    public static function subscribeUser(User $user, ?object $notifiableModel = null, ?int $maxCount = null): NotificationSubscription
    {
        $template = static::getTemplate();        

        return $user->subscribeToNotification($template, $notifiableModel, $maxCount, static::class);
    }    
}
