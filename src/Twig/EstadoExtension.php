<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Pidia\Apps\Demo\Entity\Parametro;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EstadoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('estado', [$this, 'estadoFilter']),
            new TwigFilter('estadoAlias', [$this, 'estadoAliasFilter']),
        ];
    }

    public function estadoFilter(Parametro $value): string
    {
        return $this->estadoAliasFilter($value->getAlias(), $value->getNombre());
    }

    public function estadoAliasFilter(string $alias, string $value): string
    {
        $class = '';
        if ('P' === $alias) {
            $class = 'warning';
        }
        if ('A' === $alias) {
            $class = 'success';
        }
        if ('C' === $alias) {
            $class = 'danger';
        }

        return '<small><span class="badge badge-'.$class.'">'.$value.'</span></small>';
    }
}
