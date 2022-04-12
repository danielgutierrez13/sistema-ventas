<?php

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\ProductoRepository;
use Symfony\Component\Security\Core\Security;

class ProductoManager extends CRUDManager
{
    public function __construct(ProductoRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}