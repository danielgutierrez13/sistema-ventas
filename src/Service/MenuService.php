<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Service;

use CarlosChininin\App\Domain\Model\AuthMenu\MenuServiceInterface;
use Pidia\Apps\Demo\Repository\MenuRepository;

final class MenuService implements MenuServiceInterface
{
    public function __construct(private MenuRepository $menuRepository)
    {
    }

    public function menusToArray(): array
    {
        $menus = $this->menuRepository->allForMenus();
        $data = [];
        foreach ($menus as $menu) {
            $name = mb_strtoupper($menu['parentName'].' - '.$menu['name']);
            $data[$name] = $menu['route'];
        }

        return $data;
    }
}