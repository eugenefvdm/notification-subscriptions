<?php

use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\WeeklyReminderSkipOneNotification;
use Carbon\Carbon;

it('will send weekly reminders at the correct bi-weekly interval', function () {
    $user = User::factory()->create();
    $now = Carbon::now();

    // Send first notification
    $user->notify(new WeeklyReminderSkipOneNotification());
    
    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', WeeklyReminderSkipOneNotification::getTemplate()->id)
        ->first();

    expect($subscription->count)->toBe(1);

    // Travel forward 1 week
    $this->travel(1)->weeks();

    // Try to send second notification
    $user->notify(new WeeklyReminderSkipOneNotification());
    $subscription->refresh();

    // Count should still be 1 since we're skipping every other week
    expect($subscription->count)->toBe(1);

    // Travel forward another week
    $this->travel(1)->weeks();

    // Send third notification
    $user->notify(new WeeklyReminderSkipOneNotification());
    $subscription->refresh();

    // Count should now be 2
    expect($subscription->count)->toBe(2);
}); 