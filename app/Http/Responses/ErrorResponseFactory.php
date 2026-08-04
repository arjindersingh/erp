<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Shared\Exceptions\ApplicationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

final class ErrorResponseFactory
{
    /** @param array<string, mixed> $details */
    public function make(string $code, string $message, int $status = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, array $details = []): JsonResponse
    {
        return $this->fromArray([
            'code' => $code,
            'message' => $message,
            'status' => $status,
            'request_id' => request()->attributes->get('request_id'),
            'correlation_id' => request()->attributes->get('correlation_id'),
            'details' => $details,
        ], $status);
    }

    public function fromException(Throwable $exception, ?Request $request = null): JsonResponse
    {
        $request ??= request();
        $status = $exception instanceof ApplicationException ? $exception->getStatusCode() : SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
        $code = $exception instanceof ApplicationException ? $exception->getErrorCode() : 'system_error';
        $message = $this->safeMessage($exception);
        $details = $exception instanceof ApplicationException ? $exception->getDetails() : [];

        return $this->fromArray([
            'code' => $code,
            'message' => $message,
            'status' => $status,
            'request_id' => $request->attributes->get('request_id'),
            'correlation_id' => $request->attributes->get('correlation_id'),
            'details' => $details,
        ], $status);
    }

    private function fromArray(array $payload, int $status): JsonResponse
    {
        return response()->json(['error' => $payload], $status);
    }

    private function safeMessage(Throwable $exception): string
    {
        if ($exception instanceof ApplicationException) {
            return $exception->getMessage() !== '' ? $exception->getMessage() : 'An unexpected error occurred.';
        }

        return 'An unexpected error occurred.';
    }
}
