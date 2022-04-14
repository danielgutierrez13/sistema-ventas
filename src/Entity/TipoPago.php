<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Pidia\Apps\Demo\Repository\TipoPagoRepository;

#[Entity(repositoryClass: TipoPagoRepository::class)]
#[HasLifecycleCallbacks]
class TipoPago
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private $id;

    #[Column(type: 'string', length: 100)]
    private $descripcion;

    #[Column(type: 'string', length: 200, nullable: true)]
    private $propietarioCuenta;

    #[Column(type: 'string', length: 50, nullable: true)]
    private $cuenta;

    #[Column(type: 'string', length: 50, nullable: true)]
    private $nombreCorto;

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

    public function getPropietarioCuenta(): ?string
    {
        return $this->propietarioCuenta;
    }

    public function setPropietarioCuenta(?string $propietarioCuenta): self
    {
        $this->propietarioCuenta = $propietarioCuenta;

        return $this;
    }

    public function getCuenta(): ?string
    {
        return $this->cuenta;
    }

    public function setCuenta(?string $cuenta): self
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    public function getNombreCorto(): ?string
    {
        return $this->nombreCorto;
    }

    public function setNombreCorto(?string $nombreCorto): self
    {
        $this->nombreCorto = $nombreCorto;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescripcion();
    }
}
