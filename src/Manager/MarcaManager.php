<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\MarcaRepository;
use Symfony\Component\Security\Core\Security;

class MarcaManager extends CRUDManager
{
    public function __construct(MarcaRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}