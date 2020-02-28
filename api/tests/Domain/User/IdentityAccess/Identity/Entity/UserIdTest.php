<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function validEmailShouldBeAbleToConvertToString(): void
    {
        $idString = '1';

        $id = UserId::fromString($idString);

        self::assertSame($idString, (string)$id);
    }

    /**
     * @test
     * @group unit
     */
    public function equalityShouldBeSuccessful(): void
    {
        $idOne = UserId::fromString('1');
        $idTwo = UserId::fromString('1');

        self::assertTrue($idOne->equals($idTwo));
    }
}
