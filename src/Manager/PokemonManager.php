<?php

namespace App\Manager;

use App\Behavior\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

final class PokemonManager implements ManagerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function persist(EntityInterface $pokemon): void
    {
        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();
    }

    public function update(EntityInterface $entity): void
    {
        $this->entityManager->flush();
    }

    public function remove(EntityInterface $pokemon): void
    {
        $this->entityManager->remove($pokemon);
        $this->entityManager->flush();
    }
}
