<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\TipoPagoRepository;
use Symfony\Component\Security\Core\Security;

class TipoPagoManager extends CRUDManager
{
    public function __construct(TipoPagoRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}