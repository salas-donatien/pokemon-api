<?php

namespace App\Manager;

use App\Behavior\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserManager implements ManagerInterface
{
    private EntityManagerInterface $entityManager;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persist(EntityInterface $user): void
    {
        $passwordEncoder = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($passwordEncoder);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(EntityInterface $entity): void
    {
        $this->entityManager->flush();
    }

    public function remove(EntityInterface $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
