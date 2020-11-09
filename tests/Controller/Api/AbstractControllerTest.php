<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\PokemonFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

abstract class AbstractControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var ReferenceRepository
     */
    protected ReferenceRepository $fixtures;

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixtures([
            UserFixtures::class,
            PokemonFixtures::class,
        ])->getReferenceRepository();

        self::ensureKernelShutdown();
    }

    protected function createAuthenticatedClient(string $email, string $password): KernelBrowser
    {
        Assert::email($email);

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email'    => $email,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    protected function assertJsonResponse($response, int $statusCode = Response::HTTP_OK): void
    {
        self::assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        self::assertResponseHeaderSame(
            'Content-Type',
            'application/json'
        );
    }
}
