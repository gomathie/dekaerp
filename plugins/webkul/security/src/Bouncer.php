<?php

namespace Webkul\Security;

use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class Bouncer
{
    protected ?string $authorizedUserIdsCacheKey = null;

    protected ?array $authorizedUserIdsCache = null;

    protected bool $hasAuthorizedUserIdsCache = false;

    /**
     * Return user IDs authorized for the current user.
     */
    public function getAuthorizedUserIds(): ?array
    {
        $user = auth()->user();

        if (! $user) {
            $this->clearCache();

            return null;
        }

        $cacheKey = $this->cacheKey($user);

        if ($this->hasAuthorizedUserIdsCache && $this->authorizedUserIdsCacheKey === $cacheKey) {
            return $this->authorizedUserIdsCache;
        }

        if ($user->resource_permission == PermissionType::GLOBAL) {
            $authorizedUserIds = null;
        } elseif ($user->resource_permission == PermissionType::GROUP) {
            $authorizedUserIds = $this->getCurrentAccessibleUserIds($user);
        } else {
            $authorizedUserIds = [$user->id];
        }

        $this->authorizedUserIdsCacheKey = $cacheKey;
        $this->authorizedUserIdsCache = $authorizedUserIds;
        $this->hasAuthorizedUserIdsCache = true;

        return $authorizedUserIds;
    }

    protected function cacheKey(User $user): string
    {
        $permission = $user->resource_permission instanceof PermissionType
            ? $user->resource_permission->value
            : (string) $user->resource_permission;

        $teamIds = $permission === PermissionType::GROUP->value
            ? $user->teams()->orderBy('teams.id')->pluck('teams.id')->implode(',')
            : '';

        return implode(':', [$user->getKey(), $permission, $teamIds]);
    }

    protected function clearCache(): void
    {
        $this->authorizedUserIdsCacheKey = null;
        $this->authorizedUserIdsCache = null;
        $this->hasAuthorizedUserIdsCache = false;
    }

    /**
     * Get user IDs of the current user's groups.
     */
    protected function getCurrentAccessibleUserIds(User $user): array
    {
        return User::query()
            ->select('users.id')
            ->leftJoin('user_team', 'users.id', '=', 'user_team.user_id')
            ->leftJoin('teams', 'user_team.team_id', '=', 'teams.id')
            ->whereIn('teams.id', $user->teams()->pluck('id'))
            ->pluck('users.id')
            ->toArray();
    }
}
