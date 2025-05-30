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
                    repeatFrequency: $baseNotificationClass::getRepeatFrequency(),
                    repeatInterval: $baseNotificationClass::getRepeatInterval(),
                    maxRepeats: $baseNotificationClass::getMaxRepeats(),
                );

                // Now that we have a template, let's subscribe the user to the notification
                $event->notifiable->subscribeToNotification($template, $event->notifiable);
            }

            // Get the subscription for this notification
            $subscription = $event->notifiable->notificationSubscriptions()->where('notification_template_id', $template->id)->first();

            // If the subscription has reached the max count, prevent the notification from being sent
            if ($subscription->isMaxCountReached()) {
                return false;
            }

            // If there is a scheduled time and it's in the future, do not send the notification
            if ($subscription->scheduled_at && $subscription->scheduled_at->isFuture()) {
                return false;
            }

            // If there is an initial delay and it's in the future, do not send the notification
            if ($notification->getInitialDelay() && $notification->getInitialDelay()->isFuture()) {
                return false;
            }
        }

        return true;
    }
}
