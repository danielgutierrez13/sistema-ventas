<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Producto;
use Pidia\Apps\Demo\Form\ProductoType;
use Pidia\Apps\Demo\Manager\ProductoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/producto')]
class ProductoController extends WebAuthController
{
    public const BASE_ROUTE = 'producto_index';

    #[Route(path: '/', name: 'producto_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'producto_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'producto/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'producto_export', methods: ['GET'])]
    public function export(Request $request, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'tipoPersona.descripcion' => 'Tipo de Persona',
            'nombre' => 'Nombre de Producto',
            'tipoDocumento.descripcion' => 'Tipo de Documento',
            'documento' => 'N° de Documento',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'producto');
    }

    #[Route(path: '/new', name: 'producto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($producto)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('producto_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'producto/new.html.twig',
            [
                'producto' => $producto,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'producto_show', methods: ['GET'])]
    public function show(Producto $producto): Response
    {
        $this->denyAccess([Permission::SHOW], $producto);

        return $this->render('producto/show.html.twig', ['producto' => $producto]);
    }

    #[Route(path: '/{id}/edit', name: 'producto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Producto $producto, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $producto);

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($producto)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('producto_index', ['id' => $producto->getId()]);
        }

        return $this->render(
            'producto/edit.html.twig',
            [
                'producto' => $producto,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'producto_delete', methods: ['POST'])]
    public function state(Request $request, Producto $producto, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $producto);

        if ($this->isCsrfTokenValid('delete'.$producto->getId(), $request->request->get('_token'))) {
            $producto->changeActivo();
            if ($manager->save($producto)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('producto_index');
    }

    #[Route(path: '/{id}/delete', name: 'producto_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Producto $producto, ProductoManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $producto);

        if ($this->isCsrfTokenValid('delete_forever'.$producto->getId(), $request->request->get('_token'))) {
            if ($manager->remove($producto)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('producto_index');
    }
}