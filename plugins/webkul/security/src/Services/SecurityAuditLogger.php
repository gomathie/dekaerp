<?php

namespace Webkul\Security\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Models\User;

class SecurityAuditLogger
{
    public function record(
        string $action,
        Model $subject,
        ?User $actor = null,
        ?int $companyId = null,
        array $before = [],
        array $after = [],
    ): void {
        $actor ??= auth()->user() instanceof User ? auth()->user() : null;
        $companyId ??= current_company_id();

        $context = [
            'action'       => $action,
            'actor_id'     => $actor?->getKey(),
            'subject_type' => $subject->getMorphClass(),
            'subject_id'   => $subject->getKey(),
            'company_id'   => $companyId,
            'before'       => $before,
            'after'        => $after,
        ];

        Log::notice('Security administration activity.', $context);

        if (! Schema::hasTable('chatter_messages') || ! $subject->exists) {
            return;
        }

        try {
            Message::withoutEvents(fn (): Message => Message::query()->create([
                'company_id'       => $companyId,
                'messageable_type' => $subject->getMorphClass(),
                'messageable_id'   => $subject->getKey(),
                'type'             => 'notification',
                'log_name'         => 'security',
                'body'             => $action,
                'event'            => $action,
                'causer_type'      => $actor?->getMorphClass(),
                'causer_id'        => $actor?->getKey(),
                'properties'       => array_filter([
                    'before' => $before,
                    'after'  => $after,
                ]),
            ]));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function denied(string $action, User $actor, array $context = []): void
    {
        Log::warning('Denied sensitive administration activity.', [
            'action'   => $action,
            'actor_id' => $actor->getKey(),
            ...$context,
        ]);
    }
}
