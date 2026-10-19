@extends('logistics::stop-link.layout', ['title' => __('logistics::stop-link.expired.title')])

@section('body')
    <header>
        <h1>{{ __('logistics::stop-link.expired.title') }}</h1>
    </header>

    <main>
        <div class="card centred">
            <p class="note">{{ __('logistics::stop-link.expired.message') }}</p>
        </div>
    </main>
@endsection
