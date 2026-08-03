<?php

declare(strict_types=1);

namespace App\Core\Audit;

final readonly class AuditActor
{
    public function __construct(
        public AuditActorType $type,
        public ?int $id = null,
        public ?int $userId = null,
    ) {}
}
