<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\{Event\UserPasswordWasChanged,
    Exception\InvalidCredentialsException,
    Exception\UserAlreadyExistException,
    Service\PasswordHasherInterface,
    Service\UserUniquenessCheckerByIdInterface,
    User,
    ValueObject\Name,
    ValueObject\UserId};
use App\Domain\Shared\Exception\DateTimeException;
use App\Tests\AssertEvent;
use Monolog\Test\TestCase;

class UserChangePasswordTest extends TestCase
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
    public function passwordShouldBeChange(): void
    {
        /** @var PasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->createHasher(true, $hash = 'newPasswordHash');

        $oldPassword = $this->user->getPassword();
        $this->user->changePassword($oldPassword, $newPassword = 'newPassword', $passwordHasher);

        self::assertNotNull($this->user->getPassword());

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
}
