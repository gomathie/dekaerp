<?php

namespace Webkul\Support\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Http\Requests\SetCompanyContextRequest;
use Webkul\Support\Services\CompanyContext;

class CompanyContextController extends Controller
{
    public function set(
        SetCompanyContextRequest $request,
        CompanyContext $context,
        SecurityAuditLogger $audit,
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();
        $before = $context->activeIds();
        $beforeAllCompanies = $context->allCompaniesActive();

        if ($request->input('action') === 'all') {
            try {
                $context->setAllCompanies();
            } catch (AuthorizationException $exception) {
                $audit->denied('security.company_context.switch_denied', $user, [
                    'requested_scope' => 'all_companies',
                ]);

                throw $exception;
            }

            $audit->record(
                'security.company_context.switched',
                $user,
                $user,
                $context->currentId(),
                ['company_ids' => $before, 'all_companies' => $beforeAllCompanies],
                ['company_ids' => $context->activeIds(), 'all_companies' => true],
            );

            return back();
        }

        if ($request->input('action') === 'reset') {
            $context->reset();

            $after = $context->activeIds();
            $audit->record(
                'security.company_context.switched',
                $user,
                $user,
                $after[0] ?? null,
                ['company_ids' => $before, 'all_companies' => $beforeAllCompanies],
                ['company_ids' => $after, 'all_companies' => $context->allCompaniesActive()],
            );

            return back();
        }

        $ids = array_map('intval', (array) $request->input('companies', []));

        try {
            $context->setActive($ids, $request->integer('current') ?: null);
        } catch (AuthorizationException $exception) {
            $audit->denied('security.company_context.switch_denied', $user, [
                'requested_company_ids' => $ids,
            ]);

            throw $exception;
        }

        $after = $context->activeIds();
        $audit->record(
            'security.company_context.switched',
            $user,
            $user,
            $after[0] ?? null,
            ['company_ids' => $before, 'all_companies' => $beforeAllCompanies],
            ['company_ids' => $after, 'all_companies' => $context->allCompaniesActive()],
        );

        return back();
    }
}
