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
        $producto = $productoRepository->find(1);
        $compras = $compraRepository->findBy(['activo' => true]);
        $ventas = $pedidoRepository->findBy(['estadoPago' => true, 'activo' => true]);

        $data = [];
        $this->obtenerCompras($compras, $data, $producto);
        $this->obtenerVentas($ventas, $data, $producto);

        ksort($data);

        $cantidadInicial = 0.00; // Obtenido de la table Producto.
        $this->obtenerSaldos($cantidadInicial, $data);

        return $this->render('kardex/index.html.twig', [
            'data' => $data,
        ]);
    }

    private function obtenerCompras(array $compras, array &$data, $producto): void
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
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'total' => $precio * $cantidad,
                    ];
                }
            }
        }
    }

    private function obtenerVentas(array $ventas, array &$data, $producto): void
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
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'total' => $precio * $cantidad,
                    ];
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
                    // total saldo
                }
            }
            if (isset($item['venta'])) {
                foreach ($item['venta'] as &$venta) {
                    $cantidadSaldo -= $venta['cantidad'];
                    $venta['cantidadSaldo'] = $cantidadSaldo;
                    // total saldo
                }
            }
        }
    }
}
