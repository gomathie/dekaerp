<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Expense as BaseExpense;

/**
 * The Expenses screen's model, as Bill is for bills: the Invoices policy and
 * the invoice_* permissions attach to this class.
 */
class Expense extends BaseExpense {}
