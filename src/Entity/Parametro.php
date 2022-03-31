<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Symfony\Component\Validator\Constraints\Length;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\ParametroRepository')]
#[HasLifecycleCallbacks]
class Parametro
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private $id;
    #[Column(type: 'string', length: 100)]
    private $nombre;
    #[Column(type: 'string', length: 16, nullable: true)]
    #[Length(max: 16, maxMessage: 'Debe tener un máximo de 16 carácteres')]
    private $alias;
    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $valor;
    #[ManyToOne(targetEntity: 'Pidia\Apps\Demo\Entity\Parametro')]
    private $padre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias; //Generator::withoutWhiteSpaces($alias);

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getPadre(): ?self
    {
        return $this->padre;
    }

    public function setPadre(?self $padre): self
    {
        $this->padre = $padre;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombre();
    }

    public static function nombresToArray(Collection $parametros): array
    {
        $nombres = [];

        /** @var Parametro $parametro */
        foreach ($parametros as $parametro) {
            $nombres[] = $parametro->getNombre();
        }

        return $nombres;
    }
}
