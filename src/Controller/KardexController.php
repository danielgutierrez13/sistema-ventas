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
        $mes = 4;
        $anio = 2022;
        $producto = $productoRepository->find(2);
        $compras = $compraRepository->findBy(['activo' => true]);
        $ventas = $pedidoRepository->findBy(['estadoPago' => true, 'activo' => true]);
        $stockActual = $producto->getStock();

        $data = [];
        $this->obtenerCompras($compras, $data, $producto, $stockActual, $mes, $anio);
        $this->obtenerVentas($ventas, $data, $producto, $stockActual, $mes, $anio);

        ksort($data);

        $cantidadInicial = $stockActual;
        $this->obtenerSaldos($cantidadInicial, $data, $stockActual);

        return $this->render('kardex/index.html.twig', [
            'data' => $data,
            'cantidad' => $cantidadInicial,
        ]);
    }

    private function obtenerCompras(array $compras, array &$data, $producto, &$stockActual, $mes, $anio): void
    {
        foreach ($compras as $compra) {
            $fecha = $compra->createdAt()->format('Y-m-d');
            $auxmes = $compra->createdAt()->format('m');
            $auxanio = $compra->createdAt()->format('Y');
            foreach ($compra->getDetalleCompras() as $detalleCompra) {
                if ($producto == $detalleCompra->getProducto()) {
                    $precio = (float) $detalleCompra->getPrecio();
                    $cantidad = (float) $detalleCompra->getCantidad();
                    if ($auxmes == $mes && $auxanio == $anio) {
                        $data[$fecha]['compra'][] = [
                            'fecha' => $compra->createdAt()->format('d-m-Y'),
                            'producto' => $detalleCompra->getProducto()->getDescripcion(),
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'cantidad' => $cantidad,
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if ($auxmes >= $mes && $auxanio >= $anio) {
                        $stockActual -= $cantidad;
                    }
                }
            }
        }
    }

    private function obtenerVentas(array $ventas, array &$data, $producto, &$stockActual, $mes, $anio): void
    {
        foreach ($ventas as $venta) {
            $fecha = $venta->createdAt()->format('Y-m-d');
            $auxmes = $venta->createdAt()->format('m');
            $auxanio = $venta->createdAt()->format('Y');
            foreach ($venta->getDetallePedidos() as $detallePedido) {
                if ($producto == $detallePedido->getProducto()) {
                    $precio = (float) $detallePedido->getPrecio();
                    $cantidad = (float) $detallePedido->getCantidad();
                    if ($auxmes == $mes && $auxanio == $anio) {
                        $data[$fecha]['venta'][] = [
                            'fecha' => $venta->createdAt()->format('d-m-Y'),
                            'producto' => $detallePedido->getProducto()->getDescripcion(),
                            'cantidad' => $cantidad,
                            'precio' => sprintf('%.2f', ($precio / $cantidad)),
                            'total' => sprintf('%.2f', ($precio)),
                        ];
                    }
                    if ($auxmes >= $mes && $auxanio >= $anio) {
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
