<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\ClienteRepository')]
#[HasLifecycleCallbacks]
class Cliente
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: TipoPersona::class)]
    #[JoinColumn(nullable: false)]
    private ?TipoPersona $tipoPersona = null;

    #[Column(type: 'string', length: 200)]
    private $nombre;

    #[ManyToOne(targetEntity: TipoDocumento::class)]
    #[JoinColumn(nullable: false)]
    private $tipoDocumento;

    #[Column(type: 'string', length: 20, unique: true)]
    private $documento;

    #[Column(type: 'string', length: 200, nullable: true)]
    private $direccion;

    #[Column(type: 'string', length: 15, nullable: true)]
    private $telefono;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getNombre().' - '.$this->getDocumento();
    }
}
