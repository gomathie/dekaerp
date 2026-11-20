{{-- No indentation: Markdown renders a line indented four spaces as a code block. --}}
{{-- Escaped output only: company and inviter names are typed by users. --}}
<x-mail::message>
@if ($inviterName)
{{ __('security::views/emails/user-invitation.body-with-inviter', ['inviter' => $inviterName, 'company' => $companyName]) }}
@else
{{ __('security::views/emails/user-invitation.body', ['company' => $companyName]) }}
@endif

{{ __('security::views/emails/user-invitation.instructions') }}

<x-mail::button :url="$acceptUrl">
{{ __('security::views/emails/user-invitation.create-account') }}
</x-mail::button>

{{ __('security::views/emails/user-invitation.expires', ['date' => $expiresOn]) }}

<x-slot:subcopy>
{{ __('security::views/emails/user-invitation.link-fallback') }}
<span class="break-all">[{{ $acceptUrl }}]({{ $acceptUrl }})</span>

{{ __('security::views/emails/user-invitation.discard-email') }}
</x-slot:subcopy>
</x-mail::message>
