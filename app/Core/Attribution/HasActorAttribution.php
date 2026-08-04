<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait HasActorAttribution
{
    public static function bootHasActorAttribution(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('created_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                self::applyAttributeValues($model, 'created', $context);
            }
        });

        static::updating(function (Model $model): void {
            if ($model->getAttribute('updated_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                self::applyAttributeValues($model, 'updated', $context);
            }
        });

        static::deleting(function (Model $model): void {
            if ($model->getAttribute('deleted_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                self::applyAttributeValues($model, 'deleted', $context);
            }
        });
    }

    private static function applyAttributeValues(Model $model, string $prefix, ActorContext $context): void
    {
        $columns = Schema::getColumnListing($model->getTable());

        $attributeMap = [
            $prefix.'_by_user_id' => $context->userId,
            $prefix.'_actor_type' => $context->actorType->value,
            $prefix.'_authentication_state' => $context->authenticationState->value,
            $prefix.'_via' => $context->operationSource->value,
            $prefix.'_request_id' => $context->requestId,
            $prefix.'_correlation_id' => $context->correlationId,
        ];

        foreach ($attributeMap as $attribute => $value) {
            if (in_array($attribute, $columns, true)) {
                $model->setAttribute($attribute, $value);
            }
        }
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }
}
