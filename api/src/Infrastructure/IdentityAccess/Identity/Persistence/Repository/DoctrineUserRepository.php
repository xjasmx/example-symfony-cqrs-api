<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\Repository;

use App\Domain\IdentityAccess\Identity\{
    User,
    ValueObject\Email,
    ValueObject\UserId,
    Exception\UserNotFoundException,
    Repository\UserRepositoryInterface
};
use App\Domain\Shared\Event\{AggregateRoot, EventDispatcherInterface};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
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
        return $this->userOf('id', $userId);
    }

    /**
     * @param Email $email
     * @return User
     * @throws UserNotFoundException
     */
    public function userOfEmail(Email $email): User
    {
        return $this->userOf('email', $email);
    }

    /**
     * @return UserId
     * @throws \Exception
     */
    public function nextIdentity(): UserId
    {
        return UserId::fromString(Uuid::uuid4()->toString());
    }

    /**
     * @param string $criterion
     * @param object $search
     * @return User
     * @throws UserNotFoundException
     */
    private function userOf(string $criterion, object $search): User
    {
        if (!$user = $this->repository->findOneBy([$criterion => (string)$search])) {
            throw new UserNotFoundException();
        }

        /** @var User $user */
        return $user;
    }
}
