{{--
    One endpoint of the Developers tab. $endpoint comes from ApiReference (plain
    text only), $copy is the shared copy routine, $appUrl the server address and
    $companyId the viewer's current company (the X-Company-Id example).
--}}
@php
    // Built here, not by a public page method: a Livewire page's public
    // methods can be called from the browser with any argument.
    $samples = \Webkul\Support\Services\ApiCodeSamples::for($endpoint, $appUrl, $companyId);
    $url = $appUrl.$endpoint['path'];
    $headers = [];

    if ($endpoint['authenticated']) {
        $headers[] = ['name' => 'Authorization', 'description' => __('support::filament/pages/help-developers.headers.authorization'), 'required' => true];
        $headers[] = ['name' => 'X-Company-Id', 'description' => __('support::filament/pages/help-developers.headers.company'), 'required' => false];
    }

    $headers[] = ['name' => 'Accept', 'description' => __('support::filament/pages/help-developers.headers.accept'), 'required' => true];

    $languages = ['curl' => 'cURL', 'javascript' => 'JavaScript', 'python' => 'Python', 'php' => 'PHP'];
@endphp

<x-filament::section id="api-endpoint-{{ $endpoint['id'] }}" class="scroll-mt-24">
    <div class="flex min-w-0 flex-col gap-5">
        <div class="flex flex-wrap items-center gap-2">
            <x-filament::badge :color="\Webkul\Support\Services\ApiReference::methodColor($endpoint['method'])">
                {{ $endpoint['method'] }}
            </x-filament::badge>

            <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $endpoint['title'] }}</h3>

            @unless ($endpoint['authenticated'])
                <x-filament::badge color="gray" size="sm">{{ __('support::filament/pages/help-developers.endpoint.no_token') }}</x-filament::badge>
            @endunless
        </div>

        <div
            class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-white/5 dark:ring-white/10"
            x-data="{ {{ $copy }} }"
        >
            <code class="min-w-0 flex-1 break-all font-mono text-sm text-gray-950 dark:text-white" x-ref="text">{{ $url }}</code>

            <x-filament::link tag="button" type="button" size="sm" icon="heroicon-m-clipboard-document" x-on:click="copy($refs.text.textContent)">
                <span x-show="! copied">{{ __('support::filament/pages/help-developers.copy') }}</span>
                <span x-show="copied" x-cloak>{{ __('support::filament/pages/help-developers.copied') }}</span>
            </x-filament::link>
        </div>

        @if (filled($endpoint['description']) && $endpoint['description'] !== $endpoint['title'])
            <p class="whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $endpoint['description'] }}</p>
        @endif

        <div class="flex flex-col gap-2">
            <h4 class="text-sm font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.endpoint.parameters') }}</h4>

            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full min-w-[32rem] divide-y divide-gray-200 text-left text-sm dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-3 py-2 font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.endpoint.name') }}</th>
                            <th scope="col" class="px-3 py-2 font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.endpoint.type') }}</th>
                            <th scope="col" class="px-3 py-2 font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.endpoint.description') }}</th>
                            <th scope="col" class="px-3 py-2 font-semibold text-gray-950 dark:text-white"><span class="sr-only">{{ __('support::filament/pages/help-developers.endpoint.required') }}</span></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($headers as $header)
                            <tr>
                                <td class="px-3 py-2 align-top font-mono text-xs text-gray-950 dark:text-white">{{ $header['name'] }}</td>
                                <td class="px-3 py-2 align-top text-xs text-gray-500 dark:text-gray-400">string · {{ __('support::filament/pages/help-developers.in.header') }}</td>
                                <td class="px-3 py-2 align-top text-gray-600 dark:text-gray-300">{{ $header['description'] }}</td>
                                <td class="px-3 py-2 align-top text-xs">
                                    @include('support::pages.partials.help-api-required', ['required' => $header['required']])
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($endpoint['parameters'] as $parameter)
                            <tr>
                                <td class="px-3 py-2 align-top font-mono text-xs text-gray-950 dark:text-white">{{ $parameter['name'] }}</td>
                                <td class="px-3 py-2 align-top text-xs text-gray-500 dark:text-gray-400">{{ $parameter['type'] }} · {{ __('support::filament/pages/help-developers.in.'.$parameter['in']) }}</td>
                                <td class="whitespace-pre-line px-3 py-2 align-top text-gray-600 dark:text-gray-300">{{ $parameter['description'] }}</td>
                                <td class="px-3 py-2 align-top text-xs">
                                    @include('support::pages.partials.help-api-required', ['required' => $parameter['required']])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-gray-950 ring-1 ring-gray-950/10 dark:ring-white/10" x-data="{ lang: 'curl', {{ $copy }} }">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 px-2 py-1.5">
                <div class="flex flex-wrap gap-1" role="tablist" aria-label="{{ __('support::filament/pages/help-developers.endpoint.samples') }}">
                    @foreach ($languages as $language => $label)
                        <button
                            type="button"
                            role="tab"
                            class="rounded-md px-2.5 py-1 text-xs font-medium"
                            x-on:click="lang = '{{ $language }}'"
                            x-bind:aria-selected="lang === '{{ $language }}' ? 'true' : 'false'"
                            x-bind:class="lang === '{{ $language }}' ? 'bg-white/15 text-white' : 'text-gray-400 hover:text-white'"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-gray-300 hover:text-white"
                    x-on:click="copy($refs['code-' + lang].textContent)"
                >
                    <x-filament::icon icon="heroicon-m-clipboard-document" class="size-4" />
                    <span x-show="! copied">{{ __('support::filament/pages/help-developers.copy') }}</span>
                    <span x-show="copied" x-cloak>{{ __('support::filament/pages/help-developers.copied') }}</span>
                </button>
            </div>

            @foreach ($languages as $language => $label)
                <pre
                    class="max-h-96 overflow-auto p-4 text-xs leading-relaxed text-gray-100"
                    x-show="lang === '{{ $language }}'"
                    @if ($language !== 'curl') x-cloak @endif
                ><code x-ref="code-{{ $language }}">{{ $samples[$language] }}</code></pre>
            @endforeach
        </div>

        @if ($endpoint['response'])
            <div class="flex flex-col gap-2">
                <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ __('support::filament/pages/help-developers.endpoint.response', ['status' => $endpoint['response']['status']]) }}
                </h4>

                <pre class="max-h-96 overflow-auto rounded-lg bg-gray-50 p-4 text-xs leading-relaxed text-gray-800 ring-1 ring-gray-200 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10"><code>{{ $endpoint['response']['content'] }}</code></pre>
            </div>
        @endif

        <p class="text-xs text-gray-500 dark:text-gray-400">
            <span class="font-semibold">{{ __('support::filament/pages/help-developers.endpoint.other_answers') }}</span>
            @if ($endpoint['authenticated'])
                {{ __('support::filament/pages/help-developers.answers.401') }} ·
                {{ __('support::filament/pages/help-developers.answers.403') }} ·
            @endif
            @unless ($endpoint['method'] === 'GET')
                {{ __('support::filament/pages/help-developers.answers.422') }} ·
            @endunless
            {{ __('support::filament/pages/help-developers.answers.429') }}
        </p>
    </div>
</x-filament::section>
