# Notification Subscriptions

Notification Subscriptions is a package designed to keep track of repeat emails, delayed emails, and unsubscribing of emails.

The package makes use of Laravel's built-in event listeners `NotificationSending` and `NotificationSent` to automatic subscribe and to determine if and when a message should be sent.

## Installation

```bash
composer require eugenefvdm/notification-subscriptions
```

## Usage

Generate notifications as usual using `php artisan`:

```bash
php artisan make:notification DailyReminders --markdown=reminders.daily
```

Ensure the newly created notification extends the BaseNotification Class:

```php
class DailyReminders extends BaseNotification
```

## Repeat Settings

In your notification class, add any or all of the following variables to do repeated notifications:

```php
public static ?string $repeatFrequency = 'daily';
public static ?int $repeatInterval = 4;
public static ?int $maxRepeats = 3;
```

## Delayed Sending

To wait a certain amount of time before sending a notification, set `initialDelay` in your constructor:

```php
public static ?Carbon $initialDelay = null;

/**
 * Create a new notification instance.
 */
public function __construct()
{
    //
    self::$initialDelay = Carbon::now()->addDays(3);
}
```

## Unsubscribe

Any new notification will be automatically subscribed when used the first time.

### Unsubscribe Links in Blades

For unsubscribe links, modify the `toMail` method in the notification class:

```php
/**
 * Get the mail representation of the notification.
 */
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->markdown('notification.reminders.max-count', [
            'subscription' => $this->getSubscriptionFromNotifiable($notifiable)
        ]);
}
```

Then add this to your blade:

```php
<x-unsubscribe :url="route('notifications.unsubscribe', ['uuid' => $subscription->uuid])" />
```

## Examples

- Send a notification once
- Send a notification on a fixed schedule, e.g. every four days
- Send a notification a limited amount of times, e.g 3 times and then stop
- Send a notification for a limited amount of times per period, e.g. every 2nd week but stop after the 2nd time
- Delay a notification by X amount of time before sending it, where X could be `Carbon::now()->addDays(1)`
- Send a notification to a user but also based on a different model ID, for example a notification specific to to product ID.
- Example blade views that display all notifications with unsubscribe links per item
- Performant: Uses Laravel's cache to store templates so 1000s of messages may be sent

## Testing

- We aim for 100% code coverage
- Feature tests for each use case
- Unsubscribe link test
- Blade view test
- Performance tests (tests the cache)

## Questions

- Should we unsubscribe automatically when the max count has been reached?

Scenario: A user has been notified three times that their subscription is expiring. After the 3rd time, should be subscription be deleted or should the subscripton just be unsubscribed. If it's just unsusbscribed, what do we do about the counters, because surely they will have to be reset the next time a user's subscription expires?# notifications-subscriptions
# notification-subscriptions
