<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Enums\PermissionType;

/**
 * Everyone in a company sees that company's records (user decision, 2026-09-28).
 *
 * `users.resource_permission` defaulted to `individual`, which makes
 * `OwnershipScope` filter the eight document models - invoices, bills, sales and
 * purchase orders, stock operations, manufacturing orders, projects and tasks -
 * down to rows the user created or was assigned. Colleagues in the same company
 * could not see each other's invoices, which is not the intended product
 * behaviour. `CompanyScope` is the boundary that matters and is unaffected.
 *
 * Two changes, because the old value arrived by two different routes:
 *
 *  - the column default, which is what any create that omits the field gets;
 *  - the existing `individual` rows, which is what customers are living with now.
 *
 * `group` rows are deliberately left alone. Nobody ever chose `individual` - it
 * was the column default and a hard-coded value in `UserInvitationService` - but
 * `group` has to be picked in the form *and* given a team, so it is a real
 * decision by whoever configured that user. Widening it here would silently
 * override them. See docs/company-admin-role-plan.md section 4h.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->setDefault(PermissionType::GLOBAL->value);

        DB::table('users')
            ->where('resource_permission', PermissionType::INDIVIDUAL->value)
            ->update(['resource_permission' => PermissionType::GLOBAL->value]);
    }

    /**
     * Restores the old default only.
     *
     * The rows are deliberately **not** reverted: once the default is company-wide,
     * there is no way to tell which users were `individual` because a customer
     * wanted them narrow and which were `individual` only because of the old
     * default. Putting every user back would be a guess, and a guess that silently
     * hides records from people who can currently see them. Anyone who needs a
     * narrower view is set back per user, which is a deliberate act as it should
     * be.
     */
    public function down(): void
    {
        $this->setDefault(PermissionType::INDIVIDUAL->value);
    }

    /**
     * Changes only the column default, with raw DDL.
     *
     * Not `$table->enum(...)->change()`: Laravel renders an enum change on
     * PostgreSQL as `type varchar(255) check (...)` in a single `ALTER COLUMN`,
     * which Postgres rejects with `syntax error at or near "check"`. The column's
     * type and check constraint are not changing here anyway - only the default -
     * so the narrow statement is both correct and less invasive. The syntax is
     * also valid on MySQL 8.
     */
    private function setDefault(string $permission): void
    {
        DB::statement(sprintf(
            'ALTER TABLE users ALTER COLUMN resource_permission SET DEFAULT %s',
            DB::getPdo()->quote($permission),
        ));
    }
};
