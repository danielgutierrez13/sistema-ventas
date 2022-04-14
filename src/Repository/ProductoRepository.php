<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\ProductoController;
use Pidia\Apps\Demo\Entity\Producto;

/**
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Producto::class);
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
        $queryBuilder = $this->createQueryBuilder('producto')
            ->select(['producto', 'categoria', 'marca'])
            ->join('producto.config', 'config')
            ->join('producto.categoria', 'categoria')
            ->join('producto.marca', 'marca')
        ;

        $this->security->filterQuery($queryBuilder, ProductoController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['producto.descripcion', 'categoria.descripcion',
            'marca.descripcion', ]);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('producto')
            ->select('producto.descripcion as descripcion')
            ->select('producto.precio as precio')
            ->select('producto.stock as stock')
            ->select('producto.categoria as categoria')
            ->select('producto.marca as marca')
            ->select('producto.unidadMedida as unidadMedida')
            ->select('producto.precioVenta as precioVenta')
            ->select('producto.codigo as codigo')
            ->where('producto.activo = TRUE')
            ->orderBy('producto.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, ProductoController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('producto')
            ->select(['producto'])
            ->join('producto.config', 'config')
            ;
    }

    public function buscarProductoById(int $productoId): ?Producto
    {
        try {
            return $this->createQueryBuilder('producto')
                ->where('producto.id = :productoId')
                ->setParameter('productoId', $productoId)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }

        return null;
    }
}
