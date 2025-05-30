<?php

namespace App\Listeners;

use App\Notifications\BaseNotification;
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
        ray("AfterSendingListener");
        
        $notification = $event->notification;

        if ($notification instanceof BaseNotification) {
            $subscription = $event->notifiable
                ->notificationSubscriptions()
                ->where('notification_template_id', $notification::getTemplate()->id)
                ->first();
            
            $subscription->incrementCount();

            // Set the next scheduled date based on the template's repeat frequency
            if ($subscription->template->isRepeatable()) {
                $subscription->scheduled_at = now()->addMonth(); // For monthly frequency
                $subscription->save();
            }
        }
    }
}
