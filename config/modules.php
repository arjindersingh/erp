<?php

use Modules\Admissions\Providers\AdmissionsServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Enabled Modules
    |--------------------------------------------------------------------------
    |
    | Keep this list explicit so module boundaries stay visible. Each enabled
    | module must have a provider entry in the modules map below.
    |
    */
    'enabled' => [
        'core',
        'admissions',
    ],

    'modules' => [
        'core' => [
            'name' => 'Core',
            'provider' => CoreServiceProvider::class,
        ],
        'admissions' => [
            'name' => 'Admissions',
            'provider' => AdmissionsServiceProvider::class,
        ],
    ],
];
