<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class ReferenceValueValidator
{
    /** @param array<string, mixed> $payload */
    public function validate(string $groupCode, array $payload): void
    {
        $group = ReferenceGroup::query()->where('code', $groupCode)->firstOrFail();
        $rules = [
            'code' => ['required', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:255'],
        ];

        $validator = validator($payload, $rules);
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        if ($group->is_system && Arr::get($payload, 'is_system') === false) {
            throw ValidationException::withMessages(['is_system' => 'System groups can only contain system values.']);
        }
    }
}
