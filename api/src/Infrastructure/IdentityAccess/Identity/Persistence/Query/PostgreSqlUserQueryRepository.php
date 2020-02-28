<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\Query;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\ReadModel\UserProfileView;
use App\Domain\IdentityAccess\Identity\ReadModel\UserQueryRepositoryInterface;
use App\Domain\IdentityAccess\Identity\ReadModel\UserView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\FetchMode;

final class PostgreSqlUserQueryRepository implements UserQueryRepositoryInterface
{
    private Connection $connection;
    private UserViewMapper $viewMapper;

    /**
     * PostgreSqlUserQueryRepository constructor.
     * @param Connection $connection
     * @param UserViewMapper $viewMapper
     */
    public function __construct(Connection $connection, UserViewMapper $viewMapper)
    {
        $this->connection = $connection;
        $this->viewMapper = $viewMapper;
    }

    /**
     * @param UserId $id
     * @return UserProfileView
     * @throws UserNotFoundException
     */
    public function getCredentialsById(UserId $id): UserProfileView
    {
        return $this->getCredentialBy('id', $id);
    }

    /**
     * @param UserId $id
     * @return UserView
     * @throws UserNotFoundException
     */
    public function userOfId(UserId $id): UserView
    {
        return $this->userOf('id', $id);
    }

    /**
     * @param Email $email
     * @return UserView
     * @throws UserNotFoundException
     */
    public function userOfEmail(Email $email): UserView
    {
        return $this->userOf('email', $email);
    }

    /**
     * @param Email $email
     * @return UserId|null
     */
    public function existsEmail(Email $email): ?UserId
    {
        return $this->existsOf('email', $email);
    }

    /**
     * @param UserId $id
     * @return UserId|null
     */
    public function existsId(UserId $id): ?UserId
    {
        return $this->existsOf('id', $id);
    }

    /**
     * @param string $criterion
     * @param object $search
     * @return UserProfileView
     * @throws UserNotFoundException
     */
    private function getCredentialBy(string $criterion, object $search): UserProfileView
    {
        $query = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'name_first AS first_name',
                'name_last AS last_name',
                'status_type AS status',
                'date as date_create'
            )
            ->from('users')
            ->where("$criterion = :$criterion")
            ->setParameter(":$criterion", (string)$search);

        /** @var Statement $stmt */
        $stmt = $query->execute();

        $userData = $stmt->fetch(FetchMode::ASSOCIATIVE);

        if (!$userData) {
            throw new UserNotFoundException();
        }

        return $this->viewMapper->mapToShort($userData);
    }

    /**
     * @param string $criterion
     * @param object $search
     * @return UserId|null
     */
    private function existsOf(string $criterion, object $search): ?UserId
    {
        $query = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('users')
            ->where("$criterion = :$criterion")
            ->setParameter(":$criterion", (string)$search);

        /** @var Statement $stmt */
        $stmt = $query->execute();

        $userId = $stmt->fetch(FetchMode::ASSOCIATIVE);

        if (!$userId) {
            return null;
        }

        return UserId::fromString($userId['id']);
    }

    /**
     * @param string $criterion
     * @param object $search
     * @return UserView
     * @throws UserNotFoundException
     */
    private function userOf(string $criterion, object $search): UserView
    {
        $query = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'name_first AS first_name',
                'name_last AS last_name',
                'password',
                'status_type as status',
                'date as date_create'
            )
            ->from('users')
            ->where("$criterion = :$criterion")
            ->setParameter(":$criterion", (string)$search);

        /** @var Statement $stmt */
        $stmt = $query->execute();

        $userData = $stmt->fetch(FetchMode::ASSOCIATIVE);

        if (!$userData) {
            throw new UserNotFoundException();
        }

        return $this->viewMapper->mapToFull($userData);
    }
}
