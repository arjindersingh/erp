<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Arr;

final class ReferenceMappingService
{
    /** @param array<string, mixed> $mapping */
    public function map(string $groupCode, string $code, array $mapping): ?ReferenceValue
    {
        $value = ReferenceValue::query()->whereHas('group', fn ($query) => $query->where('code', $groupCode))
            ->where('code', $code)
            ->first();

        if ($value === null) {
            return null;
        }

        $value->forceFill([
            'metadata_json' => array_merge(Arr::wrap($value->metadata_json), ['mappings' => $mapping]),
        ]);
        $value->save();

        return $value;
    }
}
