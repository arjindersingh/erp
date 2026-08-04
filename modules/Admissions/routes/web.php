<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Admissions\Http\Controllers\PublicApplicationController;
use Modules\Admissions\Http\Controllers\PublicCampaignController;

Route::middleware('web')->prefix('admissions')->name('admissions.public.')->group(function (): void {
    Route::get('/', [PublicCampaignController::class, 'index'])->name('index');
    Route::get('/campaigns', [PublicCampaignController::class, 'index'])->name('campaigns');
    Route::get('/apply/{campaign}', [PublicCampaignController::class, 'show'])->name('apply');
    Route::post('/apply/{campaign}', [PublicApplicationController::class, 'store'])
        ->middleware('throttle:10,1')->name('applications.store');
});
