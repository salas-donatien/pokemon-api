<?php

namespace App\Repository;

use App\Entity\PokemonType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PokemonType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PokemonType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PokemonType[]    findAll()
 * @method PokemonType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PokemonTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonType::class);
    }

    public function search(?string $keyword, ?string $order)
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.type', $order);

        if ($keyword) {
            $qb
                ->where('p.type LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
