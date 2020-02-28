<?php

declare(strict_types=1);

namespace App\Tests\Ui\Http\Rest\Controller\V1\IdentityAccess\Access\OAuth;

use App\Tests\DbWebTestCase;

class OAuthControllerTest extends DbWebTestCase
{
    private const URI = '/token';

    /**
     * @test
     * @group e2e
     */
    public function userShouldBeAuthorized(): void
    {
        $this->client->request(
            'POST',
            self::URI,
            [
                'grant_type' => 'password',
                'username' => 'oauth-password-user@app.test',
                'password' => 'password',
                'client_id' => OAuthFixture::CLIENT_ID,
                'client_secret' => OAuthFixture::CLIENT_SECRET,
                'access_type' => 'offline',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json; charset=UTF-8', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertSame('Bearer', $jsonResponse['token_type']);
        $this->assertSame(3600, $jsonResponse['expires_in']);
        $this->assertNotEmpty($jsonResponse['access_token']);
        $this->assertNotEmpty($jsonResponse['refresh_token']);
    }

    /**
     * @test
     * @group e2e
     */
    public function thereShouldBeAnErrorWhenAnUnregisteredUserTriesToGetAuthorizationData(): void
    {
        $this->client->request(
            'POST',
            self::URI,
            [
                'grant_type' => 'password',
                'username' => 'oauth-password-user-failed@app.test',
                'password' => 'password',
                'client_id' => OAuthFixture::CLIENT_ID,
                'client_secret' => OAuthFixture::CLIENT_SECRET,
                'access_type' => 'offline',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/vnd.api+json', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent(), true);

        self::assertArrayHasKey('errors', $jsonResponse);
    }

    /**
     * @test
     * @group e2e
     */
    public function accessTokenShouldBeUpdated(): void
    {
        $this->client->request(
            'POST',
            self::URI,
            [
                'grant_type' => 'password',
                'username' => 'oauth-password-user@app.test',
                'password' => 'password',
                'client_id' => OAuthFixture::CLIENT_ID,
                'client_secret' => OAuthFixture::CLIENT_SECRET,
                'access_type' => 'offline',
            ]
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $refreshToken = $data['refresh_token'];

        $this->client->request(
            'POST',
            self::URI,
            [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => OAuthFixture::CLIENT_ID,
                'client_secret' => OAuthFixture::CLIENT_SECRET,
                'access_type' => 'offline',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json; charset=UTF-8', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertSame('Bearer', $jsonResponse['token_type']);
        $this->assertSame(3600, $jsonResponse['expires_in']);
        $this->assertNotEmpty($jsonResponse['access_token']);
        $this->assertNotEmpty($jsonResponse['refresh_token']);
    }

    public function testFailedTokenRequest(): void
    {
        $this->client->request('POST', self::URI);

        $response = $this->client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertSame('unsupported_grant_type', $jsonResponse['error']);
        $this->assertSame(
            'The authorization grant type is not supported by the authorization server.',
            $jsonResponse['message']
        );
        $this->assertSame('Check that all required parameters have been provided', $jsonResponse['hint']);
    }

    public function testFailedClientCredentialsTokenRequest(): void
    {
        $this->client->request(
            'POST',
            self::URI,
            [
                'client_id' => 'foo',
                'client_secret' => 'wrong',
                'grant_type' => 'client_credentials'
            ]
        );

        $response = $this->client->getResponse();

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertSame('invalid_client', $jsonResponse['error']);
        $this->assertSame('Client authentication failed', $jsonResponse['message']);
    }
}
