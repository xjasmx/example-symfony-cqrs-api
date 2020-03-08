<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function parametersForTheNameShouldBeValid(): void
    {
        $name = Name::fromString($first = 'TestFirst', $last = 'TestLast');

        self::assertEquals($first, $name->first());
        self::assertEquals($last, $name->last());
        self::assertEquals($first . ' ' . $last, $name->full());
    }

    /**
     * @test
     * @group unit
     */
    public function lastNameShouldBeAnOptionalParameter(): void
    {
        $name = Name::fromString($first = 'TestFirst', null);

        self::assertNotNull($name->first());
        self::assertNull($name->last());

        self::assertEquals($first, $name->first());
        self::assertEquals($first, $name->full());
    }

    /**
     * @test
     * @group unit
     */
    public function ifAnEmptyFirstNameIsSpecifiedAnExceptionShouldBeThrown(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        Name::fromString($first = '', null);

        $this->expectExceptionMessage('First name not found.');
    }
}
