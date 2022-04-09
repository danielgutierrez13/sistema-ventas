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
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Pidia\Apps\Demo\Form\UsuarioRolType;
use Pidia\Apps\Demo\Manager\UsuarioRolManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/usuario_rol')]
class UsuarioRolController extends WebAuthController
{
    public const BASE_ROUTE = 'usuario_rol_index';

    #[Route(path: '/', name: 'usuario_rol_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'usuario_rol_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, UsuarioRolManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'usuario_rol/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'usuario_rol_export', methods: ['GET'])]
    public function export(Request $request, UsuarioRolManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'nombre' => 'Nombre',
            'rol' => 'Alias',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'usuario_rol');
    }

    #[Route(path: '/new', name: 'usuario_rol_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsuarioRolManager $manager, MenuCache $menuCache): Response
    {
        $this->denyAccess([Permission::NEW]);

        $rol = new UsuarioRol();
        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }
            $menuCache->update();

            return $this->redirectToRoute('usuario_rol_index');
        }

        return $this->render(
            'usuario_rol/new.html.twig',
            [
                'usuario_rol' => $rol,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'usuario_rol_show', methods: ['GET'])]
    public function show(UsuarioRol $rol): Response
    {
        $this->denyAccess([Permission::SHOW], $rol);

        return $this->render('usuario_rol/show.html.twig', ['usuario_rol' => $rol]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_rol_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsuarioRol $rol, UsuarioRolManager $manager, MenuCache $menuCache, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::EDIT], $rol);

        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }
            $menuCache->update();

            return $this->redirectToRoute('usuario_rol_index', ['id' => $rol->getId()]);
        }

        return $this->render(
            'usuario_rol/edit.html.twig',
            [
                'usuario_rol' => $rol,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'usuario_rol_delete', methods: ['POST'])]
    public function state(Request $request, UsuarioRol $rol, UsuarioRolManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $rol);

        if ($this->isCsrfTokenValid('delete'.$rol->getId(), $request->request->get('_token'))) {
            $rol->changeActivo();
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_rol_index');
    }

    #[Route(path: '/{id}/delete', name: 'usuario_rol_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, UsuarioRol $rol, UsuarioRolManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $rol);

        if ($this->isCsrfTokenValid('delete_forever'.$rol->getId(), $request->request->get('_token'))) {
            if ($manager->remove($rol)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_rol_index');
    }
}
