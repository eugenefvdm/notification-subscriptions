<?php

namespace Eugenefvdm\NotificationSubscriptions\Listeners;

use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use Eugenefvdm\NotificationSubscriptions\Models\NotificationTemplate;
use Illuminate\Notifications\Events\NotificationSending;

class BeforeSendingListener
{
    /**
     * Handle the event.
     */
    public function handle(NotificationSending $event): bool
    {
        $notification = $event->notification;

        // Only process notifications that extend our BaseNotification
        if ($notification instanceof BaseNotification) {

            // Get the notification class
            $baseNotificationClass = get_class($notification);

            // Check if the template exists
            $template = NotificationTemplate::where('notification_class', $baseNotificationClass)->first();

            if (!$template) {
                $template = $baseNotificationClass::getOrCreateTemplate(
                    name: class_basename($baseNotificationClass),
                    category: $baseNotificationClass::getCategory(),
                    repeatFrequency: $baseNotificationClass::getRepeatFrequency(),
                    repeatInterval: $baseNotificationClass::getRepeatInterval(),
                    maxRepeats: $baseNotificationClass::getMaxRepeats(),
                    initialDelay: $baseNotificationClass::getInitialDelay(),
                );
            }

            // Always create/update subscription for this specific notification
            if ($notification->customModel) {
                $event->notifiable->subscribeToNotification($template, $notification->customModel);
            } else {
                $event->notifiable->subscribeToNotification($template);
            }

            // Get the subscription for this notification
            $query = $event->notifiable->notificationSubscriptions()
                ->where('notification_template_id', $template->id);

            // If this notification is using a custom model, include it in the query
            if ($notification->customModel) {
                $query->where('notifiable_type', get_class($notification->customModel))
                    ->where('notifiable_id', $notification->customModel->id);
            }

            $subscription = $query->first();

            // If the subscription has reached the max count, prevent the notification from being sent
            if ($subscription->isMaxCountReached()) {
                return false;
            }

            // If there is a scheduled time and it's in the future, do not send the notification
            if ($subscription->scheduled_at && $subscription->scheduled_at->isFuture()) {
                return false;
            }

            // If there is an initial delay and it's in the future, do not send the notification
            if ($template->initial_delay && now()->lt($template->initial_delay)) {
                return false;
            }
        }

        return true;
    }
}
