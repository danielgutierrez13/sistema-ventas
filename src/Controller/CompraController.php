<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Compra;
use Pidia\Apps\Demo\Form\CompraType;
use Pidia\Apps\Demo\Manager\CompraManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/compra')]
class CompraController extends WebAuthController
{
    public const BASE_ROUTE = 'compra_index';

    #[Route(path: '/', name: 'compra_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'compra_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, CompraManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'compra/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'compra_export', methods: ['GET'])]
    public function export(Request $request, CompraManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'codigo' => 'Codigo',
            'proveedor.nombre' => 'Nombre de Proveedor',
            'precio' => 'Precio',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'compra');
    }

    #[Route(path: '/new', name: 'compra_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CompraManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $compra = new Compra();
        $form = $this->createForm(CompraType::class, $compra);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $compra->setPropietario($this->getUser());

            foreach ($compra->getDetalleCompras() as $detalle) {
                $producto = $detalle->getProducto();
                $cantidad = $detalle->getCantidad();
                $detalle->setPropietario($this->getUser());

                $producto->setStock($producto->getStock() + $cantidad);
                $entityManager->persist($producto);
                $entityManager->flush();
            }

            if ($manager->save($compra)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('compra_index');
        }

        return $this->renderForm(
            'compra/new.html.twig',
            [
                'compra' => $compra,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'compra_show', methods: ['GET'])]
    public function show(Compra $compra): Response
    {
        $this->denyAccess([Permission::SHOW], $compra);

        return $this->render('compra/show.html.twig', ['compra' => $compra]);
    }

    #[Route(path: '/{id}/edit', name: 'compra_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compra $compra, CompraManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::EDIT], $compra);
        $CompraAnterior = $compra->clone();
        $form = $this->createForm(CompraType::class, $compra);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($CompraAnterior->getDetalleCompras() as $detallesAnterior) {
                $producto = $detallesAnterior->getProducto();
                $producto->setStock($producto->getStock() - $detallesAnterior->getCantidad());
                $entityManager->persist($producto);
                $entityManager->flush();
            }

            foreach ($compra->getDetalleCompras() as $detalle) {
                $producto = $detalle->getProducto();
                $producto->setStock($producto->getStock() + $detalle->getCantidad());
                $entityManager->persist($producto);
                $entityManager->flush();
            }

            if ($manager->save($compra)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('compra_index', ['id' => $compra->getId()]);
        }

        return $this->render(
            'compra/edit.html.twig',
            [
                'compra' => $compra,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'compra_delete', methods: ['POST'])]
    public function state(Request $request, Compra $compra, CompraManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $compra);

        if ($this->isCsrfTokenValid('delete'.$compra->getId(), $request->request->get('_token'))) {
            $compra->changeActivo();
            if ($manager->save($compra)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('compra_index');
    }

    #[Route(path: '/{id}/delete', name: 'compra_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Compra $compra, CompraManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::DELETE], $compra);

        if ($this->isCsrfTokenValid('delete_forever'.$compra->getId(), $request->request->get('_token'))) {
            foreach ($compra->getDetalleCompras() as $detalle) {
                $producto = $detalle->getProducto();
                $cantidad = $detalle->getCantidad();
                $producto->setStock($producto->getStock() - $cantidad);
                $entityManager->persist($producto);
                $entityManager->flush();
            }
            if ($manager->remove($compra)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('compra_index');
    }
}
