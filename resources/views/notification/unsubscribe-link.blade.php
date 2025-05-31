<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

<x-notification-subscriptions::unsubscribe :subscription="$subscription" />
</x-mail::message>
