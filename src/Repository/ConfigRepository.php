<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Util\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    public function findLatest(array $params): Paginator
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
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
        $queryBuilder = $this->createQueryBuilder('config')
            ->select('config')
        ;

        Paginator::queryTexts($queryBuilder, $params, ['config.nombre']);

        return $queryBuilder;
    }

    public function findMenusByConfigId(int $configId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $subQuery = $queryBuilder
            ->select('xmenu.id')
            ->from(Menu::class, 'xmenu')
            ->join('xmenu.config', 'xconfig')
            ->where('xmenu.activo = true')
            ->andWhere('xconfig.id = :config_id')
            ->andWhere('xmenu.ruta = menus.route')
        ;

        return $this->createQueryBuilder('config')
            ->select('menus.name as name')
            ->addSelect('menus.route as route')
            ->leftJoin('config.menus', 'menus')
            ->where('config.activo = true')
            ->andWhere('config.id = :config_id')
            ->andWhere($queryBuilder->expr()->not($queryBuilder->expr()->exists($subQuery->getDql())))
            ->setParameter('config_id', $configId)
            ->orderBy('menus.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('config')
            ->select('config')
            ;
    }
}
