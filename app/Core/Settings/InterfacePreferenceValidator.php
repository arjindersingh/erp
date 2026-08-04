<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Authentication\ActiveContext;
use App\Models\User;

final class InterfacePreferenceValidator
{
    public function validate(User $user, ActiveContext $context, UserInterfacePreference $preferences): ValidationResult
    {
        if ($preferences->user_id !== $user->id) {
            return ValidationResult::invalid(['user' => 'Preference record does not belong to the authenticated user.']);
        }

        if ($context->portal->id !== $preferences->portal_id) {
            return ValidationResult::invalid(['portal' => 'Preference record does not belong to the current portal.']);
        }

        if ($context->membership->tenant_id !== $preferences->tenant_id) {
            return ValidationResult::invalid(['tenant' => 'Preference record does not belong to the current tenant.']);
        }

        if ($preferences->font_scale !== null && ! array_key_exists($preferences->font_scale, config('interface.font_scales'))) {
            return ValidationResult::fallbackApplied('font_scale');
        }

        if ($preferences->line_height !== null && ! array_key_exists($preferences->line_height, config('interface.line_heights'))) {
            return ValidationResult::fallbackApplied('line_height');
        }

        if ($preferences->interface_density !== null && ! in_array($preferences->interface_density, array_column(config('interface.interface_densities'), null, null), true)) {
            return ValidationResult::fallbackApplied('interface_density');
        }

        return ValidationResult::valid();
    }
}
