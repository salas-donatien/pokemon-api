<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\PokemonFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class PokemonTypeControllerTest extends AbstractControllerTest
{
    private KernelBrowser $client;

    /**
     * @group functional
     */
    public function testMethodGetPokemonTypes(): void
    {
        $this->client->request('GET', '/api/pokemon_types');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame(
            'Content-Type',
            'application/json'
        );
    }

    /**
     * @group functional
     */
    public function testMethodPostPokemonTypes(): void
    {
        $this->client->request(
            'POST',
            '/api/pokemon_types',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"type":"GrassDragon"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_CREATED
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('GrassDragon', $content['type']);
    }

    /**
     * @group functional
     */
    public function testMethodPostInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/pokemon_types',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"type"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @group functional
     */
    public function testMethodPutPokemonTypes(): void
    {
        $pokemonType = $this->getPokemonTypeReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_pokemon_types_edit',
            [
                'uuid' => $pokemonType->getUuid(),
            ]
        );

        $this->client->request(
            'PUT',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"type":"Poke"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('Poke', $content['type']);
    }

    private function getPokemonTypeReference()
    {
        return $this->fixtures->getReference(PokemonFixtures::POKEMON_TYPE);
    }

    /**
     * @group functional
     */
    public function testMethodDeletePokemonTypes(): void
    {
        $pokemonType = $this->getPokemonTypeReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_pokemon_types_delete',
            [
                'uuid' => $pokemonType->getUuid(),
            ]
        );

        $this->client->request('DELETE', $url);

        self::assertResponseStatusCodeSame(
            Response::HTTP_NO_CONTENT,
            $this->client->getResponse()->getStatusCode()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createAuthenticatedClient('api@api.com', 'password');
    }
}
