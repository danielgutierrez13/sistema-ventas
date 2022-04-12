<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\CompraRepository;
use Symfony\Component\Security\Core\Security;

class CompraManager extends CRUDManager
{
    public function __construct(CompraRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}