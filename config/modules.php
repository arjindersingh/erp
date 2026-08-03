<?php

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
    ],

    'modules' => [
        'core' => [
            'name' => 'Core',
            'provider' => Modules\Core\Providers\CoreServiceProvider::class,
        ],
    ],
];
