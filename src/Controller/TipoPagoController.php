<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\TipoPago;
use Pidia\Apps\Demo\Form\TipoPagoType;
use Pidia\Apps\Demo\Manager\TipoPagoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tipo/pago')]
class TipoPagoController extends WebAuthController
{
    public const BASE_ROUTE = 'tipo_pago_index';

    #[Route(path: '/', name: 'tipo_pago_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'tipo_pago_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'tipo_pago/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'tipo_pago_export', methods: ['GET'])]
    public function export(Request $request, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'nombreCorto' => 'Nombre Corto',
            'cuenta' => 'N° Cuenta',
            'propietarioCuenta' => 'Nombre Cuenta',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'tipo_pago');
    }

    #[Route(path: '/new', name: 'tipo_pago_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $tipo_pago = new TipoPago();
        $form = $this->createForm(TipoPagoType::class, $tipo_pago);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_pago)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('tipo_pago_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'tipo_pago/new.html.twig',
            [
                'tipo_pago' => $tipo_pago,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_pago_show', methods: ['GET'])]
    public function show(TipoPago $tipo_pago): Response
    {
        $this->denyAccess([Permission::SHOW], $tipo_pago);

        return $this->render('tipo_pago/show.html.twig', ['tipo_pago' => $tipo_pago]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_pago_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoPago $tipo_pago, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $tipo_pago);

        $form = $this->createForm(TipoPagoType::class, $tipo_pago);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_pago)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_pago_index', ['id' => $tipo_pago->getId()]);
        }

        return $this->render(
            'tipo_pago/edit.html.twig',
            [
                'tipo_pago' => $tipo_pago,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_pago_delete', methods: ['POST'])]
    public function state(Request $request, TipoPago $tipo_pago, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $tipo_pago);

        if ($this->isCsrfTokenValid('delete'.$tipo_pago->getId(), $request->request->get('_token'))) {
            $tipo_pago->changeActivo();
            if ($manager->save($tipo_pago)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_pago_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_pago_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, TipoPago $tipo_pago, TipoPagoManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $tipo_pago);

        if ($this->isCsrfTokenValid('delete_forever'.$tipo_pago->getId(), $request->request->get('_token'))) {
            if ($manager->remove($tipo_pago)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_pago_index');
    }
}
