<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Core\Authorization\AccessScope;
use App\Domains\Academics\Contracts\AcademicNomenclatureProvider;

final class ContractAcademicNomenclatureProvider implements AcademicNomenclatureProvider
{
    public function __construct(private AcademicNomenclatureService $labels) {}

    public function singular(string $key, AccessScope $scope, string $locale = 'en'): string
    {
        return $this->labels->label($key, false, $scope->company_id, $scope->campus_id, $scope->institute_id, $locale);
    }

    public function plural(string $key, AccessScope $scope, string $locale = 'en'): string
    {
        return $this->labels->label($key, true, $scope->company_id, $scope->campus_id, $scope->institute_id, $locale);
    }
}
