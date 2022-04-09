<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\TipoDocumentoRepository;
use Symfony\Component\Security\Core\Security;

class TipoDocumentoManager extends CRUDManager
{
    public function __construct(TipoDocumentoRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}