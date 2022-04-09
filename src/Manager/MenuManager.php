<?php

declare(strict_types=1);


namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\MenuRepository;
use Symfony\Component\Security\Core\Security;

final class MenuManager extends CRUDManager
{
    public function __construct(MenuRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}
