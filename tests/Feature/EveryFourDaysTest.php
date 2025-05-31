<?php

use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\EveryFourDaysNotification;
use Carbon\Carbon;

it('will send notifications every four days and respect max repeats', function () {
    $user = User::factory()->create();
    $now = Carbon::now();

    // Send first notification
    $user->notify(new EveryFourDaysNotification());
    
    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', EveryFourDaysNotification::getTemplate()->id)
        ->first();

    expect($subscription->count)->toBe(1);

    // Travel forward 2 days
    $this->travel(2)->days();

    // Try to send second notification
    $user->notify(new EveryFourDaysNotification());
    $subscription->refresh();

    // Count should still be 1 since we need 4 days between notifications
    expect($subscription->count)->toBe(1);

    // Travel forward another 2 days (total 4 days)
    $this->travel(2)->days();

    // Send second notification
    $user->notify(new EveryFourDaysNotification());
    $subscription->refresh();

    // Count should now be 2
    expect($subscription->count)->toBe(2);

    // Travel forward 3 days
    $this->travel(3)->days();

    // Try to send third notification
    $user->notify(new EveryFourDaysNotification());
    $subscription->refresh();

    // Count should still be 2 since we need 4 days between notifications
    expect($subscription->count)->toBe(2);

    // Travel forward 1 more day (total 4 days)
    $this->travel(1)->days();

    // Send third notification
    $user->notify(new EveryFourDaysNotification());
    $subscription->refresh();

    // Count should now be 3
    expect($subscription->count)->toBe(3);

    // Travel forward 4 days
    $this->travel(4)->days();

    // Try to send fourth notification
    $user->notify(new EveryFourDaysNotification());
    $subscription->refresh();

    // Count should still be 3 since we've hit max repeats
    expect($subscription->count)->toBe(3);
}); 