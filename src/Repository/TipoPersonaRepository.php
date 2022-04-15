<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\TipoPersonaController;
use Pidia\Apps\Demo\Entity\TipoPersona;

/**
 * @method TipoPersona|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoPersona|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoPersona[]    findAll()
 * @method TipoPersona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoPersonaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, TipoPersona::class);
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
        $queryBuilder = $this->createQueryBuilder('tipoPersona')
            ->select(['tipoPersona'])
            ->join('tipoPersona.config', 'config')
        ;

        $this->security->filterQuery($queryBuilder, TipoPersonaController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['tipoPersona.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('tipoPersona')
            ->select('tipoPersona.descripcion as descripcion')
            ->where('tipoPersona.activo = TRUE')
            ->orderBy('tipoPersona.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, TipoPersonaController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('tipoPersona')
            ->select(['tipoPersona'])
            ->join('tipoPersona.config', 'config')
            ;
    }
}
