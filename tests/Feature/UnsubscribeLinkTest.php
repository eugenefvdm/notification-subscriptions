<?php

use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\UnsubscribeLinkNotification;

it('will show an unsubscribe link if the notification is not a system notification', function () {
    $user = User::factory()->create();
    $notification = new UnsubscribeLinkNotification();

    $user->notify($notification);

    $subscription = $user->notificationSubscriptions()
    ->where('notification_template_id', UnsubscribeLinkNotification::getTemplate()->id)->first();

    // Build the unsubscribe url
    $unsubscribeUrl = url(route('notifications.unsubscribe', $subscription->uuid));

    $result = $notification->toMail($user);
    $bladeView = $result->render();

    // Check the unsubscribe link is present in the mail
    $this->assertStringContainsString($unsubscribeUrl, $bladeView);
});