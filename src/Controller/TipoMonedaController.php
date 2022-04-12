<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\TipoMoneda;
use Pidia\Apps\Demo\Form\TipoMonedaType;
use Pidia\Apps\Demo\Manager\TipoMonedaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tipo/moneda')]
class TipoMonedaController extends WebAuthController
{
    public const BASE_ROUTE = 'tipo_moneda_index';

    #[Route(path: '/', name: 'tipo_moneda_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'tipo_moneda_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'tipo_moneda/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'tipo_moneda_export', methods: ['GET'])]
    public function export(Request $request, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'tipo_moneda');
    }

    #[Route(path: '/new', name: 'tipo_moneda_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $tipo_moneda = new TipoMoneda();
        $form = $this->createForm(TipoMonedaType::class, $tipo_moneda);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_moneda)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('tipo_moneda_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'tipo_moneda/new.html.twig',
            [
                'tipo_moneda' => $tipo_moneda,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_moneda_show', methods: ['GET'])]
    public function show(TipoMoneda $tipo_moneda): Response
    {
        $this->denyAccess([Permission::SHOW], $tipo_moneda);

        return $this->render('tipo_moneda/show.html.twig', ['tipo_moneda' => $tipo_moneda]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_moneda_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoMoneda $tipo_moneda, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $tipo_moneda);

        $form = $this->createForm(TipoMonedaType::class, $tipo_moneda);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_moneda)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_moneda_index', ['id' => $tipo_moneda->getId()]);
        }

        return $this->render(
            'tipo_moneda/edit.html.twig',
            [
                'tipo_moneda' => $tipo_moneda,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_moneda_delete', methods: ['POST'])]
    public function state(Request $request, TipoMoneda $tipo_moneda, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $tipo_moneda);

        if ($this->isCsrfTokenValid('delete'.$tipo_moneda->getId(), $request->request->get('_token'))) {
            $tipo_moneda->changeActivo();
            if ($manager->save($tipo_moneda)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_moneda_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_moneda_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, TipoMoneda $tipo_moneda, TipoMonedaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $tipo_moneda);

        if ($this->isCsrfTokenValid('delete_forever'.$tipo_moneda->getId(), $request->request->get('_token'))) {
            if ($manager->remove($tipo_moneda)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_moneda_index');
    }
}
