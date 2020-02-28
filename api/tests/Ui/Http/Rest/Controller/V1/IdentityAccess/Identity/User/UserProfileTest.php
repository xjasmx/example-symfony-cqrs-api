<?php

declare(strict_types=1);

namespace App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Identity\User;

use App\Tests\DbWebTestCase;
use App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Access\OAuth\OAuthFixture;
use Symfony\Component\HttpFoundation\Response;

class UserProfileTest extends DbWebTestCase
{
    private const URI = '/api/v1/profile';

    /**
     * @test
     * @group e2e
     */
    public function getMethodShouldNotSupported(): void
    {
        $this->client->request('GET', self::URI);

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     * @group e2e
     */
    public function authorizedUserShouldHaveAccessToProfileData(): void
    {
        $this->client->setServerParameters(OAuthFixture::userCredentials());
        $this->client->request('GET', self::URI);

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertJson($this->client->getResponse()->getContent());
    }
}
