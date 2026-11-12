<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('support::filament/pages/whats-new.title') }}</title>
    {{-- Paper layout: no flex or grid (dompdf), and the category in words
         rather than colour or icons. --}}
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .sub { color: #6b7280; margin: 0 0 16px; }
        h2 { font-size: 14px; margin: 18px 0 6px; padding-bottom: 4px; border-bottom: 1px solid #d1d5db; }
        h2 .date { font-weight: normal; color: #6b7280; font-size: 11px; }
        h3 { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #4b5563; margin: 10px 0 4px; }
        ul { margin: 0 0 6px 16px; padding: 0; }
        li { margin-bottom: 3px; }
        code { font-family: DejaVu Sans Mono, monospace; font-size: 10px; }
        pre { white-space: pre-wrap; font-size: 9px; }
    </style>
</head>
<body>
    <h1>{{ __('support::filament/pages/whats-new.heading', ['app' => $app]) }}</h1>
    <p class="sub">{{ now()->toDateString() }}</p>

    @foreach ($releases as $release)
        <h2>
            {{ $release['version'] }}
            @if ($release['date'])
                <span class="date">— {{ $release['date'] }}</span>
            @endif
        </h2>

        @foreach ($release['sections'] as $section)
            @continue($section['items'] === [])
            <h3>{{ \Webkul\Support\Filament\Pages\WhatsNew::sectionLabel($section) }}</h3>
            <ul>
                @foreach ($section['items'] as $item)
                    <li>
                        @if ($item['security'])
                            <strong>[{{ __('support::filament/pages/whats-new.categories.security') }}]</strong>
                        @endif
                        {!! $item['html'] !!}
                    </li>
                @endforeach
            </ul>
        @endforeach
    @endforeach
</body>
</html>
