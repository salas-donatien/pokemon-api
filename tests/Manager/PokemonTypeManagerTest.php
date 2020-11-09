<?php

namespace App\Tests\Manager;

use App\Entity\PokemonType;
use App\Manager\PokemonTypeManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PokemonTypeManagerTest extends TestCase
{
    private EntityManagerInterface $em;

    private PokemonTypeManager $manager;

    private PokemonType $pokemonType;

    public function testPersist(): void
    {
        $this->em->expects(self::once())->method('persist');
        $this->em->expects(self::once())->method('flush');

        $this->manager->persist($this->pokemonType);
    }

    public function testDelete(): void
    {
        $this->em->expects(self::once())->method('remove');
        $this->em->expects(self::once())->method('flush');

        $this->manager->remove($this->pokemonType);
    }

    public function testUpdate(): void
    {
        $this->em->expects(self::once())->method('flush');

        $this->manager->update($this->pokemonType);
    }

    protected function setUp(): void
    {
        $this->pokemonType = new PokemonType();

        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->manager = new PokemonTypeManager($this->em);
    }
}
