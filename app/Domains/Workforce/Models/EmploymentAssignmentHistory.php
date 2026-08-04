<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Workforce\Enums\EmploymentAssignmentAction;
use App\Shared\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmploymentAssignmentHistory extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(EmploymentAssignment::class, 'employment_assignment_id');
    }

    protected function casts(): array
    {
        return ['action' => EmploymentAssignmentAction::class, 'old_values_json' => 'array', 'new_values_json' => 'array', 'effective_on' => 'date'];
    }
}
