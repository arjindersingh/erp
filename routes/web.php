<?php

use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Support\Facades\Route;

Route::middleware(ResolvePublicTenant::class)->group(function (): void {
    Route::get('/', function () {
        return view('welcome');
    });
});
