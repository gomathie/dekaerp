<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'he', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>{{ $title ?? __('logistics::stop-link.title') }}</title>

    {{--
        Styles are inline rather than pulled from the panel's stylesheet. This
        page opens on a driver's phone, often on a bad connection at a customer's
        gate, and it must not depend on the admin asset pipeline. noindex and
        no-referrer because the URL is a credential: it must not reach a search
        engine or leak through a Referer header.
    --}}
    <style>
        :root {
            --brand: #1a4587;
            --ink: #2f3640;
            --muted: #667085;
            --line: #d9dee5;
            --bg: #f5f6f8;
            --card: #ffffff;
            --danger: #b42318;
            --ok: #067647;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0 0 env(safe-area-inset-bottom, 0px);
            background: var(--bg);
            color: var(--ink);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }

        header {
            padding: 20px 16px calc(20px + env(safe-area-inset-top, 0px));
            background: var(--brand);
            color: #fff;
        }

        header h1 { margin: 0; font-size: 20px; }
        header p { margin: 6px 0 0; font-size: 14px; opacity: .9; }

        main { padding: 16px; }

        .card {
            padding: 16px;
            border-radius: 10px;
            background: var(--card);
            box-shadow: 0 1px 2px rgba(16, 24, 40, .08);
        }

        .card + .card { margin-top: 12px; }

        label { display: block; margin-bottom: 4px; font-size: 14px; font-weight: 600; }
        .help { margin: 0 0 8px; color: var(--muted); font-size: 13px; }

        input[type="text"], textarea, input[type="file"] {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: inherit;
            font: inherit;
        }

        textarea { min-height: 84px; resize: vertical; }

        .field + .field { margin-top: 18px; }

        canvas {
            display: block;
            width: 100%;
            height: 180px;
            border: 1px dashed var(--line);
            border-radius: 8px;
            background: #fff;
            touch-action: none;
        }

        button {
            width: 100%;
            min-height: 52px;
            padding: 14px;
            border: 0;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font: inherit;
            font-weight: 600;
        }

        button[disabled] { opacity: .6; }

        button.secondary {
            min-height: 40px;
            padding: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            font-weight: 500;
        }

        .errors {
            margin: 0 0 12px;
            padding: 12px;
            border-radius: 8px;
            background: #fef3f2;
            color: var(--danger);
            font-size: 14px;
        }

        .errors ul { margin: 0; padding-inline-start: 18px; }

        .note { color: var(--muted); font-size: 13px; }
        .ok { color: var(--ok); }

        .centred { padding: 48px 16px; text-align: center; }
        .centred h2 { margin: 0 0 8px; font-size: 20px; }
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
