<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagerTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    private UserManager $manager;

    private UserPasswordEncoderInterface $passwordEncoder;

    private User $user;

    public function testPersist(): void
    {
        $this->em->expects(self::once())->method('persist');
        $this->em->expects(self::once())->method('flush');

        $this->passwordEncoder->expects(self::once())->method('encodePassword')->willReturn('foobar');

        $this->manager->persist($this->user);
    }

    public function testDelete(): void
    {
        $this->em->expects(self::once())->method('remove');
        $this->em->expects(self::once())->method('flush');

        $this->manager->remove($this->user);
    }

    public function testUpdate(): void
    {
        $this->em->expects(self::once())->method('flush');

        $this->manager->update($this->user);
    }

    protected function setUp(): void
    {
        $this->user = User::create()
            ->setUsername('chuck_norris')
            ->setEmail('chuck@norris.com')
            ->setPassword('foobar');

        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->manager = new UserManager($this->em, $this->passwordEncoder);
    }
}
