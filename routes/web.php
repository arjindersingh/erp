<?php

use App\Http\Controllers\AccessDiagnosticsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ContextSelectionController;
use App\Http\Controllers\PortalShellController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemHealthController;
use App\Http\Livewire\Public\HomePage;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenant::class)->group(function (): void {
    Route::get('/', HomePage::class)->name('home');
    Route::get('/portal-shell', PortalShellController::class)->name('portal-shell');
});

Route::get('/site-admin/system/health', SystemHealthController::class)
    ->middleware(['auth', 'can:core.settings.view'])
    ->name('site-admin.core.health.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/context/select', [ContextSelectionController::class, 'create'])->name('context.select');
    Route::post('/context/select', [ContextSelectionController::class, 'store'])->name('context.store');
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::get('/admin', AdminDashboardController::class)->middleware(['active-context'])->name('admin.dashboard');
    Route::get('/admin/access-diagnostics', AccessDiagnosticsController::class)
        ->middleware(['active-context', 'effective-permission:access.diagnostics.use'])->name('admin.access-diagnostics');
});
