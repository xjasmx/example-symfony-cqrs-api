<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Specification;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\ReadModel\CheckUserByEmailInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use App\Domain\Shared\Specification\Specification;

final class UserUniqueEmailSpecification implements Specification, UserUniqueEmailSpecificationInterface
{
    private CheckUserByEmailInterface $checkUserByEmail;

    /**
     * UserUniqueEmailSpecification constructor.
     * @param CheckUserByEmailInterface $checkUserByEmail
     */
    public function __construct(CheckUserByEmailInterface $checkUserByEmail)
    {
        $this->checkUserByEmail = $checkUserByEmail;
    }

    /**
     * @param Email $email
     * @return bool
     */
    public function isUnique(Email $email): bool
    {
        return $this->isSatisfiedBy($email);
    }

    /**
     * @param Email $value
     * @return bool
     */
    public function isSatisfiedBy($value): bool
    {
        if ($this->checkUserByEmail->existsEmail($value)) {
            return false;
        }

        return true;
    }
}
