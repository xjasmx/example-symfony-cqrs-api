<?php

declare(strict_types=1);

namespace App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Identity\User;

use App\Tests\DbWebTestCase;
use App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Access\OAuth\OAuthFixture;
use Symfony\Component\HttpFoundation\Response;

class UserChangePasswordTest extends DbWebTestCase
{
    private const URI = '/api/v1/profile/password';

    /**
     * @test
     * @group e2e
     */
    public function userPasswordChangeShouldBeSuccessful(): void
    {
        $this->client->setServerParameters(OAuthFixture::userCredentials());
        $this->client->request(
            'PATCH',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'id' => OAuthFixture::USER_ID,
                    'current_password' => 'password',
                    'new_password' => 'newpassword',
                ]
            )
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());
    }

    /**
     * @test
     * @group e2e
     */
    public function whenChangingThePasswordIfTheUserSpecifiesAnIncorrectCurrentPasswordThereShouldBeAnError(): void
    {
        $this->client->setServerParameters(OAuthFixture::userCredentials());
        $this->client->request(
            'PATCH',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'id' => OAuthFixture::USER_ID,
                    'current_password' => 'incorrectpassword',
                    'new_password' => 'newpassword',
                ]
            )
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());
    }
}
