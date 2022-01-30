<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FechaExtension extends AbstractExtension
{
    private $dias;
    private $meses;

    public function __construct()
    {
        $this->dias = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
        $this->meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('fecha', [$this, 'fechaFilter']),
            new TwigFilter('fechamedia', [$this, 'fechaMediaFilter']),
            new TwigFilter('fechalarga', [$this, 'fechaLargaFilter']),
            new TwigFilter('fechaFormato', [$this, 'fechaFormatoFilter']),
        ];
    }

    public function fechaFilter(?DateTimeInterface $fecha, ?string $format = 'd-m-Y'): string
    {
        if (null === $fecha) {
            return '';
        }

        return $fecha->format($format);
    }

    public function fechaMediaFilter(?DateTimeInterface $fecha): string
    {
        if (null === $fecha) {
            return '';
        }

        return $fecha->format('d').' de '.mb_strtoupper($this->meses[$fecha->format('n') - 1]).' del '.$fecha->format('Y');
    }

    public function fechaLargaFilter(?DateTimeInterface $fecha): string
    {
        if (null === $fecha) {
            return '';
        }

        return $this->dias[$fecha->format('w')].', '.$fecha->format('d').' de '.$this->meses[$fecha->format('n') - 1].' del '.$fecha->format('Y');
    }

    public function fechaFormatoFilter(?DateTimeInterface $fecha, string $tipo): string
    {
        if ('F' === $tipo) {
            return $this->meses[$fecha->format('n') - 1];
        }

        return '';
    }
}
