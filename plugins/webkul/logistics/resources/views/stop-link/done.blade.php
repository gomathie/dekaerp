@extends('logistics::stop-link.layout', ['title' => __('logistics::stop-link.done.title')])

@section('body')
    <header>
        <h1>{{ __('logistics::stop-link.done.title') }}</h1>
    </header>

    <main>
        <div class="card centred">
            <h2>{{ __('logistics::stop-link.done.title') }}</h2>
            <p class="note">{{ __('logistics::stop-link.done.message') }}</p>
        </div>
    </main>
@endsection
