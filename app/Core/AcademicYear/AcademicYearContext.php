<?php

declare(strict_types=1);

namespace App\Core\AcademicYear;

use App\Core\Authorization\AccessScope;
use App\Domains\Academics\Models\AcademicYear;
use LogicException;

final class AcademicYearContext
{
    private ?AcademicYear $year = null;

    private ?AccessScope $scope = null;

    public function activate(AcademicYear $year, AccessScope $scope): void
    {
        if (! $year->containsScope($scope)) {
            throw new LogicException('Academic year does not contain the selected scope.');
        }
        $this->year = $year;
        $this->scope = $scope;
    }

    public function clear(): void
    {
        $this->year = null;
        $this->scope = null;
    }

    public function hasYear(): bool
    {
        return $this->year !== null;
    }

    public function year(): ?AcademicYear
    {
        return $this->year;
    }

    public function scope(): ?AccessScope
    {
        return $this->scope;
    }

    public function requireYear(): AcademicYear
    {
        return $this->year ?? throw new LogicException('No academic year is active.');
    }
}
