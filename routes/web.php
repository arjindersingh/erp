<?php

use App\Http\Controllers\SystemHealthController;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenant::class)->group(function (): void {
    Route::get('/', function () {
        return view('home');
    })->name('home');
});

Route::get('/site-admin/system/health', SystemHealthController::class)
    ->middleware(['auth', 'can:core.settings.view'])
    ->name('site-admin.core.health.show');
