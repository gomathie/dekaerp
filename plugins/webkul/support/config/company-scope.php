<?php

use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Invoice\Models\Bill;
use Webkul\Invoice\Models\CreditNote;
use Webkul\Invoice\Models\Invoice;
use Webkul\Invoice\Models\Refund;
use Webkul\Project\Models\Project;
use Webkul\Purchase\Models\Order;

/**
 * Models scoped to the active company chosen in the topbar company switcher.
 *
 * Every model listed here must have a `company_id` column. Records with a
 * null `company_id` are treated as shared across companies and stay visible.
 * Global scopes added at runtime do not inherit, so subclasses (e.g. the
 * invoice plugin's Move subclasses) must be listed individually.
 *
 * For your own models you can use the Webkul\Support\Traits\BelongsToCompany
 * trait instead of listing them here.
 */

return [
    'models' => [
        Move::class,
        Payment::class,
        Operation::class,
        Warehouse::class,
        Bill::class,
        CreditNote::class,
        Invoice::class,
        Webkul\Invoice\Models\Payment::class,
        Refund::class,
        Project::class,
        Order::class,
        Webkul\Sale\Models\Order::class,

        // Scoping employees also hides them from HR flows (time off,
        // timesheets) when the active company differs from the employee's
        // company. Enable deliberately if that is what you want:
        // Webkul\Employee\Models\Employee::class,
    ],
];
