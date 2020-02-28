<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Event\UserPasswordWasChanged;
use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use App\Domain\Shared\Event\DomainEvent;
use Monolog\Test\TestCase;

class UserChangePasswordTest extends TestCase
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
    public function passwordShouldBeChange(): void
    {
        /** @var PasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->createHasher(true, $hash = 'newPasswordHash');

        $oldPassword = $this->user->password();
        $this->user->changePassword($oldPassword, $newPassword = 'newPassword', $passwordHasher);

        self::assertNotNull($this->user->password());

        self::assertNotSame($oldPassword, $newPassword);

        self::assertTrue($this->assertEvent($this->user->releaseEvents(), UserPasswordWasChanged::class));
    }

    /**
     * @test
     * @group unit
     */
    public function thereShouldBeAnExceptionIfTheSpecifiedCurrentPasswordIsDifferentFromTheActualCurrentPassword(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        /** @var PasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->createHasher(false, $hash = 'newPasswordHash');

        $this->expectExceptionMessage('Incorrect current password.');
        $this->user->changePassword('wrongCurrentPassword', $newPassword = 'newPassword', $passwordHasher);
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
     * @param bool $valid
     * @param string $hash
     * @return PasswordHasherInterface
     */
    private function createHasher(bool $valid, string $hash): PasswordHasherInterface
    {
        $hasher = $this->createStub(PasswordHasherInterface::class);
        $hasher->method('validate')->willReturn($valid);
        $hasher->method('hash')->willReturn($hash);
        return $hasher;
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
