<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\DetalleCompra;

/**
 * @method DetalleCompra|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetalleCompra|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetalleCompra[]    findAll()
 * @method DetalleCompra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetalleCompraRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, DetalleCompra::class);
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
        $queryBuilder = $this->createQueryBuilder('detalleCompra')
        ;


        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['detalleCompra.nombre', 'detalleCompra.documento', 'tipoPersona.descripcion',  'tipoDocumento.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('detalleCompra')
        ;

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('detalleCompra')
            ->select(['detalleCompra'])
            ->join('detalleCompra.config', 'config')
            ;
    }
}
