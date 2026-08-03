<?php

declare(strict_types=1);

namespace App\Core\Audit\Concerns;

use Illuminate\Database\Eloquent\Model;
use LogicException;

trait AppendOnly
{
    public static function bootAppendOnly(): void
    {
        static::updating(fn (Model $model) => throw new LogicException('Audit records are append-only.'));
        static::deleting(fn (Model $model) => throw new LogicException('Audit records are append-only.'));
    }
}
