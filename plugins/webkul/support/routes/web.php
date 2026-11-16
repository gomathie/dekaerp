<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\ImageController;
use Webkul\Support\Http\Controllers\WhatsNewPdfController;

// Same access as the What's New page (signed-in panel users), D-WN-4. No
// 'auth' middleware: this app has no 'login' route, so a guest got a 500;
// the controller sends them to the panel login instead.
Route::get('admin/whats-new/pdf/{release?}', WhatsNewPdfController::class)
    ->middleware('web')
    ->name('support.whats-new.pdf');

Route::get('img/{path}', ImageController::class)
    ->where('path', '.*')
    ->name('support.image');
