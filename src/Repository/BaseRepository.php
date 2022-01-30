<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Pidia\Apps\Demo\Util\Paginator;

interface BaseRepository
{
    public function findLatest(array $params): Paginator;

    public function filter(array $params, bool $inArray = true): array;
}
