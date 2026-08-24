<?php

use App\Http\Controllers\SecureStorageController;
use Illuminate\Support\Facades\Route;

if (! request()->getRequestUri() == '/login') {
    Route::redirect('/login', '/admin/login')
        ->name('login');
}

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
