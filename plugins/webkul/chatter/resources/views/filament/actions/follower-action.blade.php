<div class="max-h-[400px] w-full overflow-y-auto text-gray-900 dark:text-gray-100">
    {{-- UF-031: a follower row whose contact row is gone entirely is skipped; a deleted (archived) contact is shown greyed. --}}
    @forelse($record->followers->filter(fn ($follower) => $follower->partner !== null) as $follower)
        @php($isDeleted = $follower->partner->trashed())
        <div
            wire:key="non-follower-{{ $follower->partner->id }}"
            @class([
                'group flex items-center justify-between rounded-lg p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60',
                'opacity-60' => $isDeleted,
            ])
        >
            <div class="flex items-center gap-3">
                <x-filament-panels::avatar.user
                    size="md"
                    :user="$follower->partner"
                />
                <div>
                    <h3 @class([
                        'font-medium',
                        'text-gray-900 dark:text-gray-100' => ! $isDeleted,
                        'text-gray-500 dark:text-gray-400' => $isDeleted,
                    ])>
                        {{ $follower->partner->name }}
                        @if ($isDeleted)
                            <span class="text-sm font-normal">{{ __('chatter::filament/resources/actions/chatter/follower-action.setup.deleted') }}</span>
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $follower->partner->email }}</p>
                </div>
            </div>

            <x-filament::icon-button
                wire:click="removeFollower({{ $follower->partner->id }})"
                icon="heroicon-s-user-minus"
                color="danger"
                :tooltip="trans('Remove Follower')"
            />
        </div>
    @empty
        <div class="flex flex-col items-center justify-center p-4 text-center text-gray-500 dark:text-gray-400">
            <x-filament::icon
                icon="heroicon-o-user-group"
                class="mb-3 h-8 w-8 text-gray-400 dark:text-gray-500"
            />
            <p class="text-sm">
                {{ __('No followers have been added yet.') }}
            </p>
        </div>
    @endforelse
</div>
