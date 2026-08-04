<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class StudentDomainModel extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];
}
