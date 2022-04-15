<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\CompraController;
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
            ->select(['detalleCompra', 'producto'])
            ->join('detalleCompra.config', 'config')
            ->join('detalleCompra.producto', 'producto')
        ;

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['producto.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('detalleCompra')
            ->select('detalleCompra.producto as producto')
            ->addSelect('detalleCompra.cantidad as cantidad')
            ->addSelect('detalleCompra.precio as precio')
            ->where('detalleCompra.activo = TRUE')
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
