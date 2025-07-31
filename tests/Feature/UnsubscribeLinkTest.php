<?php

use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\SystemNotification;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\UnsubscribeLinkNotification;

it('will show an unsubscribe link if the notification is not a system notification', function () {
    $user = User::factory()->create();
    $notification = new UnsubscribeLinkNotification();

    $user->notify($notification);

    $subscription = $user->notificationSubscriptions()
    ->where('notification_template_id', UnsubscribeLinkNotification::getTemplate()->id)->first();

    // Build the unsubscribe url
    $unsubscribeUrl = url(route('notifications.unsubscribe', $subscription->uuid));

    $mail = $notification->toMail($user);
    $bladeView = $mail->render();

    // Check the unsubscribe link is present in the mail
    $this->assertStringContainsString($unsubscribeUrl, $bladeView);
});

it('will allow you to unsubscribe from a notification if you click the unsubscribe link', function () {
    $user = User::factory()->create();
    $notification = new UnsubscribeLinkNotification();

    $user->notify($notification);

    $subscription = $user->notificationSubscriptions()
    ->where('notification_template_id', UnsubscribeLinkNotification::getTemplate()->id)->first();

    // Build the unsubscribe url
    $unsubscribeUrl = url(route('notifications.unsubscribe', $subscription->uuid));

    $mail = $notification->toMail($user);
    $bladeView = $mail->render();

    // Visit the unsubscribe url
    $response = $this->get($unsubscribeUrl);
    expect($response->status())->toBe(200);

    // Check the success message is present
    expect($response->getContent())->toContain("Successfully unsubscribed from &#039;Unsubscribe Link Notification&#039;");

    // Check if the subscription now has an unsubscribed_at timestamp
    expect($subscription->fresh()->unsubscribed_at)->not->toBeNull();    
});

it('a system notification cannot be unsubscribed', function () {
    $user = User::factory()->create();
    $notification = new SystemNotification();

    $user->notify($notification);

    $subscription = $user->notificationSubscriptions()
    ->where('notification_template_id', SystemNotification::getTemplate()->id)->first();

    // Build the unsubscribe url
    $unsubscribeUrl = url(route('notifications.unsubscribe', $subscription->uuid));

    $response = $this->get($unsubscribeUrl);
    // dd($response->getContent());
    expect($response->status())->toBe(200);

    // Check the error message is present
    expect($response->getContent())->toContain("&#039;System Notification&#039; cannot be unsubscribed");
});