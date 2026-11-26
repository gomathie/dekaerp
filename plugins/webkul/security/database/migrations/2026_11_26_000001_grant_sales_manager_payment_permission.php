<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Security\Services\RoleTemplateProvisioner;

return new class extends Migration
{
    /**
     * The existing "Sales Manager" role records payments too (user,
     * 2026-11-26: sales staff take money at the counter, and Record payment
     * now needs create_invoice_payment). New installations get it when the
     * template is created. Run once, from a migration, so a later removal on
     * the Roles screen is not undone by every deploy. Adding only; nothing
     * happens without the role or the permission.
     */
    public function up(): void
    {
        app(RoleTemplateProvisioner::class)->addExtraPermissions('Sales Manager');
    }

    /**
     * Nothing to undo safely: the grant cannot be told apart from one an
     * administrator gave by hand.
     */
    public function down(): void {}
};
