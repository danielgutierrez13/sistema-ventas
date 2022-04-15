<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Form\TipoDocumentoType;
use Pidia\Apps\Demo\Manager\TipoDocumentoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tipo/documento')]
class TipoDocumentoController extends WebAuthController
{
    public const BASE_ROUTE = 'tipo_documento_index';

    #[Route(path: '/', name: 'tipo_documento_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'tipo_documento_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'tipo_documento/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'tipo_documento_export', methods: ['GET'])]
    public function export(Request $request, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'tipoPersona.descripcion' => 'Tipo de Persona', // no trae datos
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'tipo_documento');
    }

    #[Route(path: '/new', name: 'tipo_documento_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $tipo_documento = new TipoDocumento();
        $form = $this->createForm(TipoDocumentoType::class, $tipo_documento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_documento)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('tipo_documento_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'tipo_documento/new.html.twig',
            [
                'tipo_documento' => $tipo_documento,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_documento_show', methods: ['GET'])]
    public function show(TipoDocumento $tipo_documento): Response
    {
        $this->denyAccess([Permission::SHOW], $tipo_documento);

        return $this->render('tipo_documento/show.html.twig', ['tipo_documento' => $tipo_documento]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_documento_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoDocumento $tipo_documento, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $tipo_documento);

        $form = $this->createForm(TipoDocumentoType::class, $tipo_documento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_documento)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_documento_index', ['id' => $tipo_documento->getId()]);
        }

        return $this->render(
            'tipo_documento/edit.html.twig',
            [
                'tipo_documento' => $tipo_documento,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_documento_delete', methods: ['POST'])]
    public function state(Request $request, TipoDocumento $tipo_documento, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $tipo_documento);

        if ($this->isCsrfTokenValid('delete'.$tipo_documento->getId(), $request->request->get('_token'))) {
            $tipo_documento->changeActivo();
            if ($manager->save($tipo_documento)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_documento_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_documento_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, TipoDocumento $tipo_documento, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $tipo_documento);

        if ($this->isCsrfTokenValid('delete_forever'.$tipo_documento->getId(), $request->request->get('_token'))) {
            if ($manager->remove($tipo_documento)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_documento_index');
    }
}
