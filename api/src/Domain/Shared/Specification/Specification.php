<?php

declare(strict_types=1);

namespace App\Domain\Shared\Specification;

interface Specification
{
    /**
     * @param object $value
     * @return bool
     */
    public function isSatisfiedBy(object $value): bool;
}
