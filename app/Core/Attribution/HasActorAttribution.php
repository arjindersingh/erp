<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasActorAttribution
{
    public static function bootHasActorAttribution(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('created_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                $model->setAttribute('created_by_user_id', $context->userId);
                $model->setAttribute('created_actor_type', $context->actorType->value);
                $model->setAttribute('created_authentication_state', $context->authenticationState->value);
                $model->setAttribute('created_via', $context->operationSource->value);
                $model->setAttribute('created_request_id', $context->requestId);
                $model->setAttribute('created_correlation_id', $context->correlationId);
            }
        });

        static::updating(function (Model $model): void {
            if ($model->getAttribute('updated_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                $model->setAttribute('updated_by_user_id', $context->userId);
                $model->setAttribute('updated_actor_type', $context->actorType->value);
                $model->setAttribute('updated_authentication_state', $context->authenticationState->value);
                $model->setAttribute('updated_via', $context->operationSource->value);
                $model->setAttribute('updated_request_id', $context->requestId);
                $model->setAttribute('updated_correlation_id', $context->correlationId);
            }
        });

        static::deleting(function (Model $model): void {
            if ($model->getAttribute('deleted_by_user_id') === null && app()->bound(ActorContext::class)) {
                $context = app(ActorContext::class);
                $model->setAttribute('deleted_by_user_id', $context->userId);
                $model->setAttribute('deleted_actor_type', $context->actorType->value);
                $model->setAttribute('deleted_authentication_state', $context->authenticationState->value);
                $model->setAttribute('deleted_via', $context->operationSource->value);
                $model->setAttribute('deleted_request_id', $context->requestId);
                $model->setAttribute('deleted_correlation_id', $context->correlationId);
            }
        });
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
