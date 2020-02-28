<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Event\Subscriber;

use App\Domain\IdentityAccess\Identity\Event\UserWasRegisteredByEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;

    /**
     * EmailNotificationSubscriber constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserWasRegisteredByEmail::class => [
                ['onUserSignedUpByEmail']
            ]
        ];
    }

    /**
     * @param UserWasRegisteredByEmail $event
     * @throws TransportExceptionInterface
     */
    public function onUserSignedUpByEmail(UserWasRegisteredByEmail $event): void
    {
        $email = (new Email())
            ->from('tes@ax.com')
            ->to((string)$event->getEmail())
            ->subject('Confirm Token')
            ->text("Token: {$event->getConfirmToken()->token()}");

        $this->mailer->send($email);
    }
}
