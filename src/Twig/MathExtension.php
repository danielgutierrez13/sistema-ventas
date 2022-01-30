<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Pidia\Apps\Demo\Utils\Math;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MathExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('redondeo', [$this, 'redondeoFilter']),
            new TwigFilter('numero', [$this, 'numeroFilter']),
            new TwigFilter('numeroNull', [$this, 'numeroNullFilter']),
            new TwigFilter('porcentaje', [$this, 'porcentajeFilter']),
        ];
    }

    public function redondeoFilter(?float $value, int $decimal = 2): float
    {
        if (null === $value) {
            return 0;
        }

        return Math::round($value, $decimal);
    }

    public function numeroFilter(?float $value, int $decimal = 2, bool $comma = true): string
    {
        return Math::number($value, $decimal, $comma);
    }

    public function numeroNullFilter(?float $value, int $decimal = 2, bool $comma = true): string
    {
        if (null === $value) {
            return '';
        }

        return Math::number($value, $decimal, $comma);
    }

    public function porcentajeFilter(?float $value, ?int $decimal = 2): string
    {
        if (null === $value) {
            return '';
        }

        return Math::number($value * 100, $decimal).' %';
    }
}
