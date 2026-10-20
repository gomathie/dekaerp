@extends('logistics::stop-link.layout')

@section('body')
    <main>
        {{--
            Shown briefly while the JS reads the fragment. If JS is off or the
            fragment is absent the driver sees the "invalid link" state.
        --}}
        <div id="loading" class="centred">
            <p>{{ __('logistics::stop-link.landing.loading') }}</p>
        </div>
        <div id="invalid" class="centred" hidden>
            <h2>{{ __('logistics::stop-link.landing.invalid-title') }}</h2>
            <p>{{ __('logistics::stop-link.landing.invalid-body') }}</p>
        </div>
    </main>

    {{--
        The token lives in the URL fragment and is never transmitted in an HTTP
        request line. This form reads it from location.hash via JavaScript and
        submits it as a POST body field, which keeps the token out of access
        logs at every layer (app server, edge CDN, load balancer).
    --}}
    <form id="exchange-form"
          method="POST"
          action="{{ route('logistics.stop-link.exchange') }}"
          hidden>
        @csrf
        <input type="hidden" name="token" id="exchange-token">
    </form>

    <script>
        (function () {
            'use strict';

            var token = location.hash.slice(1);

            // Clear the fragment from the address bar so the token is not
            // preserved in browser history or passed on if the driver shares
            // the page URL.
            history.replaceState('', document.title, location.pathname);

            if (!token) {
                document.getElementById('loading').hidden = true;
                document.getElementById('invalid').hidden = false;

                return;
            }

            document.getElementById('exchange-token').value = token;
            document.getElementById('exchange-form').submit();
        }());
    </script>
@endsection
