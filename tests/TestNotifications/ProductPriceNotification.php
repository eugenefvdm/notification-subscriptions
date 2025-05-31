<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestNotifications;

use Eugenefvdm\NotificationSubscriptions\Notifications\BaseNotification;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;

class ProductPriceNotification extends BaseNotification
{
    use Queueable;

    public ?Model $customModel;

    public function __construct(Product $product)
    {
        $this->customModel = $product;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var Product $product */
        $product = $this->customModel;

        return (new MailMessage)
            ->subject("Price Update for {$product->name}")
            ->markdown('test::notification.price-update', [
                'product' => $product,
                'subscription' => $this->getSubscriptionFromNotifiable($notifiable)
            ]);
    }
    
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
