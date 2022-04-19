<?php

namespace Pidia\Apps\Demo\MessageHandler;

use Pidia\Apps\Demo\Message\PagoCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

final class PagoCreatedSendEmailHandler implements MessageHandlerInterface
{
    public function __construct(private MailerInterface $mailer, private LoggerInterface $logger)
    {
    }

    public function __invoke(PagoCreated $message)
    {
        // $pedido = ...
        // $this->verificacionStock();
        $this->sendEmail();
    }

    private function sendEmail(): void
    {
        $email = (new Email())
            ->from('test@pidia.pe')
            ->to('cio@pidia.pe')
            ->subject('Mensaje de prueba!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
