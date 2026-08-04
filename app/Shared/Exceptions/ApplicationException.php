<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;
use Throwable;

class ApplicationException extends RuntimeException
{
    public function __construct(
        private readonly string $errorCode,
        string $message = '',
        private readonly int $status = 500,
        private readonly array $details = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    /** @return array<string, mixed> */
    public function getDetails(): array
    {
        return $this->details;
    }
}
