<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Models;

use App\Core\Attribution\HasActorAttribution;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class AdmissionDomainModel extends Model
{
    use BelongsToTenant, HasActorAttribution, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];
}
