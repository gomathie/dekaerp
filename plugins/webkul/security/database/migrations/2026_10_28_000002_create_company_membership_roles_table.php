<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tenant roles, scoped to the company the user holds them in.
     *
     * Deliberately **not** Spatie's `model_has_roles`. That table has no company
     * column and cannot get one while `permission.teams` is false
     * (config/permission.php:126), which is exactly why a role assigned for
     * company A also applied in company B. Turning teams on instead would
     * change how every existing role resolves, across every panel, in one step.
     *
     * Platform roles (Super Admin, Multi-Company Admin, the install
     * administrator) intentionally stay global in `model_has_roles`: they
     * administer the installation, not one tenant.
     *
     * A user may hold several tenant roles in one company, so this is a pivot
     * rather than a `role_id` on the membership row - moving to a single column
     * would silently drop the second role of anyone who has two today.
     *
     * No model uses BelongsToCompany here on purpose: the table must be
     * readable for a record's own company, which is not always the active one.
     *
     * A role row cannot outlive its membership. The composite key references
     * the (user_id, company_id) pair made unique by the previous migration, so
     * removing someone from a company removes the roles they held there - in
     * the database, whatever path removed the membership. Without it a former
     * member kept passing `hasPermissionInCompany()` for that company.
     */
    public function up(): void
    {
        Schema::create('company_membership_roles', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'company_id', 'role_id'], 'company_membership_roles_unique');
            $table->index(['company_id', 'role_id'], 'company_membership_roles_company_role_index');

            $table->foreign(['user_id', 'company_id'], 'company_membership_roles_membership_foreign')
                ->references(['user_id', 'company_id'])
                ->on('user_allowed_companies')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_membership_roles');
    }
};
