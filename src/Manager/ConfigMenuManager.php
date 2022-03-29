<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Manager;

use Pidia\Apps\Demo\Entity\ConfigMenu;
use CarlosChininin\App\Infrastructure\Repository\BaseRepository;

final class ConfigMenuManager extends BaseManager
{
    public function repositorio(): BaseRepository
    {
        return $this->manager()->getRepository(ConfigMenu::class);
    }

    public function save(object $entity): bool
    {
        $this->manager()->persist($entity);
        $this->manager()->flush();

        return true;
    }
}
