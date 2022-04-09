<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\TipoPersonaRepository;
use Symfony\Component\Security\Core\Security;

class TipoPersonaManager extends CRUDManager
{
    public function __construct(TipoPersonaRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}
