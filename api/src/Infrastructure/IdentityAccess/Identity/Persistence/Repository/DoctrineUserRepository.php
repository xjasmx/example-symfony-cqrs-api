<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\Repository;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\Shared\Event\AggregateRoot;
use App\Domain\Shared\Event\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Ramsey\Uuid\Uuid;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $manager;
    /** @var ObjectRepository<User> */
    private ObjectRepository $repository;
    private EventDispatcherInterface $dispatcher;

    /**
     * DoctrineUserRepository constructor.
     * @param EntityManagerInterface $manager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $manager, EventDispatcherInterface $dispatcher)
    {
        $this->repository = $manager->getRepository(User::class);
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param AggregateRoot $user
     */
    public function add(AggregateRoot $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();

        $this->dispatcher->dispatch($user->releaseEvents());
    }

    /**
     * @param UserId $userId
     * @return User
     * @throws UserNotFoundException
     */
    public function userOfId(UserId $userId): User
    {
        if (!$user = $this->repository->find((string)$userId)) {
            throw new UserNotFoundException();
        }

        /** @var User $user */
        return $user;
    }

    /**
     * @param Email $email
     * @return User
     * @throws UserNotFoundException
     */
    public function userOfEmail(Email $email): User
    {
        if (!$user = $this->repository->findOneBy(['email' => (string)$email])) {
            throw new UserNotFoundException();
        }

        /** @var User $user */
        return $user;
    }

    /**
     * @return UserId
     * @throws Exception
     */
    public function nextIdentity(): UserId
    {
        return UserId::fromString(Uuid::uuid4()->toString());
    }
}
