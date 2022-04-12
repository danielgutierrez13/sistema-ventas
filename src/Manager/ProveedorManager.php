<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\ProveedorRepository;
use Symfony\Component\Security\Core\Security;

class ProveedorManager extends CRUDManager
{
    public function __construct(ProveedorRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}