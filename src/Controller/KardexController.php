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
        $producto = $productoRepository->find(2);
        $compras = $compraRepository->findBy(['activo' => true]);
        $ventas = $pedidoRepository->findBy(['estadoPago' => true, 'activo' => true]);
        $stockActual = $producto->getStock();

        $data = [];
        $this->obtenerCompras($compras, $data, $producto, $stockActual);
        $this->obtenerVentas($ventas, $data, $producto, $stockActual);

        ksort($data);

        $cantidadInicial = $stockActual; // Obtenido de la table Producto.
        $this->obtenerSaldos($cantidadInicial, $data, $stockActual);

        return $this->render('kardex/index.html.twig', [
            'data' => $data,
            'cantidad' => $cantidadInicial,
        ]);
    }

    private function obtenerCompras(array $compras, array &$data, $producto, &$stockActual): void
    {
        foreach ($compras as $compra) {
            $fecha = $compra->createdAt()->format('Y-m-d');
            foreach ($compra->getDetalleCompras() as $detalleCompra) {
                if ($producto == $detalleCompra->getProducto()) {
                    $precio = (float) $detalleCompra->getPrecio();
                    $cantidad = (float) $detalleCompra->getCantidad();
                    $data[$fecha]['compra'][] = [
                        'fecha' => $compra->createdAt()->format('d-m-Y'),
                        'producto' => $detalleCompra->getProducto()->getDescripcion(),
                        'precio' => sprintf('%.2f', ($precio / $cantidad)),
                        'cantidad' => $cantidad,
                        'total' => sprintf('%.2f', ($precio)),
                    ];
                    $stockActual -= $cantidad;
                }
            }
        }
    }

    private function obtenerVentas(array $ventas, array &$data, $producto, &$stockActual): void
    {
        foreach ($ventas as $venta) {
            $fecha = $venta->createdAt()->format('Y-m-d');
            foreach ($venta->getDetallePedidos() as $detallePedido) {
                if ($producto == $detallePedido->getProducto()) {
                    $precio = (float) $detallePedido->getPrecio();
                    $cantidad = (float) $detallePedido->getCantidad();
                    $data[$fecha]['venta'][] = [
                        'fecha' => $venta->createdAt()->format('d-m-Y'),
                        'producto' => $detallePedido->getProducto()->getDescripcion(),
                        'cantidad' => $cantidad,
                        'precio' => sprintf('%.2f', ($precio / $cantidad)),
                        'total' => sprintf('%.2f', ($precio)),
                    ];
                    $stockActual += $cantidad;
                }
            }
        }
    }

    private function obtenerSaldos(float $cantidadSaldo, array &$data): void
    {
        foreach ($data as &$item) {
            if (isset($item['compra'])) {
                foreach ($item['compra'] as &$compra) {
                    $cantidadSaldo += $compra['cantidad'];
                    $compra['cantidadSaldo'] = $cantidadSaldo;
                    $compra['total'] = $cantidadSaldo;
                }
            }
            if (isset($item['venta'])) {
                foreach ($item['venta'] as &$venta) {
                    $cantidadSaldo -= $venta['cantidad'];
                    $venta['cantidadSaldo'] = $cantidadSaldo;
                }
            }
        }
    }
}
