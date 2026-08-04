<?php

use App\Core\Tenancy\Exceptions\TenantCouldNotBeResolved;
use App\Http\Middleware\EnsureAcademicYearContext;
use App\Http\Middleware\EnsureAcademicYearWritable;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'academic-year' => EnsureAcademicYearContext::class,
            'academic-year.writable' => EnsureAcademicYearWritable::class,
        ]);
        $middleware->redirectGuestsTo(fn (): string => route('home'));

        $middleware->appendToGroup('web', [
            ResolvePublicTenant::class,
            EnsureTenantIsActive::class,
        ]);

        $middleware->appendToGroup('api', [
            ResolvePublicTenant::class,
            EnsureTenantIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (TenantCouldNotBeResolved $exception, Request $request) {
            return response()->json([
                'message' => 'Tenant could not be resolved for this domain.',
            ], 404);
        });
    })->create();
