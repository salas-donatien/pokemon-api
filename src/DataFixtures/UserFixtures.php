<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserFixtures extends Fixture
{
    public const ROLE_API   = 'api';
    public const ROLE_ADMIN = 'admin';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = User::create();
        $user->setUsername('api')
            ->setEmail('api@api.com')
            ->setPassword($this->encodePassword($user));

        $this->setReference(self::ROLE_API, $user);
        $manager->persist($user);

        $user = clone $user;
        $user->setUsername('admin')
            ->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encodePassword($user));

        $this->setReference(self::ROLE_ADMIN, $user);
        $manager->persist($user);

        $manager->flush();
    }

    private function encodePassword(UserInterface $user): string
    {
        return $this->passwordEncoder->encodePassword($user, 'password');
    }
}
