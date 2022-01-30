<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Pidia\Apps\Demo\Util\Paginator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class PaginatorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('index', [$this, 'indexFilter']),
            new TwigFilter('indexReverse', [$this, 'indexReverseFilter']),
            new TwigFilter('indexCountReverse', [$this, 'indexCountReverseFilter']),
        ];
    }

    public function indexFilter(int $index, Paginator $paginator): int
    {
        return $index + ($paginator->getCurrentPage() - 1) * $paginator->getPageSize();
    }

    public function indexReverseFilter(int $index, Paginator $paginator): int
    {
        return ($paginator->getNumResults() + 1) - ($index + ($paginator->getCurrentPage() - 1) * $paginator->getPageSize());
    }

    public function indexCountReverseFilter(int $index, int $total): int
    {
        return ($total + 1) - $index;
    }
}
