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

            /*
             * A group user with no team can only be themselves.
             *
             * getCurrentAccessibleUserIds() resolves through
             * `whereIn('teams.id', $user->teams()->pluck('id'))`, so a user in no
             * team matches nothing and this comes back empty - and
             * OwnershipScope treats an empty list as **no restriction** and skips
             * itself entirely. The effect was that a `group` user without a team
             * saw everything, exactly as if they were `global`, silently.
             *
             * The form requires a team when `group` is chosen, so this is not
             * reachable by creating a user through the panel. It is reachable by
             * the team being deleted afterwards, or the user being removed from
             * it - neither of which should hand them a wider view than they had.
             * Fails closed to their own rows, which is what `group` degrades to
             * when the group is empty.
             */
            if (empty($authorizedUserIds)) {
                $authorizedUserIds = [$user->id];
            }
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
