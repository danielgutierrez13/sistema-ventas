<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\DetallePedido;

/**
 * @method DetallePedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetallePedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetallePedido[]    findAll()
 * @method DetallePedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetallePedidoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, DetallePedido::class);
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
        $queryBuilder = $this->createQueryBuilder('detallePedido')
            ->select(['detallePedido', 'producto'])
            ->join('detallePedido.config', 'config')
            ->join('detallePedido.producto', 'producto')
        ;

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['producto.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('detallePedido')
            ->select('detallePedido.producto as producto')
            ->addSelect('detallePedido.cantidad as cantidad')
            ->addSelect('detallePedido.precio as precio')
            ->where('detallePedido.activo = TRUE')
        ;

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('detallePedido')
            ->select(['detallePedido'])
            ->join('detallePedido.config', 'config')
        ;
    }
}
