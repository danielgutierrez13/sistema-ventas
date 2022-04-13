<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\PedidoRepository;
use Symfony\Component\Security\Core\Security;

class PedidoManager extends CRUDManager
{
    public function __construct(PedidoRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}