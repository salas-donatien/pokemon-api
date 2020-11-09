<?php

namespace App\Repository;

use App\Behavior\EntityInterface;
use App\Entity\User;
use App\Manager\UserManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserManagerInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function search(?string $keyword, string $order = 'ASC')
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', $order);

        if ($keyword) {
            $qb
                ->where('u.username LIKE :keyword')
                ->orWhere('u.email LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword);
        }

        return $qb->getQuery()->getResult();
    }


    public function persist(EntityInterface $entity): void
    {
        $this->_em->persist($entity);
    }

    public function update(EntityInterface $entity): void
    {
        $this->_em->flush();
    }

    public function remove(EntityInterface $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
