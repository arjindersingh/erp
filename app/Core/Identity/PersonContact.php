<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Shared\Support\BelongsToTenant;
use Database\Factories\PersonContactFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PersonContactFactory::class)]
#[Fillable([
    'tenant_id',
    'person_id',
    'type',
    'label',
    'value',
    'normalized_value',
    'is_primary',
    'is_verified',
    'verified_at',
    'metadata',
])]
class PersonContact extends Model
{
    /** @use HasFactory<PersonContactFactory> */
    use BelongsToTenant, HasFactory;

    protected $attributes = [
        'is_primary' => false,
        'is_verified' => false,
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
