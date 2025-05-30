<?php

namespace Eugenefvdm\NotificationSubscriptions\Traits;

use Eugenefvdm\NotificationSubscriptions\Models\NotificationSubscription;
use Eugenefvdm\NotificationSubscriptions\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasNotificationSubscriptions
{
    /**
     * Get all notification subscriptions for this user
     */
    public function notificationSubscriptions(): HasMany
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    /**
     * Get active notification subscriptions for this user
     */
    public function activeNotificationSubscriptions(): Collection
    {
        return $this->notificationSubscriptions()
            ->where('subscribed', true)
            ->whereNull('unsubscribed_at')
            ->get();
    }

    /**
     * Check if user has any active notifications
     */
    public function hasActiveNotifications(): bool
    {
        return $this->notificationSubscriptions()
            ->where('subscribed', true)
            ->whereNull('unsubscribed_at')
            ->exists();
    }

    /**
     * Subscribe user to a notification template
     */
    public function subscribeToNotification(NotificationTemplate $template, ?object $notifiable = null, ?string $notificationClass = null): NotificationSubscription
    {
        $data = [
            'notification_template_id' => $template->id,
            'user_id' => $this->id,
            'subscribed' => true,
            'notification_class' => $notificationClass ?? $template->notification_class,
        ];

        // If a notifiable model is provided, associate it
        if ($notifiable) {
            $data['notifiable_type'] = get_class($notifiable);
            $data['notifiable_id'] = $notifiable->id;
        }

        // Create or update the subscription
        $subscription = NotificationSubscription::updateOrCreate(
            [
                'notification_template_id' => $template->id,
                'user_id' => $this->id,
                'notifiable_type' => $data['notifiable_type'] ?? null,
                'notifiable_id' => $data['notifiable_id'] ?? null,
            ],
            $data
        );

        return $subscription;
    }

    /**
     * Unsubscribe user from a notification template
     */
    public function unsubscribeFromNotification(NotificationTemplate $template, ?object $notifiable = null): bool
    {
        $query = $this->notificationSubscriptions()
            ->where('notification_template_id', $template->id);

        // If a notifiable model is provided, filter by it
        if ($notifiable) {
            $query->where('notifiable_type', get_class($notifiable))
                ->where('notifiable_id', $notifiable->id);
        } else {
            $query->whereNull('notifiable_type')
                ->whereNull('notifiable_id');
        }

        $subscription = $query->first();

        if ($subscription && $subscription->canBeUnsubscribed()) {
            $subscription->unsubscribe();
            return true;
        }

        return false;
    }

    /**
     * Check if user is subscribed to a specific notification
     */
    public function isSubscribedTo(NotificationTemplate $template, ?object $notifiable = null): bool
    {
        $query = $this->notificationSubscriptions()
            ->where('notification_template_id', $template->id)
            ->where('subscribed', true)
            ->whereNull('unsubscribed_at');

        // If a notifiable model is provided, filter by it
        if ($notifiable) {
            $query->where('notifiable_type', get_class($notifiable))
                ->where('notifiable_id', $notifiable->id);
        } else {
            $query->whereNull('notifiable_type')
                ->whereNull('notifiable_id');
        }

        return $query->exists();
    }
}
