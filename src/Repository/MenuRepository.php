<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\ConfigController;
use Pidia\Apps\Demo\Controller\MenuController;
use Pidia\Apps\Demo\Entity\Menu;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Menu::class);
    }

    public function filter(array|ParamFetcher $params, bool $inArray = true, array $permissions = []): array
    {
        $queryBuilder = $this->filterQuery($params, $permissions);

        if (true === $inArray) {
            return $queryBuilder->getQuery()->getArrayResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterQuery(array|ParamFetcher $params, array $permissions = []): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select(['menu', 'parent'])
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
        ;

        $this->security->filterQuery($queryBuilder, ConfigController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['menu.name', 'parent.name']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('parent.name as parent_name')
            ->addSelect('menu.id as menuId')
            ->addSelect('menu.name as name')
            ->addSelect('menu.route as route')
            ->addSelect('menu.icono as icono')
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
            ->where('menu.activo = TRUE')
            ->orderBy('parent.ranking', 'ASC')
            ->addOrderBy('menu.ranking', 'ASC')
            ->addOrderBy('menu.name', 'ASC')
            ;

        $this->security->filterQuery($queryBuilder, MenuController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allForMenus(): array
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('parent.name as parentName')
            ->addSelect('menu.name as name')
            ->addSelect('menu.route as route')
            ->leftJoin('menu.parent', 'parent')
            ->leftJoin('menu.config', 'config')
            ->where('menu.activo = TRUE')
            ->andWhere('menu.parent IS NOT NULL')
            ->orderBy('parent.ranking', 'ASC')
            ->addOrderBy('menu.ranking', 'ASC')
            ->addOrderBy('menu.name', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, MenuController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('menu')
            ->select(['menu', 'parent'])
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
            ;
    }

    /** @return Menu[] */
    public function searchAllActiveWithOrder(): array
    {
        return $this->allQuery()
            ->where('menu.activo = true')
            ->orderBy('parent.ranking')
            ->addOrderBy('menu.ranking')
            ->addOrderBy('menu.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
