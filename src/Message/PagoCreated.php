<?php

namespace Pidia\Apps\Demo\Message;

final class PagoCreated
{
    public function __construct(private int $pagoId)
    {
    }

    public function pagoId(): int
    {
        return $this->pagoId;
    }
}
