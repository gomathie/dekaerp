<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Default answer to "Customer has taken the goods" on an invoice that
     * creates its sales order (user, 2026-11-22, option D). True suits the
     * counter sales this feature was asked for; a company that delivers later
     * turns it off.
     */
    public function up(): void
    {
        if (! $this->migrator->exists('sales_quotation_and_orders.goods_leave_on_invoice')) {
            $this->migrator->add('sales_quotation_and_orders.goods_leave_on_invoice', true);
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('sales_quotation_and_orders.goods_leave_on_invoice');
    }
};
