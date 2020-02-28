<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Event\UserNameWasChanged;
use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use App\Domain\Shared\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class UserChangeNameTest extends TestCase
{
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
     */
    public function createUser(): void
    {
        /** @var User UserUniqueIdSpecificationInterface */
        $uniqueEmailSpecification = $this->createStub(UserUniqueIdSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn(true);

        $this->user = User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $this->date = new \DateTimeImmutable(),
            $uniqueEmailSpecification
        );
    }

    /**
     * @param DomainEvent[] $recodedEvents
     * @param string $eventClass
     * @return bool
     */
    private function assertEvent(array $recodedEvents, string $eventClass): bool
    {
        foreach ($recodedEvents as $event) {
            if (get_class($event) === $eventClass) {
                return true;
            }
        }

        return false;
    }
}
