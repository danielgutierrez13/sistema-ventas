<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class YesnoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('yesno', [YesnoRuntime::class, 'yesnoFilter']),
            new TwigFilter('yesnocustom', [YesnoRuntime::class, 'yesnoCustomFilter']),
        ];
    }
}
