<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use App\Domain\IdentityAccess\Identity\ValueObject\ConfirmationToken;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfirmationTokenTest extends TestCase
{
    /**
     * @test
     * @group unit
     * @throws DateTimeException
     */
    public function confirmationTokenShouldBeSuccessfullyCreated(): void
    {
        $token = new ConfirmationToken($hash = '123456', $date = DateTime::now());

        self::assertEquals($hash, $token->token());
        self::assertEquals($date, $token->expiresDate());
    }

    /**
     * @test
     * @group unit
     * @throws DateTimeException
     */
    public function minimumTokenLengthShouldBeAtLeast6Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ConfirmationToken($hash = '12345', DateTime::now());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function thereShouldBeAnExceptionDuringValidationWhenTokensAreUnequal(): void
    {
        $this->expectException(InvalidConfirmationTokenException::class);

        $token = new ConfirmationToken('token1234', DateTime::now());

        $this->expectExceptionMessage('Confirmation token is invalid.');
        $token->validate('token1235', DateTime::fromString('-1 secs'));
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function thereShouldBeAnExceptionDuringTheCheckIfTheTokenHasExpired(): void
    {
        $this->expectException(InvalidConfirmationTokenException::class);

        $token = new ConfirmationToken($value = 'token1234', DateTime::now());

        $this->expectExceptionMessage('Confirmation token is invalid.');
        $token->validate($value, DateTime::fromString('+1 secs'));
    }
}
