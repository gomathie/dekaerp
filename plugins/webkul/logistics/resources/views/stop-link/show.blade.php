@extends('logistics::stop-link.layout')

@section('body')
    <header>
        <h1>{{ __('logistics::stop-link.title') }}</h1>
        <p>
            {{ __('logistics::stop-link.shipment') }} {{ $stop->shipment?->name }}
            @if ($stop->contact_name)
                &middot; {{ $stop->contact_name }}
            @endif
        </p>
    </header>

    <main>
        @if ($errors->any())
            <div class="errors" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('logistics.stop-link.store', ['token' => $token]) }}"
              enctype="multipart/form-data"
              id="pod-form">
            @csrf

            <div class="card">
                <div class="field">
                    <label for="recipient_name">{{ __('logistics::stop-link.form.recipient-name') }}</label>
                    <p class="help">{{ __('logistics::stop-link.form.recipient-name-help') }}</p>
                    <input type="text"
                           id="recipient_name"
                           name="recipient_name"
                           value="{{ old('recipient_name') }}"
                           maxlength="255"
                           autocomplete="off"
                           required>
                </div>

                <div class="field">
                    <label for="photo">{{ __('logistics::stop-link.form.photo') }}</label>
                    <p class="help">{{ __('logistics::stop-link.form.photo-help') }}</p>
                    <input type="file"
                           id="photo"
                           name="photo"
                           accept="image/jpeg,image/png,image/webp"
                           capture="environment">
                </div>

                <div class="field">
                    <label for="signature-pad">{{ __('logistics::stop-link.form.signature') }}</label>
                    <p class="help">{{ __('logistics::stop-link.form.signature-help') }}</p>
                    <canvas id="signature-pad"></canvas>
                    <p style="margin-top:8px">
                        <button type="button" class="secondary" id="signature-clear">
                            {{ __('logistics::stop-link.form.signature-clear') }}
                        </button>
                    </p>
                    <input type="hidden" name="signature" id="signature">
                </div>

                <div class="field">
                    <label for="notes">{{ __('logistics::stop-link.form.notes') }}</label>
                    <textarea id="notes" name="notes" maxlength="2000">{{ old('notes') }}</textarea>
                </div>

                <div class="field">
                    <button type="button" class="secondary" id="locate">
                        {{ __('logistics::stop-link.form.location') }}
                    </button>
                    <p class="note" id="locate-status" role="status"></p>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="accuracy_m" id="accuracy_m">
                </div>
            </div>

            <p style="margin-top:16px">
                <button type="submit" id="submit">{{ __('logistics::stop-link.form.submit') }}</button>
            </p>
        </form>
    </main>

    <script>
        (function () {
            'use strict';

            // Signature: drawn on a canvas sized to the device's pixel ratio so
            // it is not blurry, and sent as a PNG data URL in a hidden field.
            var canvas = document.getElementById('signature-pad');
            var context = canvas.getContext('2d');
            var drawing = false;
            var signed = false;

            function size() {
                var ratio = window.devicePixelRatio || 1;
                var rect = canvas.getBoundingClientRect();

                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                context.scale(ratio, ratio);
                context.lineWidth = 2;
                context.lineCap = 'round';
                context.strokeStyle = '#2f3640';
            }

            function position(event) {
                var rect = canvas.getBoundingClientRect();
                var point = event.touches ? event.touches[0] : event;

                return { x: point.clientX - rect.left, y: point.clientY - rect.top };
            }

            function start(event) {
                event.preventDefault();
                drawing = true;
                signed = true;

                var point = position(event);

                context.beginPath();
                context.moveTo(point.x, point.y);
            }

            function move(event) {
                if (!drawing) {
                    return;
                }

                event.preventDefault();

                var point = position(event);

                context.lineTo(point.x, point.y);
                context.stroke();
            }

            function end() {
                drawing = false;
            }

            size();
            window.addEventListener('resize', function () {
                // Resizing clears the canvas, so the flag has to go with it.
                size();
                signed = false;
            });

            canvas.addEventListener('pointerdown', start);
            canvas.addEventListener('pointermove', move);
            canvas.addEventListener('pointerup', end);
            canvas.addEventListener('pointerleave', end);

            document.getElementById('signature-clear').addEventListener('click', function () {
                context.clearRect(0, 0, canvas.width, canvas.height);
                signed = false;
            });

            // Location is optional and never blocks submission: a driver with
            // location switched off must still be able to confirm the delivery.
            document.getElementById('locate').addEventListener('click', function () {
                var status = document.getElementById('locate-status');

                if (!navigator.geolocation) {
                    status.textContent = @json(__('logistics::stop-link.form.location-failed'));

                    return;
                }

                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('accuracy_m').value = position.coords.accuracy;
                    status.textContent = @json(__('logistics::stop-link.form.location-attached'));
                    status.className = 'note ok';
                }, function () {
                    status.textContent = @json(__('logistics::stop-link.form.location-failed'));
                });
            });

            document.getElementById('pod-form').addEventListener('submit', function () {
                if (signed) {
                    document.getElementById('signature').value = canvas.toDataURL('image/png');
                }

                // Guard against a double submission: the link is one-time, and a
                // second POST would land on a spent token and look like an error.
                var submit = document.getElementById('submit');

                submit.disabled = true;
                submit.textContent = @json(__('logistics::stop-link.form.submitting'));
            });
        }());
    </script>
@endsection
