<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ConfigMenuExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('build_config_menu', [ConfigMenuRuntime::class, 'buildMenu']),
        ];
    }
}
