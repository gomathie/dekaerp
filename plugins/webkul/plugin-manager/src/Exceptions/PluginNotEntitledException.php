<?php

namespace Webkul\PluginManager\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * A tenant reached something it is not entitled to.
 *
 * Extends AuthorizationException on purpose: `bootstrap/app.php` already renders
 * that as a 403 for `api/*`, `admin/api/*` and JSON requests, so an API client
 * gets a clean denial without a new exception renderer - and without the
 * "looks allowed, returns a 500" failure mode.
 */
class PluginNotEntitledException extends AuthorizationException
{
    public static function forCompany(string $plugin, int $companyId): self
    {
        return new self(__('The :plugin module is not enabled for this company.', [
            'plugin' => $plugin,
        ]).' (company '.$companyId.')');
    }

    /**
     * Fail closed: without a company there is nothing to check the grant
     * against, and treating "unknown" as "allowed" is how a module ends up
     * reachable by every tenant that can guess a URL.
     */
    public static function withoutCompany(string $plugin): self
    {
        return new self(__('The :plugin module cannot be used without an active company.', [
            'plugin' => $plugin,
        ]));
    }
}
