<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ErrorResponseFactory
{
    /** @param array<string, mixed> $details */
    public function make(string $code, string $message, int $status = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, array $details = []): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
                'status' => $status,
                'request_id' => request()->attributes->get('request_id'),
                'correlation_id' => request()->attributes->get('correlation_id'),
                'details' => $details,
            ],
        ], $status);
    }
}
