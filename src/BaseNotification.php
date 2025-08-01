<?php

namespace Eugenefvdm\NotificationSubscriptions;

use Eugenefvdm\NotificationSubscriptions\Models\NotificationSubscription;
use Eugenefvdm\NotificationSubscriptions\Models\NotificationTemplate;
use App\Models\User;
use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

abstract class BaseNotification extends Notification 
{    
    /**
     * customModel may be overridden by child classes and used for model specific notifications.
     * 
     * @var null|Model
     */
    public ?Model $customModel = null;

    /**
     * The subscription that this notification belongs to.
     * 
     * @var null|NotificationSubscription
     */
    protected ?NotificationSubscription $subscription = null;    

    /**
     * The frequency at which this notification will be repeated.
     * 
     * See Enum RepeatFrequency for possible values.
     * 
     * @var null|RepeatFrequency
     */
    protected static ?RepeatFrequency $repeatFrequency = null;

    /**
     * The interval at which this notification will be repeated.
     * 
     * @var null|int
     */
    protected static ?int $repeatInterval = null;

    /**
     * The maximum number of times this notification can be repeated.
     * 
     * @var null|int
     */
    protected static ?int $maxRepeats = null;

    /**
     * The initial delay for this notification.
     * 
     * @var null|Carbon
     */
    protected static ?Carbon $initialDelay = null;

    /**
     * The category that will be stored in the database.
     * 
     * @var string
     */
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
        $template = NotificationTemplate::where('notification_class', basename(static::class))->first();
        
        if (!$template) {
            throw new Exception("Notification template not found for class: " . basename(static::class). ". Please ensure a notification has already been sent.");
        }
        
        return $template;
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
