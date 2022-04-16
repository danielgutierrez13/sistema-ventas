<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\PagoRepository;
use Symfony\Component\Security\Core\Security;

class PagoManager extends CRUDManager
{
    public function __construct(PagoRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}