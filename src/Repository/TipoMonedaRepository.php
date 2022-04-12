<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\TipoMonedaController;
use Pidia\Apps\Demo\Entity\TipoMoneda;

/**
 * @method TipoMoneda|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoMoneda|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoMoneda[]    findAll()
 * @method TipoMoneda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoMonedaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, TipoMoneda::class);
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
        $queryBuilder = $this->createQueryBuilder('tipoMoneda')
            ->select(['tipoMoneda'])
            ->join('tipoMoneda.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, TipoMonedaController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['tipoMoneda.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('tipoMoneda')
            ->select('tipoMoneda.descripcion as descripcion')
            ->where('tipoMoneda.activo = TRUE')
            ->orderBy('tipoMoneda.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, TipoMonedaController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('tipoMoneda')
            ->select(['tipoMoneda'])
            ->join('tipoMoneda.config', 'config')
            ;
    }
}
