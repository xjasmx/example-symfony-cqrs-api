<?php

declare(strict_types=1);

namespace App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Identity\User;

use App\Tests\DbWebTestCase;
use App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Access\OAuth\OAuthFixture;
use Symfony\Component\HttpFoundation\Response;

class UserChangeNameTest extends DbWebTestCase
{
    private const URI = '/api/v1/profile/name';

    /**
     * @test
     * @group e2e
     */
    public function userNameChangeShouldBeSuccessful(): void
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
                    'first_name' => 'newFirst',
                    'last_name' => 'newLast',
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
    public function keyLastNameShouldBePassedOtherwiseErrorReturned(): void
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
                    'first_name' => 'newFirst',
                ]
            )
        );

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());
    }

    /**
     * @test
     * @group e2e
     */
    public function whenChangingNameIfTheUserIndicatesAnAlreadyLinkedEmailThereShouldBeAnError(): void
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
                    'first_name' => '',
                    'last_name' => 'newLast',
                ]
            )
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());
    }
}
