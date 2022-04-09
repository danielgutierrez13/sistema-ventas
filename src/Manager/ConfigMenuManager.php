<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\ConfigMenuRepository;
use Symfony\Component\Security\Core\Security;

final class ConfigMenuManager extends CRUDManager
{
    public function __construct(ConfigMenuRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}
