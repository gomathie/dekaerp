<?php

namespace Webkul\Support\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrossCompanyException extends Exception
{
    public static function forRecords(array $records): self
    {
        return new self(__('support::support.cross-company.records', [
            'records' => implode(', ', $records),
        ]));
    }

    public static function forCompany(int $companyId): self
    {
        // The id only: naming another client's company would confirm it exists.
        return new self(__('support::support.cross-company.company', ['company' => $companyId]));
    }

    public function title(): string
    {
        return __('support::support.cross-company.title');
    }

    public function render(Request $request): ?JsonResponse
    {
        if (! $request->expectsJson()) {
            return null;
        }

        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }
}
