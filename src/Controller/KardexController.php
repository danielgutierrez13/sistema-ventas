<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use Pidia\Apps\Demo\Repository\CompraRepository;
use Pidia\Apps\Demo\Repository\PedidoRepository;
use Pidia\Apps\Demo\Repository\ProductoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/kardex')]
class KardexController extends WebAuthController
{
    public const BASE_ROUTE = 'kardex_index';

    #[Route('/', name: 'kardex_index')]
    public function index(Request $request, CompraRepository $compraRepository, PedidoRepository $pedidoRepository, ProductoRepository $productoRepository): Response
    {
        $id = $request->get('producto');
        $fechaBuscar = $request->get('fecha');

        if (null == $id) {
            $id = 0;
        }

        if (null == $fechaBuscar) {
            $fechaBuscar = 0;
        }

        $producto = $productoRepository->find($id);
        if (null == $producto) {
            $stockActual = 0;
        } else {
            $stockActual = $producto->getStock();
        }

        $productos = $productoRepository->findBy(['activo' => true]);
        $compras = $compraRepository->findBy(['activo' => true]);
        $ventas = $pedidoRepository->findBy(['estadoPago' => true, 'activo' => true]);

        $data = [];
        $this->obtenerCompras($compras, $data, $producto, $stockActual, $fechaBuscar);
        $this->obtenerVentas($ventas, $data, $producto, $stockActual, $fechaBuscar);

        ksort($data);
        $cantidadInicial = $stockActual;
        $this->obtenerSaldos($cantidadInicial, $data, $stockActual);

        return $this->render('kardex/index.html.twig', [
            'fecha' => $fechaBuscar,
            'idProducto' => $id,
            'productos' => $productos,
            'data' => $data,
            'cantidad' => $cantidadInicial,
        ]);
    }

    private function obtenerCompras(array $compras, array &$data, $producto, &$stockActual, $fechaBuscar): void
    {
        foreach ($compras as $compra) {
            $fecha = $compra->createdAt()->format('Y-m-d');
            $fechaComprar = $compra->createdAt()->format('Y-m');
            foreach ($compra->getDetalleCompras() as $detalleCompra) {
                if ($producto == $detalleCompra->getProducto()) {
                    $precio = (float) $detalleCompra->getPrecio();
                    $cantidad = (float) $detalleCompra->getCantidad();
                    if ($fechaBuscar == $fechaComprar) {
                        $data[$fecha]['compra'][] = [
                            'fecha' => $compra->createdAt()->format('d-m-Y'),
                            'producto' => $detalleCompra->getProducto()->getDescripcion(),
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'cantidad' => $cantidad,
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if (null == $fechaBuscar) {
                        $data[$fecha]['compra'][] = [
                            'fecha' => $compra->createdAt()->format('d-m-Y'),
                            'producto' => $detalleCompra->getProducto()->getDescripcion(),
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'cantidad' => $cantidad,
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if ($fechaComprar >= $fechaBuscar) {
                        $stockActual -= $cantidad;
                    }
                }
            }
        }
    }

    private function obtenerVentas(array $ventas, array &$data, $producto, &$stockActual, $fechaBuscar): void
    {
        foreach ($ventas as $venta) {
            $fecha = $venta->createdAt()->format('Y-m-d');
            $fechaComprar = $venta->createdAt()->format('Y-m');
            foreach ($venta->getDetallePedidos() as $detallePedido) {
                if ($producto == $detallePedido->getProducto()) {
                    $precio = (float) $detallePedido->getPrecio();
                    $cantidad = (float) $detallePedido->getCantidad();
                    if ($fechaBuscar == $fechaComprar) {
                        $data[$fecha]['venta'][] = [
                            'fecha' => $venta->createdAt()->format('d-m-Y'),
                            'producto' => $detallePedido->getProducto()->getDescripcion(),
                            'cantidad' => $cantidad,
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if (null == $fechaBuscar) {
                        $data[$fecha]['venta'][] = [
                            'fecha' => $venta->createdAt()->format('d-m-Y'),
                            'producto' => $detallePedido->getProducto()->getDescripcion(),
                            'cantidad' => $cantidad,
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if ($fechaComprar >= $fechaBuscar) {
                        $stockActual += $cantidad;
                    }
                }
            }
        }
    }

    private function obtenerSaldos(float $cantidadSaldo, array &$data): void
    {
        $saldo = $cantidadSaldo;
        foreach ($data as &$item) {
            if (isset($item['compra'])) {
                foreach ($item['compra'] as &$compra) {
                    $cantidadSaldo += $compra['cantidad'];
                    $compra['totalSaldo'] = $cantidadSaldo;
                    $compra['cantidadSaldo'] = $saldo;
                    $saldo = $cantidadSaldo;
                }
            }
            if (isset($item['venta'])) {
                foreach ($item['venta'] as &$venta) {
                    $cantidadSaldo -= $venta['cantidad'];
                    $venta['totalSaldo'] = $cantidadSaldo;
                    $venta['cantidadSaldo'] = $saldo;
                    $saldo = $cantidadSaldo;
                }
            }
        }
    }
}
