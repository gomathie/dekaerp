@props(['url'])
{{-- PNG, not SVG: Gmail and Outlook do not render SVG images in mail. --}}
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ asset('images/icons/icon-192.png') }}" class="logo" width="40" height="40" alt="" style="vertical-align: middle;">
<span class="brand-name">{{ trim(strip_tags($slot)) ?: config('app.name') }}</span>
</a>
</td>
</tr>
