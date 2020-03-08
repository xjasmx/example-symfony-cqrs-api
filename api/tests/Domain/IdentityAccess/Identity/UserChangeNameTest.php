<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Tests\AssertEvent;
use App\Domain\IdentityAccess\Identity\{Event\UserNameWasChanged,
    Exception\InvalidCredentialsException,
    Exception\UserAlreadyExistException,
    Service\UserUniquenessCheckerByIdInterface,
    User,
    ValueObject\Name,
    ValueObject\UserId
};
use App\Domain\Shared\Exception\DateTimeException;
use PHPUnit\Framework\TestCase;

class UserChangeNameTest extends TestCase
{
    use AssertEvent;

    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';

    private User $user;

    /**
     * @test
     * @group unit
     */
    public function nameShouldBeChange(): void
    {
        $this->user->changeName(Name::fromString($newFirst = 'NewFirst', $newLast = 'NewLast'));
        self::assertNotSame($this->nameFirst, $newFirst);
        self::assertNotSame($this->nameLast, $newLast);

        self::assertTrue($this->assertEvent($this->user->releaseEvents(), UserNameWasChanged::class));
    }

    /**
     * @test
     * @group unit
     */
    public function ifAnEmptyNameIsSpecifiedDuringTheChangeAnExceptionShouldBeThrown(): void
    {
        $this->expectException(InvalidCredentialsException::class);
        $this->user->changeName(Name::fromString('', ''));
    }

    /**
     * @before
     * @throws UserAlreadyExistException
     * @throws DateTimeException
     */
    public function createUser(): void
    {
        $checker = $this->createStub(UserUniquenessCheckerByIdInterface::class);
        $checker->method('isUnique')->willReturn(true);

        $this->user = User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $checker
        );
    }
}
