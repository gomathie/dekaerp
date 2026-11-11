<?php

use Illuminate\Support\Facades\Route;
use Webkul\Security\Http\Controllers\ImpersonationController;
use Webkul\Security\Livewire\AcceptInvitation;

Route::middleware(['web'])->group(function () {
    Route::post('impersonation/end', [ImpersonationController::class, 'end'])
        ->middleware('auth')
        ->name('security.impersonation.end');

    Route::middleware('signed')
        ->get('invitation/{invitation}/accept', AcceptInvitation::class)
        ->name('security.invitation.accept');
});
