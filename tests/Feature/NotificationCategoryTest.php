<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\Feature;

use Eugenefvdm\NotificationSubscriptions\Tests\Models\User;
use Eugenefvdm\NotificationSubscriptions\Tests\Feature\DefaultCategoryNotification;
use Eugenefvdm\NotificationSubscriptions\Tests\Feature\CustomCategoryNotification;

it('will assign correct categories to notifications', function () {
    $user = User::factory()->create();

    // Test default category notification
    $user->notify(new DefaultCategoryNotification());
    
    $defaultSubscription = $user->notificationSubscriptions()
        ->where('notification_template_id', DefaultCategoryNotification::getTemplate()->id)
        ->first();

    // Template should be created with default category
    expect($defaultSubscription->template->category)->toBe('default');

    // Test custom category notification
    $user->notify(new CustomCategoryNotification());
    
    $customSubscription = $user->notificationSubscriptions()
        ->where('notification_template_id', CustomCategoryNotification::getTemplate()->id)
        ->first();

    // Template should be created with custom category
    expect($customSubscription->template->category)->toBe('custom');
}); 