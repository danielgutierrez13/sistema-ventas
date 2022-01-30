<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Security\Security;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository implements BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function findLatest(array $params): Paginator
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function filter(array $params, bool $inArray = true): array
    {
        $queryBuilder = $this->filterQuery($params);

        if (true === $inArray) {
            return $queryBuilder->getQuery()->getArrayResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    private function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('usuario')
            ->select(['usuario', 'config', 'usuarioRoles'])
            ->leftJoin('usuario.config', 'config')
            ->leftJoin('usuario.usuarioRoles', 'usuarioRoles')
        ;

        $this->security->configQuery($queryBuilder);
        if (!$this->security->isSuperAdmin()) {
            $queryBuilder->andWhere('usuario.username <> :usuario_admin')->setParameter('usuario_admin', 'admin');
        }

        Paginator::queryTexts($queryBuilder, $params, ['usuario.username']);

        return $queryBuilder;
    }

    public function findValuesArrayByUsuarioId(int $usuarioId): array
    {
        try {
            return $this->createQueryBuilder('usuario')
                ->select(['usuario', 'config', 'usuarioRoles', 'propietario'])
                ->leftJoin('usuario.config', 'config')
                ->leftJoin('usuario.propietario', 'propietario')
                ->leftJoin('usuario.usuarioRoles', 'usuarioRoles')
                ->where('usuario.activo = true')
                ->andWhere('usuario.id = :usuario_id')
                ->setParameter('usuario_id', $usuarioId)
                ->getQuery()
                ->getOneOrNullResult(Query::HYDRATE_ARRAY);
        } catch (NonUniqueResultException|Exception) {
        }

        return [];
    }
}
