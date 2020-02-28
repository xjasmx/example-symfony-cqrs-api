<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Subscriber;

use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private string $environment;

    /**
     * ExceptionSubscriber constructor.
     *
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/vnd.api+json');
        $response->setStatusCode($this->getStatusCode($exception));
        $response->setData($this->getErrorMessage($exception, $response));

        $event->setResponse($response);
    }

    /**
     * @param Throwable $exception
     * @return int
     */
    private function getStatusCode(Throwable $exception): int
    {
        return $this->determineStatusCode($exception);
    }

    /**
     * @param Throwable $exception
     * @param Response $response
     * @return array
     */
    private function getErrorMessage(Throwable $exception, Response $response): array
    {
        $error = [
            'errors' => [
                'title' => str_replace('\\', '.', \get_class($exception)),
                'detail' => $this->getExceptionMessage($exception),
                'code' => $exception->getCode(),
                'status' => $response->getStatusCode(),
            ],
        ];

        if ($this->environment === 'dev') {
            $error = array_merge(
                $error,
                [
                    'meta' => [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTrace(),
                        'traceString' => $exception->getTraceAsString(),
                    ],
                ]
            );
        }

        return $error;
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    private function getExceptionMessage(Throwable $exception): string
    {
        return $exception->getMessage();
    }

    /**
     * @param Throwable $exception
     * @return int
     */
    private function determineStatusCode(Throwable $exception): int
    {
        switch (true) {
            case $exception instanceof \InvalidArgumentException
                || $exception instanceof UniqueConstraintViolationException
                || $exception instanceof UserAlreadyExistException
                || $exception instanceof InvalidCredentialsException
                || $exception instanceof UserNotFoundException:
                $statusCode = Response::HTTP_BAD_REQUEST;

                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;

                break;
            default:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $statusCode;
    }
}
