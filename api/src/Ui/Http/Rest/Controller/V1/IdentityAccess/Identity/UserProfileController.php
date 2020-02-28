<?php

declare(strict_types=1);

namespace App\Ui\Http\Rest\Controller\V1\IdentityAccess\Identity;

use App\Application\IdentityAccess\Identity\Command\User\ChangeUserEmailCommand;
use App\Application\IdentityAccess\Identity\Command\User\ChangeUserEmailHandler;
use App\Application\IdentityAccess\Identity\Command\User\ChangeUserNameCommand;
use App\Application\IdentityAccess\Identity\Command\User\ChangeUserNameHandler;
use App\Application\IdentityAccess\Identity\Command\User\ChangeUserPasswordCommand;
use App\Application\IdentityAccess\Identity\Command\User\ChangeUserPasswordHandler;
use App\Application\IdentityAccess\Identity\Query\User\GetUserCredentialFindByIdHandler;
use App\Application\IdentityAccess\Identity\Query\User\GetUserCredentialFindByIdQuery;
use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\ReadModel\UserProfileView;
use App\Domain\IdentityAccess\Identity\ReadModel\UserView;
use App\Infrastructure\IdentityAccess\Access\Authorization\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/profile")
 */
class UserProfileController extends AbstractController
{
    private SerializerInterface $serializer;

    /**
     * UserProfileController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "",
     *     name="user_profile",
     *     methods={"GET"}
     * )
     * @param GetUserCredentialFindByIdHandler $handler
     * @return Response
     */
    public function show(GetUserCredentialFindByIdHandler $handler): Response
    {
        /** @var UserIdentity|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw new InvalidCredentialsException('User not found');
        }

        $query = new GetUserCredentialFindByIdQuery((string)$user->getId());

        /** @var UserProfileView $user */
        $user = $handler->handle($query);

        return $this->json(
            [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName()
            ]
        );
    }

    /**
     * @Route("/name", name="change_user_name", methods={"PATCH"})
     * @param Request $request
     * @param ChangeUserNameHandler $handler
     * @return JsonResponse
     * @throws UserNotFoundException
     */
    public function changeName(Request $request, ChangeUserNameHandler $handler): JsonResponse
    {
        /** @var ChangeUserNameCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), ChangeUserNameCommand::class, 'json');

        $handler->handle($command);

        return $this->json([], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/email", name="change_user_email", methods={"PATCH"})
     * @param Request $request
     * @param ChangeUserEmailHandler $handler
     * @return JsonResponse
     * @throws UserNotFoundException
     */
    public function changeEmail(Request $request, ChangeUserEmailHandler $handler): JsonResponse
    {
        /** @var ChangeUserEmailCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), ChangeUserEmailCommand::class, 'json');

        $handler->handle($command);

        return $this->json([], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/password", name="change_user_password", methods={"PATCH"})
     * @param Request $request
     * @param ChangeUserPasswordHandler $handler
     * @return JsonResponse
     * @throws UserNotFoundException
     */
    public function changePassword(Request $request, ChangeUserPasswordHandler $handler): JsonResponse
    {
        /** @var ChangeUserPasswordCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), ChangeUserPasswordCommand::class, 'json');

        $handler->handle($command);

        return $this->json([], JsonResponse::HTTP_OK);
    }
}
