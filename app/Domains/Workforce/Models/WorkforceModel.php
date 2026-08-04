<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class WorkforceModel extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];
}
