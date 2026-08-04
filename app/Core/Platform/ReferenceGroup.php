<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceGroup extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $table = 'reference_groups';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'allows_tenant_values' => 'boolean',
            'allows_institute_values' => 'boolean',
            'allows_translations' => 'boolean',
            'is_hierarchical' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(ReferenceValue::class, 'reference_group_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
