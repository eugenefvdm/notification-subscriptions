<?php

use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\Product;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;
use Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications\ProductPriceNotification;

it('will automatically subscribe a user to product price notifications', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 99.99
    ]);

    $user->notify(new ProductPriceNotification($product));

    $subscription = $user->notificationSubscriptions()
        ->where('notification_template_id', ProductPriceNotification::getTemplate()->id)
        ->where('notifiable_type', Product::class)
        ->where('notifiable_id', $product->id)
        ->first();

    expect($subscription)->not->toBeNull()
        ->and($subscription->count)->toBe(1)
        ->and($subscription->notifiable_type)->toBe(Product::class)
        ->and($subscription->notifiable_id)->toBe($product->id);
});

it('will create separate subscriptions for different products', function () {
    $user = User::factory()->create();
    $product1 = Product::factory()->create(['name' => 'Product 1', 'price' => 99.99]);
    $product2 = Product::factory()->create(['name' => 'Product 2', 'price' => 149.99]);

    // Send notification for first product
    $user->notify(new ProductPriceNotification($product1));

    $subscriptions = $user->notificationSubscriptions()
        ->where('notification_template_id', ProductPriceNotification::getTemplate()->id)
        ->get();
    
    // Send notification for second product
    $user->notify(new ProductPriceNotification($product2));

    $subscriptions = $user->notificationSubscriptions()
        ->where('notification_template_id', ProductPriceNotification::getTemplate()->id)
        ->get();

    expect($subscriptions)->toHaveCount(2)
        ->and($subscriptions[0]->notifiable_id)->toBe($product1->id)
        ->and($subscriptions[1]->notifiable_id)->toBe($product2->id);
});

it('will include product details in the notification', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Premium Widget',
        'price' => 199.99
    ]);

    $notification = new ProductPriceNotification($product);
    $mail = $notification->toMail($user);
    $bladeView = $mail->render();

    $this->assertStringContainsString('The price for Premium Widget has been updated to 199.99', $bladeView);    
});