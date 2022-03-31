<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Controller\UsuarioController;
use Pidia\Apps\Demo\Entity\Usuario;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Usuario::class);
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
        $queryBuilder = $this->createQueryBuilder('usuario')
            ->select(['usuario', 'config', 'usuarioRoles'])
            ->leftJoin('usuario.config', 'config')
            ->leftJoin('usuario.usuarioRoles', 'usuarioRoles')
        ;

        $this->security->filterQuery($queryBuilder, UsuarioController::BASE_ROUTE, $permissions);

        if (!$this->security->isSuperAdmin()) {
            $queryBuilder->andWhere('usuario.username <> :usuario_admin')->setParameter('usuario_admin', 'admin');
        }

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['usuario.username']);

        return $queryBuilder;
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('usuario')
            ->select(['usuario', 'config', 'usuarioRoles'])
            ->leftJoin('usuario.config', 'config')
            ->leftJoin('usuario.usuarioRoles', 'usuarioRoles')
            ;
    }
}
