<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Security\Security;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ConfigMenuRuntime implements RuntimeExtensionInterface
{
    private $entityManager;
    private $menus;
    private $router;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->router = $router;
    }

    public function buildMenu(string $section): array
    {
        if (!$this->security->isAuthenticate()) {
            return [];
        }

        return $this->build($section);
    }

    private function menus(): array
    {
        if (null === $this->menus) {
            $config = $this->security->config();
            $this->menus = $this->entityManager->getRepository(Config::class)->findMenusByConfigId($config['id']);
        }

        return $this->menus;
    }

    private function build(string $section): array
    {
        $isSuperAdmin = $this->security->isSuperAdmin();
        $isAdmin = $this->security->isAdmin();
        $menus = $this->menus();
        $data = [];

        if (false === $isSuperAdmin && false === $isAdmin) {
            return [];
        }

        $class = '';
        foreach ($menus as $menu) {
            if (false === $this->isValidRouter($menu['route'])) {
                continue;
            }

            if ($menu['route'] === $section) {
                $class = 'open';
            }
            $data[] = array_merge($menu, ['active' => $menu['route'] === $section]);
        }

        return ['items' => $data, 'class' => $class];
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
