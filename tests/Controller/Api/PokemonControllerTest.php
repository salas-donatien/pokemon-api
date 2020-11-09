<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\PokemonFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class PokemonControllerTest extends AbstractControllerTest
{
    private KernelBrowser $client;

    /**
     * @group functional
     */
    public function testMethodGetPokemons(): void
    {
        $this->client->request('GET', '/api/pokemons');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame(
            'Content-Type',
            'application/json'
        );
    }

    /**
     * @group functional
     */
    public function testMethodPostPokemons(): void
    {
        $json = '
        {
            "name": "Poke",
            "main_type": {
                "type": "Grass"
            },
            "secondary_type": {
                "type": "Poison"
            },
            "hit_points": "1",
            "attack": "2",
            "defense": "3",
            "speed_attack": "4",
            "speed_defense": "5",
            "speed": "6",
            "generation": "1",
            "legendary":true
        }';

        $this->client->request(
            'POST',
            '/api/pokemons',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_CREATED
        );
    }

    /**
     * @group functional
     */
    public function testMethodPostInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/pokemons',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @group functional
     */
    public function testMethodPutPokemons(): void
    {
        $pokemon = $this->getPokemonReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_pokemons_edit',
            [
                'uuid' => $pokemon->getUuid(),
            ]
        );

        $this->client->request(
            'PUT',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"SuperPoke"}'
        );

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('SuperPoke', $content['name']);
    }

    private function getPokemonReference()
    {
        return $this->fixtures->getReference(PokemonFixtures::POKEMON);
    }

    /**
     * @group functional
     */
    public function testMethodDeletePokemons(): void
    {
        $pokemon = $this->getPokemonReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_pokemons_delete',
            [
                'uuid' => $pokemon->getUuid(),
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
    public function testMethodShowPokemon(): void
    {
        $pokemon = $this->getPokemonReference();

        $url = static::$kernel->getContainer()->get('router')->generate(
            'api_pokemons_show',
            [
                'uuid' => $pokemon->getUuid(),
            ]
        );

        $this->client->request('GET', $url);

        $this->assertJsonResponse(
            $this->client->getResponse(),
            Response::HTTP_OK
        );
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame($pokemon->getName(), $content['name']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createAuthenticatedClient('api@api.com', 'password');
    }
}
