<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Pidia\Apps\Demo\Form\UsuarioRolType;
use Pidia\Apps\Demo\Manager\UsuarioRolManager;
use Pidia\Apps\Demo\Security\Access;
use Pidia\Apps\Demo\Util\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/usuario_rol')]
class UsuarioRolController extends BaseController
{
    #[Route(path: '/', name: 'usuario_rol_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'usuario_rol_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Access::LIST, 'usuario_rol_index');
        $paginator = $manager->list($request->query->all(), $page);

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
        $this->denyAccess(Access::EXPORT, 'usuario_rol_index');
        $headers = [
            'nombre' => 'Nombre',
            'rol' => 'Alias',
            'activo' => 'Activo',
        ];

        return $manager->exportOfQuery($request->query->all(), $headers, 'Reporte');
    }

    #[Route(path: '/new', name: 'usuario_rol_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsuarioRolManager $manager, MenuCache $menuCache): Response
    {
        $this->denyAccess(Access::NEW, 'usuario_rol_index');
        $rol = new UsuarioRol();
        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rol->setPropietario($this->getUser());
            $configId = $this->getUser()->config()->getId();
            if (null === $rol->getRol()) {
                $rol->setRol(Generator::createRol($rol->getNombre(), $configId));
            }
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
        $this->denyAccess(Access::VIEW, 'usuario_rol_index');

        return $this->render('usuario_rol/show.html.twig', ['usuario_rol' => $rol]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_rol_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsuarioRol $rol, UsuarioRolManager $manager, MenuCache $menuCache): Response
    {
        $this->denyAccess(Access::EDIT, 'usuario_rol_index');
        $originalPermisos = new ArrayCollection();
        foreach ($rol->getPermisos() as $permiso) {
            $originalPermisos->add($permiso);
        }
        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            foreach ($originalPermisos as $permiso) {
                if (false === $rol->getPermisos()->contains($permiso)) {
                    $em->remove($permiso);
                }
            }

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
    public function delete(Request $request, UsuarioRol $rol, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Access::DELETE, 'usuario_rol_index');
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
        $this->denyAccess(Access::MASTER, 'usuario_rol_index', $rol);
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
