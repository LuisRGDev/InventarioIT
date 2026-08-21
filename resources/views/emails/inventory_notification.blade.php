<x-mail::message>
# {{ $title }}

{{ $messageContent }}

@if(!empty($details))
<x-mail::panel>
@foreach($details as $key => $value)
**{{ $key }}:** {{ $value }}<br>
@endforeach
</x-mail::panel>
@endif

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
