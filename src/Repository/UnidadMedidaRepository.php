<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\UnidadMedidaController;
use Pidia\Apps\Demo\Entity\UnidadMedida;

/**
 * @method UnidadMedida|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnidadMedida|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnidadMedida[]    findAll()
 * @method UnidadMedida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnidadMedidaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, UnidadMedida::class);
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
        $queryBuilder = $this->createQueryBuilder('unidad_medida')
            ->select(['unidad_medida'])
            ->join('unidad_medida.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, UnidadMedidaController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['unidad_medida.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('unidad_medida')
            ->select('unidad_medida.descripcion as descripcion')
            ->where('unidad_medida.activo = TRUE')
            ->orderBy('unidad_medida.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, MenuController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('unidad_medida')
            ->select(['unidad_medida'])
            ->join('unidad_medida.config', 'config')
            ;
    }
}
