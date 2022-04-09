<?php

declare(strict_types=1);


namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Pidia\Apps\Demo\Repository\ConfigRepository;
use Symfony\Component\Security\Core\Security;

final class ConfigManager extends CRUDManager
{
    public function __construct(ConfigRepository $repository, Security $security)
    {
        parent::__construct($repository, $security);
    }
}
