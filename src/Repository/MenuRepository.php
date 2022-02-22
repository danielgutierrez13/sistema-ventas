<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Security\Security;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository implements BaseRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Menu::class);
        $this->security = $security;
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
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select(['menu', 'padre'])
            ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
        ;

        $this->security->configQuery($queryBuilder, true);

        Paginator::queryTexts($queryBuilder, $params, ['menu.nombre', 'padre.nombre']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('padre.nombre as padre_nombre')
            ->addSelect('menu.id as menuId')
            ->addSelect('menu.nombre as nombre')
            ->addSelect('menu.ruta as ruta')
            ->addSelect('menu.icono as icono')
            ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
            ->where('menu.activo = TRUE')
            ->orderBy('padre.orden', 'ASC')
            ->addOrderBy('menu.orden', 'ASC')
            ->addOrderBy('menu.nombre', 'ASC')
            ;

        $this->security->configQuery($queryBuilder, true);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
