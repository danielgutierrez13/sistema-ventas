<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\UnidadMedidaRepository;
use Symfony\Component\Security\Core\Security;

class UnidadMedidaManager extends CRUDManager
{
    public function __construct(UnidadMedidaRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}
