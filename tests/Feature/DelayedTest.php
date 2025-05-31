<?php

use Carbon\Carbon;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\DelayedNotification;

it('will respect initial delay, bi-weekly interval, and max repeats', function () {
    $user = User::factory()->create();

    // Try to send first notification
    $user->notify(new DelayedNotification());
    
    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', DelayedNotification::getTemplate()->id)
        ->first();

    // Count should be 0 since we have a 1-week initial delay
    expect($subscription->count)->toBe(0);

    // Travel forward 6 days
    $this->travel(6)->days();

    // Try to send notification again
    $user->notify(new DelayedNotification());
    $subscription->refresh();

    // Count should still be 0 since we haven't reached the 1-week delay
    expect($subscription->count)->toBe(0);

    // Travel forward 1 more day (total 1 week)
    $this->travel(1)->days();

    // Send first notification (after initial delay)
    $user->notify(new DelayedNotification());
    $subscription->refresh();

    // Count should now be 1
    expect($subscription->count)->toBe(1);

    // Travel forward 1 week
    $this->travel(1)->weeks();

    // Try to send second notification
    $user->notify(new DelayedNotification());
    $subscription->refresh();

    // Count should still be 1 since we need 2 weeks between notifications
    expect($subscription->count)->toBe(1);

    // Travel forward 1 more week (total 2 weeks)
    $this->travel(1)->weeks();

    // Send second notification
    $user->notify(new DelayedNotification());
    $subscription->refresh();

    // Count should now be 2
    expect($subscription->count)->toBe(2);

    // Travel forward 2 weeks
    $this->travel(2)->weeks();

    // Try to send third notification
    $user->notify(new DelayedNotification());
    $subscription->refresh();

    // Count should still be 2 since we've hit max repeats
    expect($subscription->count)->toBe(2);
}); 