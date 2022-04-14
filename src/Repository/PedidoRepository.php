<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\PedidoController;
use Pidia\Apps\Demo\Controller\VendedorController;
use Pidia\Apps\Demo\Entity\Pedido;

/**
 * @method Pedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pedido[]    findAll()
 * @method Pedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Pedido::class);
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
        $queryBuilder = $this->createQueryBuilder('vendedor')
            ->select(['vendedor'])
            ->join('vendedor.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, PedidoController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['pedido.codigo']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('pedido')
        ;

        $this->security->filterQuery($queryBuilder, PedidoController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('pedido')
            ->select(['pedido'])
            ->join('pedido.config', 'config')
            ;
    }

    public function BuscarPedidoId(int $id): ?Pedido
    {
        try {
            return $this->createQueryBuilder('pedido')
                ->select(['pedido', 'config'])
                ->join('pedido.config', 'config')
                ->where('pedido.id = :pedidoId')
                ->setParameter('pedidoId', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }

        return null;
    }
}
