@props(['subscription'])

<div style="text-align: center; margin: 1em 0;">
    <a href="{{ route('notifications.unsubscribe', ['uuid' => $subscription->uuid]) }}">Unsubscribe</a>
</div> 