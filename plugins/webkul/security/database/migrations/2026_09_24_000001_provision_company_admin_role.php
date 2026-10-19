<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;

return new class extends Migration
{
    public function up(): void
    {
        app(CompanyAdminRoleProvisioner::class)->provision();
    }

    public function down(): void
    {
        // Roles may already be assigned in production, so rollback is non-destructive.
    }
};
