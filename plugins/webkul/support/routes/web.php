<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\ApiDocsController;
use Webkul\Support\Http\Controllers\ImageController;
use Webkul\Support\Http\Controllers\WhatsNewPdfController;

// Same access as the What's New page (signed-in panel users), D-WN-4. No
// 'auth' middleware: this app has no 'login' route, so a guest got a 500;
// the controller sends them to the panel login instead.
Route::get('admin/whats-new/pdf/{release?}', WhatsNewPdfController::class)
    ->middleware('web')
    ->name('support.whats-new.pdf');

// The full API reference, sign-in only (user, 2026-11-25). Replaces Scribe's
// own routes, which served a spec file production never had (Sentry
// DEKAERP-26). The names are Scribe's, so route('scribe') keeps working. No
// 'auth' middleware: the controller sends a guest to the panel login itself,
// on all three routes.
Route::middleware('web')->group(function (): void {
    $docsUrl = '/'.ltrim((string) config('scribe.laravel.docs_url', '/api/docs'), '/');

    Route::get($docsUrl, [ApiDocsController::class, 'page'])->name('scribe');
    Route::get($docsUrl.'.openapi', [ApiDocsController::class, 'openapi'])->name('scribe.openapi');
    Route::get($docsUrl.'.postman', [ApiDocsController::class, 'postman'])->name('scribe.postman');
});

Route::get('img/{path}', ImageController::class)
    ->where('path', '.*')
    ->name('support.image');
