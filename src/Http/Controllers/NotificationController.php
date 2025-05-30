<?php

namespace EugeneFvdm\NotificationSubscriptions\Http\Controllers;

use App\Models\NotificationSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    /**
     * Unsubscribe a user from a notification.
     */
    public function unsubscribe(Request $request, string $uuid)
    {
        $subscription = NotificationSubscription::where('uuid', $uuid)->first();

        if (!$subscription) {
            abort(404, 'Subscription not found');
        }

        // Check if this notification can be unsubscribed
        if (!$subscription->canBeUnsubscribed()) {
            return redirect()->route('home')->with('error', 'This notification cannot be unsubscribed.');
        }

        // Unsubscribe
        $subscription->unsubscribe();

        Session::flash('success', 'You have been successfully unsubscribed from this notification.');

        // Return to previous page or home
        return redirect()->back()->fallback(route('home'));
    }    
} 