<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function search(
        ?string $keyword,
        ?string $mainType,
        ?string $secondaryType,
        string $order = 'ASC'
    ) {
        $qb = $this
            ->createQueryBuilder('p')
            ->orderBy('p.name', $order);

        $this->addLikeCondition($qb, $keyword);
        $this->addPokemonMainTypeCondition($qb, $mainType);
        $this->addPokemonSecondaryTypeCondition($qb, $secondaryType);

        return $qb->getQuery()->getResult();
    }

    private function addLikeCondition(QueryBuilder $qb, ?string $keyword): void
    {
        if ($keyword) {
            $qb
                ->where('p.name LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword);
        }
    }

    private function addPokemonMainTypeCondition(QueryBuilder $qb, ?string $type): void
    {
        if ($type) {
            $qb
                ->innerJoin('p.mainType', 'mainType')
                ->andWhere('mainType.type = :main')
                ->setParameter('main', $type);
        }
    }

    private function addPokemonSecondaryTypeCondition(QueryBuilder $qb, ?string $type): void
    {
        if ($type) {
            $qb
                ->join('p.secondaryType', 'secondaryType')
                ->andWhere('secondaryType.type = :secondary')
                ->setParameter('secondary', $type);
        }
    }
}
