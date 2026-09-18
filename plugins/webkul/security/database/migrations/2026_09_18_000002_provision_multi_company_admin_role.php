<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;

return new class extends Migration
{
    public function up(): void
    {
        app(MultiCompanyAdminRoleProvisioner::class)->provision();
    }

    public function down(): void
    {
        // Roles may already be assigned in production, so rollback is non-destructive.
    }
};
