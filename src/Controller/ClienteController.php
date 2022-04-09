<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Cliente;
use Pidia\Apps\Demo\Form\ClienteType;
use Pidia\Apps\Demo\Manager\ClienteManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cliente')]
class ClienteController extends WebAuthController
{
    public const BASE_ROUTE = 'cliente_index';

    #[Route(path: '/', name: 'cliente_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'cliente_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'cliente/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'cliente_export', methods: ['GET'])]
    public function export(Request $request, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'tipoPersona.descripcion' => 'Tipo de Persona',
            'nombre' => 'Nombre de Cliente',
            'tipoDocumento.descripcion' => 'Tipo de Documento',
            'documento' => 'N° de Documento',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'cliente');
    }

    #[Route(path: '/new', name: 'cliente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $cliente = new Cliente();
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($cliente)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('cliente_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'cliente/new.html.twig',
            [
                'cliente' => $cliente,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'cliente_show', methods: ['GET'])]
    public function show(Cliente $cliente): Response
    {
        $this->denyAccess([Permission::SHOW], $cliente);

        return $this->render('cliente/show.html.twig', ['cliente' => $cliente]);
    }

    #[Route(path: '/{id}/edit', name: 'cliente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $cliente);

        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($cliente)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('cliente_index', ['id' => $cliente->getId()]);
        }

        return $this->render(
            'cliente/edit.html.twig',
            [
                'cliente' => $cliente,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'cliente_delete', methods: ['POST'])]
    public function state(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $cliente);

        if ($this->isCsrfTokenValid('delete'.$cliente->getId(), $request->request->get('_token'))) {
            $cliente->changeActivo();
            if ($manager->save($cliente)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('cliente_index');
    }

    #[Route(path: '/{id}/delete', name: 'cliente_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $cliente);

        if ($this->isCsrfTokenValid('delete_forever'.$cliente->getId(), $request->request->get('_token'))) {
            if ($manager->remove($cliente)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('cliente_index');
    }
}
