<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\AcademicYear\AcademicYearLockService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAcademicYearWritable
{
    public function __construct(private AcademicYearContext $context, private AcademicYearLockService $locks) {}

    public function handle(Request $request, Closure $next, ?string $moduleKey = null, ?string $resourceType = null): Response
    {
        $year = $this->context->requireYear();
        $scope = $this->context->scope();
        abort_unless($scope && $this->locks->isWritable($year, $scope, $moduleKey, $resourceType), 423, 'This academic year is read-only for the requested area.');

        return $next($request);
    }
}
