<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\ProveedorController;
use Pidia\Apps\Demo\Entity\Proveedor;

/**
 * @method Proveedor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proveedor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proveedor[]    findAll()
 * @method Proveedor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProveedorRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Proveedor::class);
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
        $queryBuilder = $this->createQueryBuilder('proveedor')
            ->select(['proveedor', 'tipoPersona', 'tipoDocumento'])
            ->join('proveedor.config', 'config')
            ->join('proveedor.tipoPersona', 'tipoPersona')
            ->join('proveedor.tipoDocumento', 'tipoDocumento')
        ;

        $this->security->filterQuery($queryBuilder, ProveedorController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['proveedor.nombre', 'proveedor.documento', 'tipoPersona.descripcion',  'tipoDocumento.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('proveedor')
            ->select('proveedor.tipoPersona as tipoPersona')
            ->select('proveedor.nombre as nombre')
            ->select('proveedor.tipoDocumento as proveedor.tipoDocumento')
            ->select('proveedor.documento as documento')
            ->select('proveedor.direccion as direccion')
            ->select('proveedor.telefono as telefono')
            ->where('proveedor.activo = TRUE')
            ->orderBy('proveedor.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, ProveedorController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('proveedor')
            ->select(['proveedor'])
            ->join('proveedor.config', 'config')
            ;
    }
}
