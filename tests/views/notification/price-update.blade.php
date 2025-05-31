<x-mail::message>
# Introduction

The price for {{$product->name}} has been updated to {{$product->price}}

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

<x-notification-subscriptions::unsubscribe :subscription="$subscription" />
</x-mail::message> 