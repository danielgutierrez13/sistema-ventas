<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Pidia\Apps\Demo\Controller\VendedorController;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\Vendedor;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vendedor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vendedor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vendedor[]    findAll()
 * @method Vendedor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendedorRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Vendedor::class);
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

        $this->security->filterQuery($queryBuilder, VendedorController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['vendedor.nombre', 'vendedor.documento']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('vendedor')
            ->select('vendedor.nombre as nombre')
            ->select('vendedor.tipoDocumento as vendedor.tipoDocumento')
            ->select('vendedor.documento as documento')
            ->select('vendedor.direccion as direccion')
            ->select('vendedor.telefono as telefono')
            ->where('vendedor.activo = TRUE')
            ->orderBy('vendedor.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, VendedorController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('vendedor')
            ->select(['vendedor'])
            ->join('vendedor.config', 'config')
            ;
    }

    public function buscarUsuarioVendedor(int $documento): ?Vendedor
    {
        try {
            return $this->createQueryBuilder('vendedor')
                ->where('vendedor.documento = :documento')
                ->setParameter('documento', $documento)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }

        return null;
    }
}

