<?php

declare(strict_types=1);

namespace App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Access\OAuth;

use App\Domain\IdentityAccess\Identity\Service\UserUniquenessCheckerByEmailInterface;
use App\Domain\IdentityAccess\Identity\User;
use App\Domain\IdentityAccess\Identity\ValueObject\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use App\Domain\IdentityAccess\Identity\ValueObject\Name;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\ValueObject\DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Grants;
use Exception;

class OAuthFixture extends Fixture
{
    public const CLIENT_ID = '67f073edbe5fd67b9fa41570e9fd1d29';
    public const CLIENT_SECRET = 'd912e059a87d1a3cdad6082e4c6eec966bb78b8c183b4ea230c80d3a0c87e598524b382291919a7824f7cea2308550003d6134d18d58b2be94874e64627b59f1';
    public const USER_ID = '1adfaa1b-6f20-4f41-868f-74457b6c467e';
    public const USER_EMAIL = 'oauth-password-user@app.test';
    public const USER_FIRST_NAME = 'OAuth';
    public const USER_LAST_NAME = 'User';

    private UserUniquenessCheckerByEmailInterface $checkerByEmail;

    public function __construct(UserUniquenessCheckerByEmailInterface $checkerByEmail)
    {
        $this->checkerByEmail = $checkerByEmail;
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->createUser();
        $manager->persist($user);

        $client = new Client(self::CLIENT_ID, self::CLIENT_SECRET);
        $client->setActive(true);
        $client->setGrants(new Grant(OAuth2Grants::PASSWORD), new Grant(OAuth2Grants::REFRESH_TOKEN));

        $manager->persist($client);

        $manager->flush();
    }

    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => self::USER_ID,
            'PHP_AUTH_PW' => 'password',
        ];
    }

    /**
     * @return User
     * @throws Exception
     */
    private function createUser(): User
    {
        $user = User::registerByEmail(
            UserId::fromString(self::USER_ID),
            Name::fromString(self::USER_FIRST_NAME, self::USER_LAST_NAME),
            Email::fromString(self::USER_EMAIL),
            '$2y$12$qwnND33o8DGWvFoepotSju7eTAQ6gzLD/zy6W8NCVtiHPbkybz.w6', // 'password'
            new ConfirmationToken('confirmHash', DateTime::fromString('+1 day')),
            $this->checkerByEmail
        );

        $user->confirmRegistrationByEmail('confirmHash', DateTime::now());

        return $user;
    }
}
