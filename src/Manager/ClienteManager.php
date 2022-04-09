<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\ClienteRepository;
use Symfony\Component\Security\Core\Security;

class ClienteManager extends CRUDManager
{
    public function __construct(ClienteRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}