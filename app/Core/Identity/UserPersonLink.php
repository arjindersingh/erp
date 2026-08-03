<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Models\User;
use App\Shared\Support\BelongsToTenant;
use Database\Factories\UserPersonLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(UserPersonLinkFactory::class)]
#[Fillable(['tenant_id', 'user_id', 'person_id', 'is_primary', 'status'])]
class UserPersonLink extends Model
{
    /** @use HasFactory<UserPersonLinkFactory> */
    use BelongsToTenant, HasFactory;

    protected $attributes = [
        'is_primary' => false,
        'status' => IdentityStatus::Active->value,
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'status' => IdentityStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
