<?php

declare(strict_types=1);

namespace App\Ui\Http\Rest\Controller\V1\IdentityAccess\Identity;

use App\Application\IdentityAccess\Identity\Command\User\EmailRegistrationConfirmationCommand;
use App\Application\IdentityAccess\Identity\Command\User\EmailRegistrationConfirmationHandler;
use App\Application\IdentityAccess\Identity\Command\User\RegisterUserByEmailCommand;
use App\Application\IdentityAccess\Identity\Command\User\RegisterUserByEmailHandler;
use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use App\Domain\IdentityAccess\Identity\Exception\UserActivationException;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Exception\UserPropertyException;
use App\Domain\Shared\Exception\DateTimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/signup")
 */
class UserRegistrationByEmailController extends AbstractController
{
    private SerializerInterface $serializer;

    /**
     * UserRegistrationByEmailController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "",
     *     name="user_create",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param RegisterUserByEmailHandler $handler
     * @return JsonResponse
     * @throws \Exception
     */
    public function registration(Request $request, RegisterUserByEmailHandler $handler): JsonResponse
    {
        /** @var RegisterUserByEmailCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), RegisterUserByEmailCommand::class, 'json');

        $handler->handle($command);

        return $this->json([], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/confirm",
     *     name="confirm_token",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param EmailRegistrationConfirmationHandler $handler
     * @return JsonResponse
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     * @throws UserNotFoundException
     * @throws DateTimeException
     */
    public function confirm(Request $request, EmailRegistrationConfirmationHandler $handler): JsonResponse
    {
        /** @var EmailRegistrationConfirmationCommand $command */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            EmailRegistrationConfirmationCommand::class,
            'json'
        );

        $handler->handle($command);

        return $this->json([$request->getContent()], JsonResponse::HTTP_CREATED);
    }
}
