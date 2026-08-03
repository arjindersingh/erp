<?php

use App\Core\System\SystemHealthService;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenant::class)->group(function (): void {
    Route::get('/health', function (SystemHealthService $health) {
        $status = $health->publicStatus();

        return response()->json($status, $status['status'] === 'ok' ? 200 : 503);
    })->name('api.health');

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
