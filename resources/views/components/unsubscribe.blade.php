@props(['subscription'])

@if($subscription?->uuid)
<div style="text-align: center; margin: 1em 0; font-size: 0.9em;">
    <a href="{{ route('notifications.unsubscribe', ['uuid' => $subscription->uuid]) }}">Unsubscribe</a>
</div>
@endif
