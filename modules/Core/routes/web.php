<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\ModuleHealthController;

Route::get('/health/modules', ModuleHealthController::class)
    ->name('modules.health');
