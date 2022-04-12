<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Pidia\Apps\Demo\Controller\TipoMonedaController;
use Pidia\Apps\Demo\Controller\TipoPagoController;
use Pidia\Apps\Demo\Entity\TipoPago;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoPago|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoPago|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoPago[]    findAll()
 * @method TipoPago[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoPagoRepository  extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, TipoPago::class);
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
        $queryBuilder = $this->createQueryBuilder('tipoPago')
            ->select(['tipoPago'])
            ->join('tipoPago.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, TipoPagoController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['tipoPago.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('tipoPago')
            ->select('tipoPago.descripcion as descripcion')
            ->where('tipoPago.activo = TRUE')
            ->orderBy('tipoPago.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, TipoPagoController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('tipoPago')
            ->select(['tipoPago'])
            ->join('tipoPago.config', 'config')
            ;
    }
}
