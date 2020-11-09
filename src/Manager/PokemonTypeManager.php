<?php

namespace App\Manager;

use App\Behavior\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

final class PokemonTypeManager implements ManagerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function persist(EntityInterface $pokemonType): void
    {
        $this->entityManager->persist($pokemonType);
        $this->entityManager->flush();
    }

    public function update(EntityInterface $entity): void
    {
        $this->entityManager->flush();
    }

    public function remove(EntityInterface $pokemonType): void
    {
        $this->entityManager->remove($pokemonType);
        $this->entityManager->flush();
    }
}
