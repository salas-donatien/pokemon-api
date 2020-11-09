<?php

namespace App\Tests\Manager;

use App\Entity\Pokemon;
use App\Manager\PokemonManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PokemonManagerTest extends TestCase
{
    private EntityManagerInterface $em;

    private PokemonManager $manager;

    private Pokemon $pokemon;

    public function testPersist(): void
    {
        $this->em->expects(self::once())->method('persist');
        $this->em->expects(self::once())->method('flush');

        $this->manager->persist($this->pokemon);
    }

    public function testDelete(): void
    {
        $this->em->expects(self::once())->method('remove');
        $this->em->expects(self::once())->method('flush');

        $this->manager->remove($this->pokemon);
    }

    public function testUpdate(): void
    {
        $this->em->expects(self::once())->method('flush');

        $this->manager->update($this->pokemon);
    }

    protected function setUp(): void
    {
        $this->pokemon = new Pokemon();

        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->manager = new PokemonManager($this->em);
    }
}
