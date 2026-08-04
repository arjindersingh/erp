<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\AcademicYear\AcademicYearContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAcademicYearContext
{
    public function __construct(private AcademicYearContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->context->hasYear(), 409, 'Select an academic year to continue.');

        return $next($request);
    }
}
