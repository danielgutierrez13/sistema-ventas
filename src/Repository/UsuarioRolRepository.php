<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Pidia\Apps\Demo\Security\Security;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method UsuarioRol|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioRol|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioRol[]    findAll()
 * @method UsuarioRol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRolRepository extends ServiceEntityRepository implements BaseRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, UsuarioRol::class);
        $this->security = $security;
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
        $queryBuilder = $this->createQueryBuilder('usuarioRol')
            ->select(['usuarioRol', 'config', 'permisos', 'menu'])
            ->leftJoin('usuarioRol.config', 'config')
            ->leftJoin('usuarioRol.permisos', 'permisos')
            ->leftJoin('permisos.menu', 'menu')
        ;

        $this->security->configQuery($queryBuilder);

        Paginator::queryTexts($queryBuilder, $params, ['usuarioRol.nombre']);

        return $queryBuilder;
    }
}
