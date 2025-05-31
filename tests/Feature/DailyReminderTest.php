<?php

use App\Notifications\DailyReminder;
use Carbon\Carbon;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\DailyReminderNotification;

it('will automatically subscribe a user to daily reminders', function () {
    $user = User::factory()->create();

    $user->notify(new DailyReminderNotification());

    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', DailyReminderNotification::getTemplate()->id)
        ->first();

    expect($subscription)->not->toBeNull();
    expect($subscription->count)->toBe(1);
});

it('will send daily reminders at the correct interval', function () {
    $user = User::factory()->create();
    $now = Carbon::now();

    // Send first notification
    $user->notify(new DailyReminderNotification());
    
    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', DailyReminderNotification::getTemplate()->id)
        ->first();

    expect($subscription->count)->toBe(1);

    // Travel forward 1 day
    $this->travel(1)->days();

    // Send second notification
    $user->notify(new DailyReminderNotification());
    $subscription->refresh();

    expect($subscription->count)->toBe(2);

    // Travel forward another day
    $this->travel(1)->days();

    // Send third notification
    $user->notify(new DailyReminderNotification());
    $subscription->refresh();

    expect($subscription->count)->toBe(3);
});

it('will respect max repeats if set', function () {
    $user = User::factory()->create();

    // Send first notification
    $user->notify(new DailyReminderNotification());
    
    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', DailyReminderNotification::getTemplate()->id)
        ->first();

    expect($subscription->count)->toBe(1);

    // Travel forward 1 day
    $this->travel(1)->days();

    // Send second notification
    $user->notify(new DailyReminderNotification());
    $subscription->refresh();

    expect($subscription->count)->toBe(2);

    // Travel forward another day
    $this->travel(1)->days();

    // Send third notification
    $user->notify(new DailyReminderNotification());
    $subscription->refresh();

    expect($subscription->count)->toBe(3);

    // Travel forward another day
    $this->travel(1)->days();

    // Try to send fourth notification
    $user->notify(new DailyReminderNotification());
    $subscription->refresh();

    // Count should still be 3 if maxRepeats is set to 3
    expect($subscription->count)->toBe(3);
});