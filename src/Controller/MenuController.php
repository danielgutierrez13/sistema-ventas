<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Pidia\Apps\Demo\Form\MenuType;
use Pidia\Apps\Demo\Manager\MenuManager;
use Pidia\Apps\Demo\Repository\MenuRepository;
use Pidia\Apps\Demo\Repository\UsuarioRolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route(path: '/admin/menu')]
class MenuController extends WebAuthController
{
    public const BASE_ROUTE = 'menu_index';

    #[Route(path: '/', name: 'menu_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'menu_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, MenuManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'menu/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'menu_export', methods: ['GET'])]
    public function export(Request $request, MenuManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'padre.nombre' => 'Padre',
            'nombre' => 'Nombre',
            'ruta' => 'Ruta',
            'icono' => 'Icono',
            'orden' => 'Orden',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'menu');
    }

    #[Route(path: '/new', name: 'menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::NEW]);

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index');
        }

        return $this->renderForm(
            'menu/new.html.twig',
            [
                'menu' => $menu,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        $this->denyAccess([Permission::SHOW], $menu);

        return $this->render('menu/show.html.twig', ['menu' => $menu]);
    }

    #[Route(path: '/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::EDIT], $menu);

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index', ['id' => $menu->getId()]);
        }

        return $this->render(
            'menu/edit.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'menu_delete', methods: ['POST'])]
    public function state(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $menu);

        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $menu->changeActivo();
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }

    #[Route(path: '/{id}/delete', name: 'menu_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::DELETE], $menu);

        if ($this->isCsrfTokenValid('delete_forever'.$menu->getId(), $request->request->get('_token'))) {
            if ($manager->remove($menu)) {
                $cache->update();
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }
}
