<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Action\Menu;

use CarlosChininin\App\Infrastructure\Controller\WebController;
use CarlosChininin\App\Infrastructure\Security\MenuPermission;
use CarlosChininin\App\Infrastructure\Security\Security;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Pidia\Apps\Demo\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BuildMenuAction extends WebController
{
    public const SUBLEVEL = 'submenu';

    #[Route('/admin/security_menu/build', name: 'security_menu_build', methods: ['GET'])]
    public function __invoke(string $menuSelected, MenuRepository $menuRepository, Security $security): Response
    {
        $menus = $menuRepository->searchAllActiveWithOrder();
        $userMenus = $security->isSuperAdmin() ? $this->allMenupaths($menus) : $this->userMenuPaths();
        $buildMenus = $this->buildMenus($menus, $userMenus);
        $this->selectedMenus($buildMenus, $menuSelected);

        return $this->render('menu/menu.html.twig', [
            'menus' => $buildMenus,
            'menuSelected' => $menuSelected,
        ]);
    }

    /** @param Menu[] $menus */
    public function allMenupaths(array $menus): array
    {
        $menuPaths = [];
        foreach ($menus as $menu) {
            if (null !== $menu->getPadre()) {
                $menuPaths[] = $menu->getRuta();
            }
        }

        return $menuPaths;
    }

    private function userMenuPaths(): array
    {
        /** @var Usuario $user */
        $user = $this->getUser();

        $menuPaths = [];
        /** @var UsuarioRol $authRole */
        foreach ($user->getUsuarioRoles() as $authRole) {
            /** @var MenuPermission $permission */
            foreach ($authRole->getPermissions() as $permission) {
                $menuPaths[] = $permission->menu();
            }
        }

        return $menuPaths;
    }

    /** @param Menu[] $menus */
    private function buildMenus(array $menus, array $userPaths): array
    {
        $sections = [];
        foreach ($userPaths as $userPath) {
            $newMenus = $this->menuParents($menus, $userPath);
            $level = null;
            $menuLevel = [];
            foreach ($newMenus as $newMenu) {
                if (null === $level) {
                    $level = [];
                    $menuLevel = &$level;
                }

                $level[$newMenu['nombre']] = $newMenu;
                $level[$newMenu['nombre']][self::SUBLEVEL] = [];
                $level = &$level[$newMenu['nombre']][self::SUBLEVEL];
            }

            $this->mergeMenus($sections, $menuLevel);
            unset($level);
        }

        return $sections;
    }

    /** @param Menu[] $menus */
    private function menuParents(array $menus, string $path): array
    {
        $parents = [];
        foreach ($menus as $menu) {
            if ($menu->getRuta() === $path) {
                $this->getParents($menus, $menu->getId(), $parents);

                return $parents;
            }
        }

        return $parents;
    }

    /** @param Menu[] $menus */
    private function getParents(array $menus, int $menuId, array &$parents): void
    {
        foreach ($menus as $menu) {
            if ($menu->getId() === $menuId) {
                $parentId = $menu->getPadre()?->getId();
                if (null !== $parentId) {
                    $this->getParents($menus, $parentId, $parents);
                }
                $parents[] = $menu->toArray();

                break;
            }
        }
    }

    private function mergeMenus(array &$sections, array &$menuLevel): void
    {
        if (0 === count($sections) && 0 !== count($menuLevel)) {
            $sections = $menuLevel;

            return;
        }

        foreach ($sections as $key => $section) {
            if (isset($menuLevel[$key])) {
                // $sections[$key] es el original section
                $this->mergeMenus($sections[$key][self::SUBLEVEL], $menuLevel[$key][self::SUBLEVEL]);

                return;
            }
        }

        $sections = array_merge($sections, $menuLevel);
    }

    private function selectedMenus(array &$menus, string $menuSelected): bool
    {
        foreach ($menus as $key => $menu) {
            // $menus[$key] es el original menu
            if ($menu['ruta'] === $menuSelected || $this->selectedMenus($menus[$key][self::SUBLEVEL], $menuSelected)) {
                $menus[$key]['selected'] = true;

                return true;
            }
        }

        return false;
    }
}