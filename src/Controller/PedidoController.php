<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\DetallePedido;
use Pidia\Apps\Demo\Entity\Pedido;
use Pidia\Apps\Demo\Form\PedidoType;
use Pidia\Apps\Demo\Manager\PedidoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pedido')]
class PedidoController extends WebAuthController
{
    public const BASE_ROUTE = 'pedido_index';

    #[Route(path: '/', name: 'pedido_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'pedido_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, PedidoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'pedido/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'pedido_export', methods: ['GET'])]
    public function export(Request $request, PedidoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'codigo' => 'Codigo',
            'vendedor.nombre' => 'Nombre de Vendedor',
            'precioFinal' => 'Precio',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'compra');
    }

    #[Route(path: '/new', name: 'pedido_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PedidoManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $pedido = new Pedido();
        $form = $this->createForm(PedidoType::class, $pedido);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pedido->setPropietario($this->getUser());
            $pedido->setEstadoPago(0);
            foreach ($pedido->getDetallePedidos() as $detalle) {
                $producto = $detalle->getProducto();
                $cantidad = $detalle->getCantidad();
                $detalle->setPropietario($this->getUser());
                $detalle->setEstadoEntrega(0);
                $producto->setStock($producto->getStock() - $cantidad);
                $entityManager->persist($producto);
                $entityManager->flush();
            }
            if ($manager->save($pedido)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('pedido_index');
        }

        return $this->renderForm(
            'pedido/new.html.twig',
            [
                'pedido' => $pedido,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'pedido_show', methods: ['GET'])]
    public function show(Pedido $pedido): Response
    {
        $this->denyAccess([Permission::SHOW], $pedido);

        return $this->render('pedido/show.html.twig', ['pedido' => $pedido]);
    }

    #[Route(path: '/{id}/edit', name: 'pedido_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pedido $pedido, PedidoManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::EDIT], $pedido);
        $PedidoAnterior = $pedido->clone();
        $form = $this->createForm(PedidoType::class, $pedido);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($PedidoAnterior->getDetallePedidos() as $detallesAnterior) {
                $producto = $detallesAnterior->getProducto();
                $producto->setStock($producto->getStock() + $detallesAnterior->getCantidad());
                $entityManager->persist($producto);
                $entityManager->flush();
            }

            foreach ($pedido->getDetallePedidos() as $detalle) {
                $producto = $detalle->getProducto();
                $producto->setStock($producto->getStock() - $detalle->getCantidad());
                $entityManager->persist($producto);
                $entityManager->flush();
            }
            if ($manager->save($pedido)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('pedido_index', ['id' => $pedido->getId()]);
        }

        return $this->render(
            'pedido/edit.html.twig',
            [
                'pedido' => $pedido,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'pedido_delete', methods: ['POST'])]
    public function state(Request $request, Pedido $pedido, PedidoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $pedido);

        if ($this->isCsrfTokenValid('delete'.$pedido->getId(), $request->request->get('_token'))) {
            $pedido->changeActivo();
            if ($manager->save($pedido)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('pedido_index');
    }

    #[Route(path: '/{id}/entregaDetalle', name: 'pago_entrega_detalle', methods: ['GET', 'POST'])]
    public function entregaDetalle(DetallePedido $detallePedido, EntityManagerInterface $entityManager): Response
    {
        if (1 == $detallePedido->getEstadoEntrega()) {
            $detallePedido->setEstadoEntrega(0);
        } else {
            $detallePedido->setEstadoEntrega(1);
        }
        $entityManager->persist($detallePedido);
        $entityManager->flush();

        return $this->render('pedido/show.html.twig', ['pedido' => $detallePedido->getPedido()]);
    }

    #[Route(path: '/{id}/delete', name: 'pedido_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Pedido $pedido, PedidoManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::DELETE], $pedido);

        if ($this->isCsrfTokenValid('delete_forever'.$pedido->getId(), $request->request->get('_token'))) {
            foreach ($pedido->getDetallePedidos() as $detalle) {
                $producto = $detalle->getProducto();
                $cantidad = $detalle->getCantidad();
                $producto->setStock($producto->getStock() + $cantidad);
                $entityManager->persist($producto);
                $entityManager->flush();
            }
            if ($manager->remove($pedido)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('pedido_index');
    }
}
