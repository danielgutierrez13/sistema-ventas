<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Pidia\Apps\Demo\Controller\TipoPersonaController;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoDocumento|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoDocumento|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoDocumento[]    findAll()
 * @method TipoDocumento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoDocumentoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, TipoDocumento::class);
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
        $queryBuilder = $this->createQueryBuilder('tipoDocumento')
            ->select(['tipoDocumento'])
            ->join('tipoDocumento.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, TipoPersonaController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['tipoDocumento.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('tipoDocumento')
            ->select('tipoDocumento.descripcion as descripcion')
            ->where('tipoDocumento.activo = TRUE')
            ->orderBy('tipoDocumento.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, MenuController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('tipoDocumento')
            ->select(['tipoDocumento'])
            ->join('tipoDocumento.config', 'config')
            ;
    }
}
