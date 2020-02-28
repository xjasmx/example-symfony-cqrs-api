<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\ReadModel;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\UserId;

interface UserQueryRepositoryInterface extends CheckUserByEmailInterface, CheckUserByIdInterface
{
    /**
     * @param UserId $userId
     * @return UserProfileView
     */
    public function getCredentialsById(UserId $userId): UserProfileView;

    /**
     * @param UserId $userId
     * @return UserView
     */
    public function userOfId(UserId $userId): UserView;

    /**
     * @param Email $email
     * @return UserView
     */
    public function userOfEmail(Email $email): UserView;
}
