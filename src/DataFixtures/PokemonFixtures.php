<?php

namespace App\DataFixtures;

use App\Entity\Generation;
use App\Entity\Pokemon;
use App\Entity\PokemonType;
use App\Repository\PokemonTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonFixtures extends Fixture
{
    public const POKEMON_TYPE = 'POKEMON_TYPE';
    public const POKEMON      = 'POKEMON';

    private HttpClientInterface $client;

    private PokemonTypeRepository $pokemonTypeRepository;

    private string $pokemonDataUrl;

    public function __construct(
        HttpClientInterface $client,
        PokemonTypeRepository $pokemonTypeRepository,
        string $pokemonDataUrl
    ) {
        $this->client                = $client;
        $this->pokemonTypeRepository = $pokemonTypeRepository;
        $this->pokemonDataUrl        = $pokemonDataUrl;
    }

    public function load(ObjectManager $manager): void
    {
        $pokemonCsv = $this->getContent();

        if (!empty($pokemonCsv)) {
            $rows = explode("\n", $pokemonCsv);

            foreach ($rows as $index => $row) {
                if ($index !== 0 && $index < count($rows) - 1) {
                    $rowAsArray = explode(',', $row);
                    if (count($rowAsArray) > 1) {
                        try {
                            $this->createPokemon($manager, $rowAsArray);
                        } catch (Exception $exception) {
                            throw $exception;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get content of file csv
     */
    private function getContent(): ?string
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->pokemonDataUrl
            );

            return $response->getContent();
        } catch (ClientException $exception) {
            throw $exception;
        }
    }

    private function createPokemon(ObjectManager $manager, array $rowAsArray): void
    {
        $mainType      = $this->createPokemonType($manager, trim($rowAsArray[2]));
        $secondaryType = $rowAsArray[3] ? $this->createPokemonType($manager, trim($rowAsArray[3])) : null;

        $pokemon = Pokemon::create()
            ->setName($rowAsArray[1])
            ->setHitPoints($rowAsArray[5])
            ->setAttack($rowAsArray[6])
            ->setDefense($rowAsArray[7])
            ->setSpeedAttack($rowAsArray[8])
            ->setSpeedDefense($rowAsArray[9])
            ->setSpeed($rowAsArray[10])
            ->setGeneration($rowAsArray[11])
            ->setLegendary($rowAsArray[12] !== 'False')
            ->setMainType($mainType)
            ->setSecondaryType($secondaryType);

        $this->setReference(self::POKEMON_TYPE, $mainType);
        $this->setReference(self::POKEMON, $pokemon);

        $manager->persist($pokemon);
        $manager->flush();
    }

    private function createPokemonType(ObjectManager $manager, string $type): PokemonType
    {
        $pokemonType = $this->pokemonTypeRepository->findOneBy(['type' => $type]);

        if ($pokemonType) {
            return $pokemonType;
        }

        $pokemonType = PokemonType::create()->setType($type);

        $manager->persist($pokemonType);
        $manager->flush();

        return $pokemonType;
    }
}
