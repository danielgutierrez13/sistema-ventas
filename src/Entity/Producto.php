<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\ProductoRepository')]
#[HasLifecycleCallbacks]
class Producto
{
    use EntityTrait;
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Categoria::class)]
    #[JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ManyToOne(targetEntity: Marca::class)]
    #[JoinColumn(nullable: false)]
    private ?Marca $marca = null;

    #[Column(type: 'string', length: 100)]
    private ?string $descripcion = null;

    #[ManyToOne(targetEntity: UnidadMedida::class)]
    #[JoinColumn(nullable: false)]
    private ?UnidadMedida $unidadMedida = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $precio = null;

    #[Column(type: 'integer')]
    private ?int $stock = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $precioVenta = null;

    #[Column(type: 'string', length: 8)]
    private ?string $codigo = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getMarca(): ?Marca
    {
        return $this->marca;
    }

    public function setMarca(?Marca $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getUnidadMedida(): ?UnidadMedida
    {
        return $this->unidadMedida;
    }

    public function setUnidadMedida(?UnidadMedida $unidadMedida): self
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }


    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPrecioVenta(): ?string
    {
        return $this->precioVenta;
    }

    public function setPrecioVenta(string $precioVenta): self
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescripcion();
    }

}
