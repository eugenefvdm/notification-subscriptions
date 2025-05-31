# Notification Subscriptions

![Tests](https://github.com/eugenefvdm/notification-subscriptions/actions/workflows/run-tests.yml/badge.svg)
 [![Downloads](https://img.shields.io/packagist/dt/eugenefvdm/notification-subscriptions.svg)](https://packagist.org/packages/eugenefvdm/notification-subscriptions)

Notification Subscriptions is a Laravel package that may be used to keep track of repeat emails, delayed emails, and unsubscribing of email notifications.

The package makes use of Laravel's built-in event listeners `NotificationSending` and `NotificationSent` to automatically subscribe and to determine if and when a message should be sent.

Categorization of emails and model specific subscriptions are possible.

## Installation

```bash
composer require eugenefvdm/notification-subscriptions
```

After installing the package, publish and run the migrations:

```bash
php artisan vendor:publish --tag="notification-subscriptions-migrations"
php artisan migrate
```

This will create the `notification_templates` and `notification_subscriptions` tables.

If you want to customize the unsubscribe link, publish the view component:

```bash
php artisan vendor:publish --tag="notification-subscriptions-views"
```

### User Model

Add the `HasNotificationSubscriptions` trait to your `User` model:

```php
use Eugenefvdm\NotificationSubscriptions\Traits\HasNotificationSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasNotificationSubscriptions;
```

## Usage

Generate notifications as per usual using `php artisan`:

```bash
php artisan make:notification DailyReminder --markdown=reminders.daily
```

Extend the newly created notification with the BaseNotification class:

```php
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;

class DailyReminder extends BaseNotification
{
    use Queueable;
```

## Repeat Settings

In your notification class, add any or all of the following variables to do repeated notifications:

```php
use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;


class DailyReminder extends BaseNotification
{
    use Queueable;

    public static ?string $repeatFrequency = RepeatFrequency::Daily;
    public static ?int $repeatInterval = 4; // optional
    public static ?int $maxRepeats = 3; // optional defaults to 1
```

## Delayed Sending

To wait a certain amount of time before sending a notification, set `initialDelay` in your constructor:

```php
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;

class DailyReminder extends BaseNotification
{
    use Queueable;

    public static ?Carbon $initialDelay = null;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {        
        self::$initialDelay = Carbon::now()->addWeek();
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
<x-notification-subscriptions::unsubscribe :subscription="$subscription" />
```

## Categorization

All new messages without an explicit category assignment will be assigned to the `default` category in the database.

To specify a custom category, use `$category`:

```php
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;

class DailyReminder extends BaseNotification
{
    use Queueable;

    public static ?string $category = 'reminders';
```

## Model Specific Subscriptions

Notifications are typically tied to a user, but at times one wants to bind a notification to both a user and another model. For example, you might have a `products` table, and you want a user to be subscribed to a price notification for the `Product` model. Here's how:

```php
use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use App\Models\Product;

class ProductPriceNotification extends BaseNotification
{
    use Queueable;

    public Product $customModel; // Override $customModel

    public function __construct(Product $product)
    {
        $this->customModel = $product;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Price Update for {$this->customModel->name}")
            ->markdown('notification.product-price-update', [
                'product' => $this->customModel,
                // Add line below and blade component for unsubscribe
                'subscription' => $this->getSubscriptionFromNotifiable($notifiable)
            ]);
    }
}
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

