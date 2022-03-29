<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Service;

use CarlosChininin\App\Domain\Model\Menu\MenuServiceInterface;
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
            $name = mb_strtoupper($menu['padre_nombre'].' - '.$menu['nombre']);
            $data[$name] = $menu['ruta'];
        }

        return $data;
    }
}