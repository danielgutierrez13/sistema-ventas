<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\MessageHandler;

use Pidia\Apps\Demo\Message\PagoCreated;
use Pidia\Apps\Demo\Repository\PedidoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PagoCreatedCreateOrdenCompraHandler implements MessageHandlerInterface
{
    public function __construct(private LoggerInterface $logger, private PedidoRepository $pedidoRepository)
    {
    }

    public function __invoke(PagoCreated $pagoCreated)
    {
        $pedido = $this->pedidoRepository->find($pagoCreated->pagoId());
        $this->logger->info('Se ha pagado el pedido del cliente: '.$pedido->getCliente()->getNombre());
    }
}
