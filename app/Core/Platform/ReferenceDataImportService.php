<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Arr;

final class ReferenceDataImportService
{
    /** @param array<int, array<string, mixed>> $rows */
    public function import(string $groupCode, array $rows): int
    {
        $group = ReferenceGroup::query()->where('code', $groupCode)->firstOrFail();

        foreach ($rows as $row) {
            ReferenceValue::query()->updateOrCreate(
                [
                    'reference_group_id' => $group->id,
                    'code' => (string) Arr::get($row, 'code'),
                ],
                [
                    'tenant_id' => $group->tenant_id,
                    'label' => (string) Arr::get($row, 'label'),
                    'short_label' => Arr::get($row, 'short_label'),
                    'description' => Arr::get($row, 'description'),
                    'status' => 'active',
                    'is_system' => $group->is_system,
                    'is_default' => false,
                    'metadata_json' => Arr::get($row, 'metadata_json', []),
                ],
            );
        }

        return count($rows);
    }
}
