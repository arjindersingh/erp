<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Http\Request;

final class ActorContextResolverFactory
{
    public function make(?Request $request = null): ActorContextResolver
    {
        return new ActorContextResolver($request);
    }
}
