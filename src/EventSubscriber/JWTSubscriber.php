<?php

namespace App\EventSubscriber;

use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\User\UserInterface;

final class JWTSubscriber implements EventSubscriberInterface
{
    private const REFRESH_TIME = 600;

    private array $payload = [];

    private UserInterface $user;

    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            Events::JWT_AUTHENTICATED      => 'onAuthenticatedAccess',
            KernelEvents::RESPONSE         => 'onAuthenticatedResponse',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $eventData = $event->getData();

        if (isset($eventData['token'])) {
            $response = $event->getResponse();
            $jwt      = $eventData['token'];

            $this->createCookie($response, $jwt);
        }
    }

    protected function createCookie(Response $response, $jwt): void
    {
        $response->headers->setCookie(
            new Cookie(
                "BEARER",
                $jwt,
                new DateTimeImmutable('+1 day'),
                "/",
                null,
                true,
                true,
                false,
                'strict'
            )
        );
    }

    public function onAuthenticatedAccess(JWTAuthenticatedEvent $event): void
    {
        $this->payload = $event->getPayload();
        $this->user    = $event->getToken()->getUser();
    }

    public function onAuthenticatedResponse(ResponseEvent $event): void
    {
        if ($this->payload && $this->user) {
            $expireTime = $this->payload['exp'] - time();
            if ($expireTime < static::REFRESH_TIME) {
                $jwt = $this->jwtManager->create($this->user);
                $this->createCookie($event->getResponse(), $jwt);
            }
        }
    }
}
