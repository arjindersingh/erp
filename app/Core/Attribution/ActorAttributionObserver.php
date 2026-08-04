<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Database\Eloquent\Model;

final class ActorAttributionObserver
{
    public function creating(Model $model): void
    {
        $this->applyCreateAttribution($model);
    }

    public function created(Model $model): void
    {
        $this->applyCreateAttribution($model);
    }

    public function updating(Model $model): void
    {
        $this->applyUpdateAttribution($model);
    }

    public function updated(Model $model): void
    {
        $this->applyUpdateAttribution($model);
    }

    public function deleting(Model $model): void
    {
        $this->applyDeleteAttribution($model);
    }

    public function deleted(Model $model): void
    {
        $this->applyDeleteAttribution($model);
    }

    public function restoring(Model $model): void
    {
        // no-op
    }

    public function restored(Model $model): void
    {
        // no-op
    }

    private function applyCreateAttribution(Model $model): void
    {
        if (! app()->bound(ActorContext::class)) {
            return;
        }

        $context = app(ActorContext::class);
        if ($model->getAttribute('created_by_user_id') === null) {
            $model->setAttribute('created_by_user_id', $context->userId);
        }
        if ($model->getAttribute('created_actor_type') === null) {
            $model->setAttribute('created_actor_type', $context->actorType->value);
        }
        if ($model->getAttribute('created_authentication_state') === null) {
            $model->setAttribute('created_authentication_state', $context->authenticationState->value);
        }
        if ($model->getAttribute('created_via') === null) {
            $model->setAttribute('created_via', $context->operationSource->value);
        }
        if ($model->getAttribute('created_request_id') === null) {
            $model->setAttribute('created_request_id', $context->requestId);
        }
        if ($model->getAttribute('created_correlation_id') === null) {
            $model->setAttribute('created_correlation_id', $context->correlationId);
        }
    }

    private function applyUpdateAttribution(Model $model): void
    {
        if (! app()->bound(ActorContext::class)) {
            return;
        }

        $context = app(ActorContext::class);
        if ($model->getAttribute('updated_by_user_id') === null) {
            $model->setAttribute('updated_by_user_id', $context->userId);
        }
        if ($model->getAttribute('updated_actor_type') === null) {
            $model->setAttribute('updated_actor_type', $context->actorType->value);
        }
        if ($model->getAttribute('updated_authentication_state') === null) {
            $model->setAttribute('updated_authentication_state', $context->authenticationState->value);
        }
        if ($model->getAttribute('updated_via') === null) {
            $model->setAttribute('updated_via', $context->operationSource->value);
        }
        if ($model->getAttribute('updated_request_id') === null) {
            $model->setAttribute('updated_request_id', $context->requestId);
        }
        if ($model->getAttribute('updated_correlation_id') === null) {
            $model->setAttribute('updated_correlation_id', $context->correlationId);
        }
    }

    private function applyDeleteAttribution(Model $model): void
    {
        if (! app()->bound(ActorContext::class)) {
            return;
        }

        $context = app(ActorContext::class);
        if ($model->getAttribute('deleted_by_user_id') === null) {
            $model->setAttribute('deleted_by_user_id', $context->userId);
        }
        if ($model->getAttribute('deleted_actor_type') === null) {
            $model->setAttribute('deleted_actor_type', $context->actorType->value);
        }
        if ($model->getAttribute('deleted_authentication_state') === null) {
            $model->setAttribute('deleted_authentication_state', $context->authenticationState->value);
        }
        if ($model->getAttribute('deleted_via') === null) {
            $model->setAttribute('deleted_via', $context->operationSource->value);
        }
        if ($model->getAttribute('deleted_request_id') === null) {
            $model->setAttribute('deleted_request_id', $context->requestId);
        }
        if ($model->getAttribute('deleted_correlation_id') === null) {
            $model->setAttribute('deleted_correlation_id', $context->correlationId);
        }
    }
}
