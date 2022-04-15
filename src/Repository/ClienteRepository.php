<?php

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\ClienteController;
use Pidia\Apps\Demo\Entity\Cliente;


/**
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[]    findAll()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Cliente::class);
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
        $queryBuilder = $this->createQueryBuilder('cliente')
            ->select(['cliente', 'tipoPersona', 'tipoDocumento'])
            ->join('cliente.config', 'config')
            ->join('cliente.tipoPersona', 'tipoPersona')
            ->join('cliente.tipoDocumento', 'tipoDocumento')
        ;

        $this->security->filterQuery($queryBuilder, ClienteController::BASE_ROUTE, $permissions);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['cliente.nombre', 'cliente.documento', 'cliente.direccion', 'tipoPersona.descripcion',  'tipoDocumento.descripcion']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('cliente')
            ->select('cliente.tipoPersona as tipoPersona')
            ->addSelect('cliente.nombre as nombre')
            ->addSelect('cliente.tipoDocumento as tipoDocumento')
            ->addSelect('cliente.documento as documento')
            ->addSelect('cliente.direccion as direccion')
            ->addSelect('cliente.telefono as telefono')
            ->where('cliente.activo = TRUE')
            ->orderBy('cliente.descripcion', 'ASC')
        ;

        $this->security->filterQuery($queryBuilder, ClienteController::BASE_ROUTE);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('cliente')
            ->select(['cliente'])
            ->join('cliente.config', 'config')
            ;
    }
}
