<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Action\Menu;

use CarlosChininin\App\Infrastructure\Controller\WebController;
use CarlosChininin\App\Infrastructure\Security\Menu\MenuBuilder;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BuildMenuAction extends WebController
{
    #[Route('/admin/security_menu/build', name: 'security_menu_build', methods: ['GET'])]
    public function __invoke(string $menuSelected, MenuRepository $menuRepository, MenuBuilder $menuBuilder, MenuCache $menuCache): Response
    {
        $content = $menuCache->menus($menuSelected, function () use ($menuRepository, $menuBuilder, $menuSelected) {
            $menus = $menuRepository->searchAllActiveWithOrder();

            return $this->renderView('@App/theme1/menu/menu.html.twig', [
                'menus' => $menuBuilder->execute($menus, $menuSelected),
                'menuSelected' => $menuSelected,
            ]);
        });

        return new Response($content);
    }
}
