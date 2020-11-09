<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends AbstractControllerTest
{
    private KernelBrowser $client;

    /**
     * @group functional
     */
    public function testMethodGetUsers(): void
    {
        $this->client->request('GET', '/api/users');

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );
    }

    /**
     * @group functional
     */
    public function testMethodPostUsers(): void
    {
        $json = '{"username":"chuck_norris", "email":"chuck@norris.com", "password":"foobar"}';

        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_CREATED
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('chuck_norris', $content['username']);
    }

    /**
     * @group functional
     */
    public function testMethodPostInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @group functional
     */
    public function testMethodPutUsers(): void
    {
        $user = $this->getUserReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_users_edit',
            [
                'uuid' => $user->getUuid(),
            ]
        );

        $this->client->request(
            'PUT',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"chuck_norris"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('chuck_norris', $content['username']);
    }

    private function getUserReference()
    {
        return $this->fixtures->getReference(UserFixtures::ROLE_ADMIN);
    }

    /**
     * @group functional
     */
    public function testMethodDeleteUsers(): void
    {
        $user = $this->getUserReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_users_delete',
            [
                'uuid' => $user->getUuid(),
            ]
        );

        $this->client->request('DELETE', $url);

        self::assertResponseStatusCodeSame(
            Response::HTTP_NO_CONTENT,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * @group functional
     */
    public function testMethodShowUsers(): void
    {
        $user = $this->getUserReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_users_show',
            [
                'uuid' => $user->getUuid(),
            ]
        );

        $this->client->request('GET', $url);

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame($user->getEmail(), $content['email']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createAuthenticatedClient('api@api.com', 'password');
    }
}
