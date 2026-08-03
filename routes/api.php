<?php

use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenant::class)->group(function (): void {
    Route::get('/health', function () {
        DB::select('select 1');

        return response()->json([
            'status' => 'ok',
            'service' => config('app.name'),
            'environment' => app()->environment(),
            'database' => 'connected',
            'timestamp' => now()->toISOString(),
        ]);
    })->name('api.health');

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
