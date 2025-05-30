<?php

namespace Eugenefvdm\NotificationSubscriptions;

use Eugenefvdm\NotificationSubscriptions\Listeners\AfterSendingListener;
use Eugenefvdm\NotificationSubscriptions\Listeners\BeforeSendingListener;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationSubscriptionsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notification-subscriptions');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/notification-subscriptions'),
        ], 'notification-subscriptions-views');

        Event::listen(
            NotificationSending::class,
            BeforeSendingListener::class,
        );

        Event::listen(
            NotificationSent::class,
            AfterSendingListener::class,
        );
    }
} 