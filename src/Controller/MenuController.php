<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Pidia\Apps\Demo\Form\MenuType;
use Pidia\Apps\Demo\Manager\MenuManager;
use Pidia\Apps\Demo\Security\Access;
use Pidia\Apps\Demo\Util\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route(path: '/admin/menu')]
class MenuController extends BaseController
{
    #[Route(path: '/', name: 'menu_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'menu_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, MenuManager $manager): Response
    {
        $this->denyAccess(Access::LIST, 'menu_index');
        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'menu/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'menu_export')]
    public function export(Request $request, MenuManager $manager): Response
    {
        $this->denyAccess(Access::EXPORT, 'menu_index');
        $headers = [
            'padre' => 'Padre',
            'nombre' => 'Nombre',
            'ruta' => 'Ruta',
            'icono' => 'Icono',
            'orden' => 'Orden',
            'activo' => 'Activo',
        ];
        $params = Paginator::params($request->query->all());
        $objetos = $manager->repositorio()->filter($params, false);
        $data = [];
        /** @var Menu $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['padre'] = $objeto->getPadre() ? $objeto->getPadre()->getNombre() : '';
            $item['nombre'] = $objeto->getNombre();
            $item['ruta'] = $objeto->getRuta();
            $item['icono'] = $objeto->getIcono();
            $item['orden'] = $objeto->getOrden();
            $item['activo'] = $objeto->activo();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'Reporte', 'menu');
    }

    #[Route(path: '/new', name: 'menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Access::NEW, 'menu_index');
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setPropietario($this->getUser());
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index');
        }

        return $this->render(
            'menu/new.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        $this->denyAccess(Access::VIEW, 'menu_index');

        return $this->render('menu/show.html.twig', ['menu' => $menu]);
    }

    #[Route(path: '/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Access::EDIT, 'menu_index');
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Registro actualizado!!!');
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
    public function delete(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Access::DELETE, 'menu_index');
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $menu->changeActivo();
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }

    #[Route(path: '/{id}/delete', name: 'menu_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Access::MASTER, 'menu_index', $menu);
        if ($this->isCsrfTokenValid('delete_forever'.$menu->getId(), $request->request->get('_token'))) {
            if ($manager->remove($menu)) {
                $cache->update();
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }

    #[Route(path: '/build', name: 'menu_build')]
    public function build($section, AuthorizationCheckerInterface $authChecker, EntityManagerInterface $entityManager): Response
    {
        $menus = $entityManager->getRepository(Menu::class)->findAllActivo();
        $usuarioMenus = $entityManager->getRepository(UsuarioPermiso::class)
            ->findMenus($this->getUser()->getId());
        $isSuperAdmin = $authChecker->isGranted('ROLE_SUPER_ADMIN');
        $data = [];
        foreach ($menus as $menu) {
            $agregar = false;
            foreach ($usuarioMenus as $usuarioMenu) {
                if ($usuarioMenu['padre_nombre'] === $menu['padre_nombre'] && $usuarioMenu['ruta'] === $menu['ruta']) {
                    $agregar = true;
                    break;
                }
            }
            if (true === $agregar || true === $isSuperAdmin) {
                if (null !== $menu['padre_nombre']) {
                    if (empty($data[$menu['padre_nombre']]['clases']) && $menu['ruta'] === $section) {
                        $data[$menu['padre_nombre']]['clases'] = 'hover show'; //'active';
                    }
                    $data[$menu['padre_nombre']]['menus'][] = $menu;
                } else {
                    $data[$menu['nombre']]['icono'] = $menu['icono'];
                    $data[$menu['nombre']]['ruta'] = $menu['ruta'];
                }
            }
        }

        return $this->render('menu/build.html.twig', [
            '_section' => $section,
            '_data' => $data,
        ]);
    }
}
