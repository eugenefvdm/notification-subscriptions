<?php

namespace Eugenefvdm\NotificationSubscriptions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Eugenefvdm\NotificationSubscriptions\Models\NotificationSubscription;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    /**
     * Unsubscribe a user from a notification.
     */
    public function unsubscribe(Request $request, string $uuid)
    {
        $subscription = NotificationSubscription::where('uuid', $uuid)->first();

        if (!$subscription) {
            return redirect()->back()->with('error', 'Subscription not found');
        }

        if (!$subscription->canBeUnsubscribed()) {
            return redirect()->back()->with('error', 'This notification cannot be unsubscribed');
        }

        $subscription->unsubscribe();

        return redirect()->back()->with('success', 'Successfully unsubscribed');
    }    
} 