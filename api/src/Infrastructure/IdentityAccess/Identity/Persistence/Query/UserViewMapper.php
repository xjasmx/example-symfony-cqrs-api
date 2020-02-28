<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\Query;

use App\Domain\IdentityAccess\Identity\ReadModel\UserProfileView;
use App\Domain\IdentityAccess\Identity\ReadModel\UserView;

class UserViewMapper
{
    /**
     * @param array $data
     * @return UserProfileView
     */
    public function mapToShort(array $data): UserProfileView
    {
        return new UserProfileView(
            $data['id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['status'],
            $data['date_create']
        );
    }

    /**
     * @param array $data
     * @return UserView
     */
    public function mapToFull(array $data): UserView
    {
        return new UserView(
            $data['id'],
            $data['first_name'],
            $data['status'],
            $data['password'],
            $data['date_create'],
            $data['last_name'],
            $data['email']
        );
    }
}
