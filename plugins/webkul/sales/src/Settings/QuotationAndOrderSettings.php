<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;

class QuotationAndOrderSettings extends Settings
{
    public int $default_quotation_validity;

    public bool $enable_lock_confirm_sales;

    /** Default for "Customer has taken the goods" on an invoice that creates its order. */
    public bool $goods_leave_on_invoice = true;

    public static function group(): string
    {
        return 'sales_quotation_and_orders';
    }
}
