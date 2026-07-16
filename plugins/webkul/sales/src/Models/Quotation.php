<?php

namespace Webkul\Sale\Models;

use Webkul\Sale\Models\Order as BaseOrder;

class Quotation extends BaseOrder
{
    public function getModelTitle(): string
    {
        return __('sales::models/quotation.title');
    }
}
