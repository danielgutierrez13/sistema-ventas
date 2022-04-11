<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\VendedorRepository;
use Symfony\Component\Security\Core\Security;

class VendedorManager extends CRUDManager
{
    public function __construct(VendedorRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}