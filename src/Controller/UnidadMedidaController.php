<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\UnidadMedida;
use Pidia\Apps\Demo\Form\UnidadMedidaType;
use Pidia\Apps\Demo\Manager\UnidadMedidaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/unidad/medida')]
class UnidadMedidaController extends WebAuthController
{
    #[Route(path: '/', name: 'unidad_medida_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'unidad_medida_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);
        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'unidad_medida/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'unidad_medida_export', methods: ['GET'])]
    public function export(Request $request, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'unidad_medida');
    }

    #[Route(path: '/new', name: 'unidad_medida_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $unidad_medida = new UnidadMedida();
        $form = $this->createForm(UnidadMedidaType::class, $unidad_medida);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($unidad_medida)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('unidad_medida_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'unidad_medida/new.html.twig',
            [
                'unidad_medida' => $unidad_medida,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'unidad_medida_show', methods: ['GET'])]
    public function show(UnidadMedida $unidad_medida): Response
    {
        $this->denyAccess([Permission::SHOW], $unidad_medida);

        return $this->render('unidad_medida/show.html.twig', ['unidad_medida' => $unidad_medida]);
    }

    #[Route(path: '/{id}/edit', name: 'unidad_medida_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UnidadMedida $unidad_medida, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $unidad_medida);

        $form = $this->createForm(UnidadMedidaType::class, $unidad_medida);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($unidad_medida)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('unidad_medida_index', ['id' => $unidad_medida->getId()]);
        }

        return $this->render(
            'unidad_medida/edit.html.twig',
            [
                'unidad_medida' => $unidad_medida,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'unidad_medida_delete', methods: ['POST'])]
    public function state(Request $request, UnidadMedida $unidad_medida, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $unidad_medida);

        if ($this->isCsrfTokenValid('delete'.$unidad_medida->getId(), $request->request->get('_token'))) {
            $unidad_medida->changeActivo();
            if ($manager->save($unidad_medida)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('unidad_medida_index');
    }

    #[Route(path: '/{id}/delete', name: 'unidad_medida_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, UnidadMedida $unidad_medida, UnidadMedidaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $unidad_medida);

        if ($this->isCsrfTokenValid('delete_forever'.$unidad_medida->getId(), $request->request->get('_token'))) {
            if ($manager->remove($unidad_medida)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('unidad_medida_index');
    }
}
