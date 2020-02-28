<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Query\User;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\ReadModel\UserProfileView;
use App\Domain\IdentityAccess\Identity\ReadModel\UserQueryRepositoryInterface;

final class GetUserCredentialFindByIdHandler
{
    private UserQueryRepositoryInterface $repository;

    /**
     * GetUserCredentialFindByIdHandler constructor.
     * @param UserQueryRepositoryInterface $repository
     */
    public function __construct(UserQueryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetUserCredentialFindByIdQuery $query
     * @return UserProfileView
     */
    public function handle(GetUserCredentialFindByIdQuery $query): UserProfileView
    {
        return $this->repository->getCredentialsById(UserId::fromString($query->id));
    }
}
