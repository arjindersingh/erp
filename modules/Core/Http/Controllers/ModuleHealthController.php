<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ModuleHealthController
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'modules' => config('modules.enabled', []),
        ]);
    }
}
