<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

class ReceiptRequired extends RuntimeException
{
    public static function forCategory(string $category): self
    {
        return new self(__('logistics::exceptions.receipt-required', [
            'category' => $category,
        ]));
    }
}
