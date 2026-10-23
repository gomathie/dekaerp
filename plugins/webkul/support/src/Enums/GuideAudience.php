<?php

namespace Webkul\Support\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Webkul\Security\Models\User;

/**
 * Who a guide category is written for.
 *
 * The three cases map onto roles this application already has rather than onto
 * a new concept: Role::COMPANY_ADMIN administers one tenant, while a super
 * admin and Role::MULTI_COMPANY_ADMIN administer the installation. That is why
 * the admin guide is not super-admin-only - the person it is written for is the
 * customer's own administrator.
 *
 * Access is cumulative upward: an administrator also needs the end-user pages,
 * so visibleTo() returns every audience at or below the viewer's level.
 */
enum GuideAudience: string implements HasIcon, HasLabel
{
    case User = 'user';

    case Admin = 'admin';

    case SuperAdmin = 'superadmin';

    public function getLabel(): string
    {
        return __('support::enums/guide-audience.'.$this->value);
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::User       => 'heroicon-o-book-open',
            self::Admin      => 'heroicon-o-shield-check',
            self::SuperAdmin => 'heroicon-o-wrench-screwdriver',
        };
    }

    /**
     * The default for a category that does not declare one.
     *
     * Every category shipped before audiences existed is end-user material, and
     * defaulting to the narrowest audience is also the safe direction: an
     * unmarked category can never leak administrative content.
     */
    public static function default(): self
    {
        return self::User;
    }

    public static function tryFromValue(mixed $value): ?self
    {
        return is_string($value) ? self::tryFrom($value) : null;
    }

    /**
     * Audiences this user may read, narrowest first.
     *
     * @return array<int, self>
     */
    public static function visibleTo(?object $user): array
    {
        if (! $user instanceof User) {
            return [];
        }

        $audiences = [self::User];

        $administersInstallation = $user->isSuperAdmin() || $user->isMultiCompanyAdmin();

        if ($administersInstallation || $user->isCompanyAdmin()) {
            $audiences[] = self::Admin;
        }

        if ($administersInstallation) {
            $audiences[] = self::SuperAdmin;
        }

        return $audiences;
    }

    public static function allowsUser(?object $user, ?self $audience): bool
    {
        return in_array($audience ?? self::default(), static::visibleTo($user), true);
    }
}
