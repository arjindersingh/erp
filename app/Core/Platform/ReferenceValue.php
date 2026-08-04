<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Attribution\HasActorAttribution;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceValue extends Model
{
    use BelongsToTenant, HasActorAttribution, HasPublicUuid, SoftDeletes;

    protected $table = 'reference_values';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
            'is_system' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ReferenceGroup::class, 'reference_group_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
