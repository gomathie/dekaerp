<?php

namespace Webkul\Logistics\Models\Concerns;

use Webkul\Support\Traits\InheritsParentCompany as SupportInheritsParentCompany;

/**
 * Kept as an alias; the rule now lives in `Webkul\Support\Traits`.
 *
 * It was written here for Logistics child rows, but it states a rule that applies
 * to every plugin - and core packages cannot depend on Logistics, which is
 * optional and can be uninstalled. Moved to Support on 2026-09-29 so
 * `Inventory\Models\MoveLine` and `Inventory\Models\ProductQuantity` can use the
 * same implementation instead of a second copy of the same business rule.
 *
 * New models should use `Webkul\Support\Traits\InheritsParentCompany` directly.
 * This alias exists so the six Logistics models that already use it keep working
 * unchanged, and so it can be retired in a later pass rather than mid-review.
 */
trait InheritsParentCompany
{
    use SupportInheritsParentCompany;
}
