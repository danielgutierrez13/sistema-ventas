<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\TipoMonedaRepository;
use Symfony\Component\Security\Core\Security;

class TipoMonedaManager extends CRUDManager
{
    public function __construct(TipoMonedaRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}