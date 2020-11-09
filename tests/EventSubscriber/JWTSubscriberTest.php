<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\JWTSubscriber;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;

final class JWTSubscriberTest extends TestCase
{
    public function testEventsSubscribed(): void
    {
        self::assertArrayHasKey(
            Events::AUTHENTICATION_SUCCESS,
            JWTSubscriber::getSubscribedEvents()
        );

        self::assertArrayHasKey(
            Events::JWT_AUTHENTICATED,
            JWTSubscriber::getSubscribedEvents()
        );

        self::assertArrayHasKey(
            KernelEvents::RESPONSE,
            JWTSubscriber::getSubscribedEvents()
        );
    }
}
