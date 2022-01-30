<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('filter_text', [$this, 'filterText']),
        ];
    }

    public function filterText(?string $text): string
    {
        if (null === $text) {
            return '';
        }

        return preg_replace('([^A-Za-z0-9])', ' ', $text);
    }
}
