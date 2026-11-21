<?php

use App\Http\Controllers\BrandingController;
use App\Http\Controllers\SecureStorageController;
use Illuminate\Support\Facades\Route;

// Laravel's 'auth' middleware sends guests to route('login'). The old guard
// `if (! request()->getRequestUri() == '/login')` was always false (operator
// precedence), so this route never existed and every guest request to an
// 'auth' route, such as an expired session loading an avatar from
// /secure-storage, failed with a 500 (Sentry DEKAERP-1Y, 2026-11-21).
Route::redirect('/login', '/admin/login')
    ->name('login');

// Brand assets for the unauthenticated login and password-reset pages. The
// pattern allows a bare filename only - no slashes, so no caller-supplied key
// can reach the tenant bucket this disk shares with invoices and attachments.
// Everything else that lives on that disk is served by SecureStorageController
// below, which authorizes by company.
Route::get('/branding/{filename}', BrandingController::class)
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('branding.asset');

/*
 | Serves uploads from the private tenant bucket. Only reachable when the
 | public disk is object storage - with the local driver, files are served
 | straight from the public/storage symlink and this route is never linked to.
 |
 | 'auth' is not optional here: without it the company check in the controller
 | has no user to check against, and every object becomes world-readable.
 */
Route::middleware('auth')
    ->get('/secure-storage/{path}', SecureStorageController::class)
    ->where('path', '.*')
    ->name('secure-storage');
