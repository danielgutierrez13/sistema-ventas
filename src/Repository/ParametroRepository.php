<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function is_array;
use Pidia\Apps\Demo\Entity\Parametro;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method Parametro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parametro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parametro[]    findAll()
 * @method Parametro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametroRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametro::class);
    }

    public function findLatest(array $params): Paginator
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
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
        $queryBuilder = $this->createQueryBuilder('parametro')
            ->select(['parametro', 'padre'])
            ->leftJoin('parametro.padre', 'padre')
        ;

        Paginator::queryTexts($queryBuilder, $params, ['parametro.nombre', 'padre.nombre']);

        return $queryBuilder;
    }

    /**
     * @return Parametro[] Returns an array of Acopio objects
     */
    public function findData(?int $length = null, ?int $start = null, $search = null, $columns = null, $order = null)
    {
        $qb = $this->createQueryBuilder('parametro');

        $query = $qb
            ->select(['parametro', 'padre'])
            ->leftJoin('parametro.padre', 'padre')
            ->where('parametro.activo = true')
            // ->setParameter('val', $value)
            // ->orderBy('a.id', 'ASC')
        ;

        if (null !== $length) {
            $query = $query->setMaxResults($length);
        }

        if (null !== $start) {
            $query = $query->setFirstResult($start);
        }

        if (null !== $search && '' !== $search['value']) {
            $orX = $qb->expr()->orX();
            $orX->add($qb->expr()->like('parametro.nombre', $qb->expr()->literal('%'.$search['value'].'%')));
            $orX->add($qb->expr()->like('padre.nombre', $qb->expr()->literal('%'.$search['value'].'%')));
            $query = $query->andWhere($orX);
        }

        if (null !== $order && is_array($order) && is_array($order[0])) {
            if ('4' !== $order[0]['column']) {
                $query = $query->addOrderBy('parametro.'.$columns[(int) ($order[0]['column'])]['data'], $order[0]['dir']);
            } else {
                $query = $query->addOrderBy('padre.nombre', $order[0]['dir']);
            }
        }

        if (null !== $columns && is_array($columns)) {
            $andX = $qb->expr()->andX();
            foreach ($columns as $column) {
                if ('' !== $column['search']['value']) {
                    $value = $qb->expr()->literal($column['search']['value']);
                    switch ($column['data']) {
                        case 'id':
                            $andX->add($qb->expr()->eq('parametro.id', $value));
                            break;
                        case 'nombre':
                            $andX->add($qb->expr()->like('parametro.nombre', $qb->expr()->literal('%'.$column['search']['value'].'%')));
                            break;
                        case 'padre':
                            $andX->add($qb->expr()->eq('padre.id', $value));
                            break;
                        case 'activo':
                            $andX->add($qb->expr()->eq('parametro.activo', $value));
                            break;
                    }
                }
            }
            $query = $query->andWhere($andX);
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByPadreAndAlias(string $padre_alias, string $alias): ?Parametro
    {
        // try{
        return $this->createQueryBuilder('parametro')
                ->join('parametro.padre', 'padre')
                ->where('parametro.activo = TRUE')
                ->andWhere('parametro.alias = :alias')
                ->andWhere('padre.alias = :padre_alias')
                ->setParameter('alias', $alias)
                ->setParameter('padre_alias', $padre_alias)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        /*}
        catch (NonUniqueResultException $e){
            return null;
        }*/
    }

    public function findByPadreAlias(string $padreAlias, bool $inArray = false): array
    {
        if (true === $inArray) {
            return $this->queryPadreAlias($padreAlias)->getQuery()->getArrayResult();
        }

        return $this->queryPadreAlias($padreAlias)->getQuery()->getResult();
    }

    public function queryPadreAlias(string $padreAlias): QueryBuilder
    {
        return $this->createQueryBuilder('parametro')
            ->select(['parametro', 'padre'])
            ->join('parametro.padre', 'padre')
            ->where('parametro.activo = TRUE')
            ->andWhere('padre.alias = :padre_alias')
            ->setParameter('padre_alias', $padreAlias)
            ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByAlias(string $alias, ?string $padreAlias = null): ?Parametro
    {
        $query = $this->createQueryBuilder('parametro')
            ->select('parametro')
            ->where('parametro.activo = TRUE')
            ->andWhere('parametro.alias = :alias')
            ->setParameter('alias', $alias)

            ;

        if (null !== $padreAlias && '' !== $padreAlias) {
            $query = $query
                ->join('parametro.padre', 'padre')
                ->andWhere('padre.alias = :padre_alias')
                ->setParameter('padre_alias', $padreAlias)
                ;
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('parametro')
            ->select(['parametro', 'padre'])
            ->leftJoin('parametro.padre', 'padre')
            ;
    }
}
