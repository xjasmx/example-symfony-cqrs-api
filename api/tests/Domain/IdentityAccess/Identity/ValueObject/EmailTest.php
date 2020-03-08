<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Exception\InvalidEmailException;
use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function invalidEmailShouldThrowAnException(): void
    {
        $this->expectException(InvalidEmailException::class);
        Email::fromString('asdf');
    }

    /**
     * @test
     * @group unit
     */
    public function validEmailShouldBeAbleToConvertToString(): void
    {
        $emailString = 'test@test.com';

        $email = Email::fromString($emailString);

        self::assertSame($emailString, (string)$email);
    }

    /**
     * @test
     * @group unit
     */
    public function equalityShouldBeSuccessful(): void
    {
        $emailOne = Email::fromString('test@test.com');
        $emailTwo = Email::fromString('test@test.com');

        self::assertTrue($emailOne->equals($emailTwo));
    }
}
