<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Core\Authorization\AccessScope;

interface AcademicNomenclatureProvider
{
    public function singular(string $key, AccessScope $scope, string $locale = 'en'): string;

    public function plural(string $key, AccessScope $scope, string $locale = 'en'): string;
}
