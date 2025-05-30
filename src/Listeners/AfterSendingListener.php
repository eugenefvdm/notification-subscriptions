<?php

namespace Eugenefvdm\NotificationSubscriptions\Listeners;

use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use Illuminate\Notifications\Events\NotificationSent;

class AfterSendingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {           
        $notification = $event->notification;

        if ($notification instanceof BaseNotification) {
            $query = $event->notifiable
                ->notificationSubscriptions()
                ->where('notification_template_id', $notification::getTemplate()->id);

            // If this notification is for a specific model, include it in the query
            if (property_exists($notification, 'customModel') && $notification->customModel) {
                $query->where('notifiable_type', get_class($notification->customModel))
                    ->where('notifiable_id', $notification->customModel->id);
            }

            $subscription = $query->first();
            
            $subscription->incrementCount();            
        }
    }
}
