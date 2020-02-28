<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Specification;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\ReadModel\CheckUserByIdInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use App\Domain\Shared\Specification\Specification;

final class UserUniqueIdSpecification implements Specification, UserUniqueIdSpecificationInterface
{
    private CheckUserByIdInterface $checkUserById;

    /**
     * UserUniqueEmailSpecification constructor.
     * @param CheckUserByIdInterface $checkUserByEmail
     */
    public function __construct(CheckUserByIdInterface $checkUserByEmail)
    {
        $this->checkUserById = $checkUserByEmail;
    }

    /**
     * @param UserId $id
     * @return bool
     */
    public function isUnique(UserId $id): bool
    {
        return $this->isSatisfiedBy($id);
    }

    /**
     * @param UserId $value
     * @return bool
     */
    public function isSatisfiedBy($value): bool
    {
        if ($this->checkUserById->existsId($value)) {
            return false;
        }

        return true;
    }
}
