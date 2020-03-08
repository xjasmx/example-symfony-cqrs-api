<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Query\User;

use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;

final class GetUserDetailsHandler
{
    private Connection $connection;

    /**
     * UserUniquenessCheckerById constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param GetUserCredentialFindByIdQuery $query
     * @return UserDetailsView
     * @throws DBALException
     * @throws UserNotFoundException
     */
    public function handle(GetUserCredentialFindByIdQuery $query): UserDetailsView
    {
        $stmt = $this->connection->prepare(
            'SELECT id, name_first as first_name, name_last as last_name, email, password, status_type as status, created_on 
                        FROM users 
                        WHERE id=:id'
        );
        $stmt->execute([':id' => $query->id]);

        $data = $stmt->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new UserNotFoundException();
        }

        return new UserDetailsView(
            $data['id'],
            $data['first_name'],
            $data['created_on'],
            $data['password'],
            $data['status'],
            $data['last_name'],
            $data['email'],
        );
    }
}
