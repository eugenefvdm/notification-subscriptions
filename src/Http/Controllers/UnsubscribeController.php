<?php

namespace Eugenefvdm\NotificationSubscriptions\Http\Controllers;

use Eugenefvdm\NotificationSubscriptions\Models\NotificationSubscription;
use Illuminate\Routing\Controller;

class UnsubscribeController extends Controller
{
    /**
     * Unsubscribe a user from a notification template.
     */
    public function __invoke(string $uuid)
    {
        $subscription = NotificationSubscription::where('uuid', $uuid)->first();

        if (!$subscription) {
            $result = [
                'success' => false,
                'message' => "Subscription not found",
            ];
        } else if (!$subscription->canBeUnsubscribed()) {
            $result = [
                'success' => false,
                'message' => "'{$subscription->template->name_with_spaces}' cannot be unsubscribed",
            ];
        } else if ($subscription->unsubscribed_at) {
            $result = [
                'success' => false,
                'message' => "You are already unsubscribed from '{$subscription->template->name_with_spaces}'",
            ];
        } else {
            $subscription->unsubscribe();

            $result = [
                'success' => true,
                'message' => "Successfully unsubscribed from '{$subscription->template->name_with_spaces}'",
            ];
        }

        $result['template'] = $subscription->template;
    
        return view('notification-subscriptions::unsubscribed', compact('result'));
    }    
} 