<?php

namespace Webkul\Logistics\Support;

use Webkul\Support\Support\CompanyStorage as BaseCompanyStorage;

/**
 * Moved to Webkul\Support\Support\CompanyStorage (2026-11-26) so Expenses
 * can store receipts the same way. Kept under this name for Logistics'
 * existing callers; one implementation.
 */
class CompanyStorage extends BaseCompanyStorage {}
