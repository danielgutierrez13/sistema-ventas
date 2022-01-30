<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Pidia\Apps\Demo\Security\Security;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class MenuRuntime implements RuntimeExtensionInterface
{
    private $entityManager;
    private $permisos;
    private $menus;
    private $cache;
    private $router;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security, MenuCache $cache, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->cache = $cache;
        $this->router = $router;
    }

    public function buildMenu(string $section): array
    {
        if (!$this->security->isAuthenticate()) {
            return [];
        }

        return $this->build($section);
    }

    private function permisos(): array
    {
        if (null === $this->permisos) {
            $this->permisos = $this->cache->permisos(function () {
                $user = $this->security->user();

                return $this->entityManager->getRepository(UsuarioPermiso::class)->findMenus($user['id']);
            });
        }

        return $this->permisos;
    }

    private function menus(): array
    {
        if (null === $this->menus) {
            $this->menus = $this->cache->menus(function () {
                return $this->entityManager->getRepository(Menu::class)->findAllActivo();
            });
        }

        return $this->menus;
    }

    private function isAnonymousPage(?string $currentRoute): bool
    {
        return null !== $currentRoute && 'homepage' === $currentRoute;
    }

    private function build(string $section): array
    {
        $class = 'open';
        $isVisible = $this->security->isSuperAdmin() || $this->security->isAdmin();
        $permisos = $this->permisos();
        $menus = $this->menus();
        $padre = [];
        $data = [];
        foreach ($menus as $menu) {
            $agregar = false;
            foreach ($permisos as $usuarioMenu) {
                if (null === $menu['padre_nombre']) {
                    $padre[$menu['nombre']] = $menu;
                }
                if ($usuarioMenu['padre_nombre'] === $menu['padre_nombre'] && $usuarioMenu['ruta'] === $menu['ruta']) {
                    $agregar = true;
                    break;
                }
            }
            if ($this->isValidRouter($menu['ruta']) && (true === $agregar || $this->isAnonymousPage($menu['ruta']) || true === $isVisible)) {
                if (null !== $menu['padre_nombre']) {
                    if (empty($data[$menu['padre_nombre']]['clases']) && $menu['ruta'] === $section) {
                        $data[$menu['padre_nombre']]['clases'] = $class; //'active';
                    }
                    if (empty($data[$menu['padre_nombre']]['icono'])) {
                        $data[$menu['padre_nombre']]['icono'] = isset($padre[$menu['padre_nombre']]) ? $padre[$menu['padre_nombre']]['icono'] : null;
                    }
                    $data[$menu['padre_nombre']]['menus'][] = $menu;
                } elseif (true === $isVisible) {
                    if (null === $menu['padre_nombre']) {
                        $padre[$menu['nombre']] = $menu;
                    }
                }
            }
        }

        return $data;
    }

    private function isValidRouter(?string $routeName): bool
    {
        if (null === $routeName) {
            return true;
        }

        try {
            $this->router->generate($routeName);
        } catch (RouteNotFoundException $e) {
            return false;
        }

        return true;
    }
}
