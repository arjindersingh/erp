<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Throwable;

final class UserSafeErrorMessageResolver
{
    public function resolve(Throwable $exception): string
    {
        return match (true) {
            $exception instanceof \Illuminate\Validation\ValidationException => 'The provided data is invalid.',
            $exception instanceof \Illuminate\Auth\AuthenticationException => 'Authentication is required to continue.',
            $exception instanceof \Illuminate\Authorization\AuthorizationException => 'You are not allowed to perform this action.',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 'The requested resource could not be found.',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException => 'Access denied.',
            default => 'An unexpected error occurred. Please try again later.',
        };
    }
}
