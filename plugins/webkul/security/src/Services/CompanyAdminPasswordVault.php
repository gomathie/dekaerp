<?php

namespace Webkul\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;

/**
 * Readable copies of company administrators' passwords (user decision,
 * 2026-11-10; the alternatives, show-once and reset-only, were offered and
 * declined).
 *
 * The risk accepted: anyone holding both APP_KEY and the database can read
 * every stored password, and an administrator who reuses theirs elsewhere is
 * exposed there too. What keeps it bounded:
 *
 * - only users who hold the Company Admin role are stored, never staff or
 *   ordinary users;
 * - only a Super Admin may read one, after re-entering their own password;
 * - every read is audited with who and whose, never the password itself;
 * - the value lives in its own table, never on the user record.
 *
 * Every rule is here; screens call reveal() and repeat nothing.
 */
class CompanyAdminPasswordVault
{
    public const ONLY_SUPER_ADMIN = 'Only a Super Admin may view a stored password.';

    public const WRONG_PASSWORD = 'Your password is not correct.';

    public const NOT_STORED = 'No password is stored for this user. Set one to store it.';

    public function __construct(protected SecurityAuditLogger $audit) {}

    public function store(User $user, string $plainPassword): void
    {
        DB::table('security_user_password_vault')->upsert([
            'user_id'            => $user->getKey(),
            'encrypted_password' => Crypt::encryptString($plainPassword),
            'stored_at'          => now(),
        ], ['user_id'], ['encrypted_password', 'stored_at']);
    }

    /**
     * Called by User whenever a password is set. Company administrators are
     * stored; anyone else has any earlier copy removed, so a stale password
     * never outlives the role.
     */
    public function capture(User $user, string $plainPassword): void
    {
        if ($user->isCompanyAdmin()) {
            $this->store($user, $plainPassword);

            return;
        }

        $this->forget($user);
    }

    public function forget(User $user): void
    {
        DB::table('security_user_password_vault')->where('user_id', $user->getKey())->delete();
    }

    public function has(User $user): bool
    {
        return DB::table('security_user_password_vault')->where('user_id', $user->getKey())->exists();
    }

    public function mayReveal(User $actor): bool
    {
        return $actor->is_active && $actor->isSuperAdmin();
    }

    /**
     * @throws AuthorizationException when the actor is not an active Super Admin
     * @throws ValidationException when the actor's own password is wrong, or nothing is stored
     */
    public function reveal(User $actor, User $target, string $actorPassword, ?int $companyId = null): string
    {
        if (! $this->mayReveal($actor)) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN);
        }

        if (! Hash::check($actorPassword, (string) $actor->password)) {
            throw ValidationException::withMessages(['password' => self::WRONG_PASSWORD]);
        }

        $encrypted = DB::table('security_user_password_vault')
            ->where('user_id', $target->getKey())
            ->value('encrypted_password');

        if ($encrypted === null) {
            throw ValidationException::withMessages(['password' => self::NOT_STORED]);
        }

        // Who read whose password, never the password itself.
        $this->audit->record('security.user.password_revealed', $target, $actor, $companyId);

        return Crypt::decryptString($encrypted);
    }
}
