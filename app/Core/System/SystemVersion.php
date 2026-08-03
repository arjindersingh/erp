<?php

declare(strict_types=1);

namespace App\Core\System;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\SystemVersionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(SystemVersionFactory::class)]
#[Fillable(['uuid', 'version', 'build', 'commit_hash', 'deployed_at', 'metadata'])]
final class SystemVersion extends Model
{
    /** @use HasFactory<SystemVersionFactory> */
    use HasFactory, HasPublicUuid;

    protected function casts(): array
    {
        return ['deployed_at' => 'immutable_datetime', 'metadata' => 'array'];
    }
}
