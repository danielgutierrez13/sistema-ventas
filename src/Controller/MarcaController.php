<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Marca;
use Pidia\Apps\Demo\Form\MarcaType;
use Pidia\Apps\Demo\Manager\MarcaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/marca')]
class MarcaController extends WebAuthController
{
    public const BASE_ROUTE = 'marca_index';

    #[Route(path: '/', name: 'marca_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'marca_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);
        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'marca/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'marca_export', methods: ['GET'])]
    public function export(Request $request, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'marca');
    }

    #[Route(path: '/new', name: 'marca_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $marca = new Marca();
        $form = $this->createForm(MarcaType::class, $marca);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($marca)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('marca_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'marca/new.html.twig',
            [
                'marca' => $marca,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'marca_show', methods: ['GET'])]
    public function show(Marca $marca): Response
    {
        $this->denyAccess([Permission::SHOW], $marca);

        return $this->render('marca/show.html.twig', ['marca' => $marca]);
    }

    #[Route(path: '/{id}/edit', name: 'marca_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marca $marca, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $marca);

        $form = $this->createForm(MarcaType::class, $marca);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($marca)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('marca_index', ['id' => $marca->getId()]);
        }

        return $this->render(
            'marca/edit.html.twig',
            [
                'marca' => $marca,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'marca_delete', methods: ['POST'])]
    public function state(Request $request, Marca $marca, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $marca);

        if ($this->isCsrfTokenValid('delete'.$marca->getId(), $request->request->get('_token'))) {
            $marca->changeActivo();
            if ($manager->save($marca)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('marca_index');
    }

    #[Route(path: '/{id}/delete', name: 'marca_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Marca $marca, MarcaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $marca);

        if ($this->isCsrfTokenValid('delete_forever'.$marca->getId(), $request->request->get('_token'))) {
            if ($manager->remove($marca)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('marca_index');
    }
}
