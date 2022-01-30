<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use Pidia\Apps\Demo\Entity\Parametro;
use Pidia\Apps\Demo\Form\ParametroType;
use Pidia\Apps\Demo\Manager\ParametroManager;
use Pidia\Apps\Demo\Security\Access;
use Pidia\Apps\Demo\Util\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/parametro')]
class ParametroController extends BaseController
{
    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'parametro_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'parametro_index_paginated')]
    public function index(Request $request, int $page, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::LIST, 'parametro_index');
        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'parametro/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'parametro_export')]
    public function export(Request $request, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::EXPORT, 'parametro_index');
        $headers = [
            'nombre' => 'Nombre',
            'codigo' => 'Codigo',
            'activo' => 'Activo',
        ];
        $params = Paginator::params($request->query->all());
        $objetos = $manager->repositorio()->filter($params, false);
        $data = [];
        /** @var Parametro $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['nombre'] = $objeto->getNombreCompleto();
            $item['codigo'] = $objeto->getCodigo();
            $item['activo'] = $objeto->activo();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'Reporte', 'parametro');
    }

    #[Route(path: '/new', name: 'parametro_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::NEW, 'parametro_index');
        $parametro = new Parametro();
        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $parametro->setPropietario($this->getUser());
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('parametro_index');
        }

        return $this->render(
            'parametro/new.html.twig',
            [
                'parametro' => $parametro,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'parametro_show', methods: ['GET'])]
    public function show(Parametro $parametro): Response
    {
        $this->denyAccess(Access::VIEW, 'parametro_index');

        return $this->render('parametro/show.html.twig', ['parametro' => $parametro]);
    }

    #[Route(path: '/{id}/edit', name: 'parametro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::EDIT, 'parametro_index');
        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('parametro_index', ['id' => $parametro->getId()]);
        }

        return $this->render(
            'parametro/edit.html.twig',
            [
                'parametro' => $parametro,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'parametro_delete', methods: ['POST'])]
    public function delete(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::DELETE, 'parametro_index');
        if ($this->isCsrfTokenValid('delete'.$parametro->getId(), $request->request->get('_token'))) {
            $parametro->changeActivo();
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('parametro_index');
    }

    #[Route(path: '/{id}/delete', name: 'parametro_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess(Access::MASTER, 'parametro_index', $parametro);
        if ($this->isCsrfTokenValid('delete_forever'.$parametro->getId(), $request->request->get('_token'))) {
            if ($manager->remove($parametro)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('parametro_index');
    }
}
