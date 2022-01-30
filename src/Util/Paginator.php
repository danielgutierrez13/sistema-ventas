<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Util;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Traversable;

final class Paginator
{
    public const PAGE_SIZE = 10;

    private $currentPage;
    private $pageSize;
    private $results;
    private $numResults;

    public function __construct(Traversable $results, int $numResults, int $currentPage, int $pageSize)
    {
        $this->results = $results;
        $this->numResults = $numResults;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
    }

    public static function create(QueryBuilder $queryBuilder, array $params): self
    {
        $currentPage = max(1, $params['page']);
        $firstResult = ($currentPage - 1) * $params['size_page'];

        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($params['size_page'])
            ->getQuery();

        if (0 === \count($queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new DoctrinePaginator($query, true);

        $useOutputWalkers = \count($queryBuilder->getDQLPart('having') ?: []) > 0;
        $paginator->setUseOutputWalkers($useOutputWalkers);

        $results = $paginator->getIterator();
        $numResults = $paginator->count();

        return new self($results, $numResults, $currentPage, $params['size_page']);
    }

    public static function params(array $values, int $page = 1): array
    {
        return [
            'page' => $page,
            'size_page' => (int) (isset($values['n']) && $values['n'] > 0) ? $values['n'] : self::PAGE_SIZE,
            'searching' => $values['b'] ?? '',
        ];
    }

    public static function queryTexts(QueryBuilder $qb, array $params, array $fields): void
    {
        $searching = $params['searching'] ?? '';
        if ('' !== trim($searching)) {
            $texts = explode(' ', trim($searching));
            foreach ($texts as $t) {
                if ('' !== $t) {
                    $orX = $qb->expr()->orX();
                    foreach ($fields as $field) {
                        $orX->add($qb->expr()->like($field, $qb->expr()->literal('%'.$t.'%')));
                    }

                    $qb = $qb->andWhere($orX);
                }
            }
        }
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getResults(): Traversable
    {
        return $this->results;
    }
}
