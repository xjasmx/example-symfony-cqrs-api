<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfirmationTokenTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function confirmationTokenShouldBeSuccessfullyCreated(): void
    {
        $token = new ConfirmationToken($hash = '123456', $date = new  DateTimeImmutable());

        self::assertEquals($hash, $token->token());
        self::assertEquals($date, $token->expiresDate());
    }

    /**
     * @test
     * @group unit
     */
    public function minimumTokenLengthShouldBeAtLeast6Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ConfirmationToken($hash = '12345', new DateTimeImmutable());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function thereShouldBeAnExceptionDuringValidationWhenTokensAreUnequal(): void
    {
        $this->expectException(InvalidConfirmationTokenException::class);

        $token = new ConfirmationToken('token1234', $expires = new DateTimeImmutable());

        $this->expectExceptionMessage('Confirmation token is invalid.');
        $token->validate('token1235', $expires->modify('-1 secs'));
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function thereShouldBeAnExceptionDuringTheCheckIfTheTokenHasExpired(): void
    {
        $this->expectException(InvalidConfirmationTokenException::class);

        $token = new ConfirmationToken($value = 'token1234', $expires = new DateTimeImmutable());

        $this->expectExceptionMessage('Confirmation token is invalid.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}
