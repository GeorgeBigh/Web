@component('mail::message')
# Invitation to Join Company

You have been invited to join the company: {{ $invitation->company->company_name }}.

@if ($password)
    Please use the following credentials to log in and set your password:
    **Password:** {{ $password }}
@endif

@component('mail::button', ['url' => $acceptUrl])
    Accept Invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
