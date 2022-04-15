<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\TipoDocumentoRepository')]
#[HasLifecycleCallbacks]
class TipoDocumento
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 100)]
    private ?string $descripcion = null;

    #[ManyToOne(targetEntity: TipoPersona::class)]
    private ?TipoPersona $tipoPersona = null;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescripcion();
    }

    public function getTipoPersona(): ?TipoPersona
    {
        return $this->tipoPersona;
    }

    public function setTipoPersona(?TipoPersona $tipoPersona): self
    {
        $this->tipoPersona = $tipoPersona;

        return $this;
    }

    
}
