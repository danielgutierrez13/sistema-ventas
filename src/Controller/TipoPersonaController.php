<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Pidia\Apps\Demo\Form\TipoPersonaType;
use Pidia\Apps\Demo\Manager\TipoPersonaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tipo/persona')]
class TipoPersonaController extends WebAuthController
{
    public const BASE_ROUTE = 'tipo_persona_index';

    #[Route(path: '/', name: 'tipo_persona_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'tipo_persona_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'tipo_persona/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'tipo_persona_export', methods: ['GET'])]
    public function export(Request $request, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'tipo_persona');
    }

    #[Route(path: '/new', name: 'tipo_persona_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $tipo_persona = new TipoPersona();
        $form = $this->createForm(TipoPersonaType::class, $tipo_persona);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_persona)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('tipo_persona_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'tipo_persona/new.html.twig',
            [
                'tipo_persona' => $tipo_persona,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_persona_show', methods: ['GET'])]
    public function show(TipoPersona $tipo_persona): Response
    {
        $this->denyAccess([Permission::SHOW], $tipo_persona);

        return $this->render('tipo_persona/show.html.twig', ['tipo_persona' => $tipo_persona]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_persona_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoPersona $tipo_persona, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $tipo_persona);

        $form = $this->createForm(TipoPersonaType::class, $tipo_persona);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo_persona)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_persona_index', ['id' => $tipo_persona->getId()]);
        }

        return $this->render(
            'tipo_persona/edit.html.twig',
            [
                'tipo_persona' => $tipo_persona,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_persona_delete', methods: ['POST'])]
    public function state(Request $request, TipoPersona $tipo_persona, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $tipo_persona);

        if ($this->isCsrfTokenValid('delete'.$tipo_persona->getId(), $request->request->get('_token'))) {
            $tipo_persona->changeActivo();
            if ($manager->save($tipo_persona)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_persona_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_persona_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, TipoPersona $tipo_persona, TipoPersonaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $tipo_persona);

        if ($this->isCsrfTokenValid('delete_forever'.$tipo_persona->getId(), $request->request->get('_token'))) {
            if ($manager->remove($tipo_persona)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_persona_index');
    }
}