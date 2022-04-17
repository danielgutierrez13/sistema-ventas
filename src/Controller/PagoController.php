<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Cliente;
use Pidia\Apps\Demo\Entity\DetallePedido;
use Pidia\Apps\Demo\Entity\Pedido;
use Pidia\Apps\Demo\Entity\Proveedor;
use Pidia\Apps\Demo\Form\ClienteType;
use Pidia\Apps\Demo\Form\PedidoType;
use Pidia\Apps\Demo\Form\ProveedorType;
use Pidia\Apps\Demo\Manager\PagoManager;
use Pidia\Apps\Demo\Manager\PedidoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pago')]
class PagoController extends WebAuthController
{
    public const BASE_ROUTE = 'pago_index';

    #[Route(path: '/', name: 'pago_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'pedido_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'pago/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'pago_export', methods: ['GET'])]
    public function export(Request $request, PedidoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'codigo' => 'Codigo',
            'vendedor.nombre' => 'Nombre de Vendedor',
            'cliente.nombre' => 'Nombre de Cliente', // no resonator
            'precioFinal' => 'Precio',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'pago');
    }

    #[Route(path: '/{id}/new', name: 'pago_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Pedido $pedido, PedidoManager $manager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW], $pedido);
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

            $pedido->setEstadoPago(1);
            if ($manager->save($pedido)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('pago_index', ['id' => $pedido->getId()]);
        }

        $cliente = new Cliente();
        $fmrCliente = $this->createForm(ClienteType::class, $cliente);
        $fmrCliente->handleRequest($request);

        return $this->render(
            'pago/new.html.twig',
            [
                'pedido' => $pedido,
                'form' => $form->createView(),
                'fmrCliente' => $fmrCliente->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'pago_show', methods: ['GET'])]
    public function show(Pedido $pedido): Response
    {
        $this->denyAccess([Permission::SHOW], $pedido);

        return $this->render('pago/show.html.twig', ['pago' => $pedido]);
    }

    #[Route(path: '/{id}/edit', name: 'pago_edit', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('pago_index', ['id' => $pedido->getId()]);
        }

        return $this->render(
            'pago/edit.html.twig',
            [
                'pedido' => $pedido,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/entregaDetallePago', name: 'pago_entrega_detalle', methods: ['GET', 'POST'])]
    public function entregaDetalle(DetallePedido $detallePedido, EntityManagerInterface $entityManager): Response
    {
        if (1 == $detallePedido->getEstadoEntrega()) {
            $detallePedido->setEstadoEntrega(0);
        } else {
            $detallePedido->setEstadoEntrega(1);
        }
        $entityManager->persist($detallePedido);
        $entityManager->flush();

        return $this->render('pago/show.html.twig', ['pago' => $detallePedido->getPedido()]);
    }

    #[Route(path: '/{id}', name: 'pago_delete', methods: ['POST'])]
    public function state(Request $request, Pedido $pedido, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $pedido);
        $pedido->setEstadoPago(0);
        $pedido->setCliente(null);
        $pedido->setTipoMoneda(null);
        $pedido->setTipoPago(null);
        $pedido->setCantidadRecibida(null);
        $pedido->setCambio(null);
        if ($manager->save($pedido)) {
            $this->messageSuccess('El pago ha sido anulado');
        } else {
            $this->addErrors($manager->errors());
        }

        return $this->redirectToRoute('pago_index');
    }
}
