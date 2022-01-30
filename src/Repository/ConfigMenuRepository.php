<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\ConfigMenu;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method ConfigMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigMenu[]    findAll()
 * @method ConfigMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigMenuRepository extends ServiceEntityRepository implements BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigMenu::class);
    }

    public function findLatest(array $params): Paginator
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function filter(array $params, bool $inArray = true): array
    {
        $queryBuilder = $this->filterQuery($params);

        if (true === $inArray) {
            return $queryBuilder->getQuery()->getArrayResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    private function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('configMenu')
            ->select('configMenu')
            ->orderBy('configMenu.name', 'ASC')
        ;

        Paginator::queryTexts($queryBuilder, $params, ['configMenu.name']);

        return $queryBuilder;
    }
}
