<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\DetalleCompraRepository')]
#[HasLifecycleCallbacks]
class DetalleCompra
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Producto::class)]
    #[JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $precio = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $cantidad = null;

    #[ManyToOne(targetEntity: Compra::class, inversedBy: 'detalleCompras')]
    #[JoinColumn(nullable: false)]
    private ?Compra $compra = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

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

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getCompra(): ?Compra
    {
        return $this->compra;
    }

    public function setCompra(?Compra $compra): self
    {
        $this->compra = $compra;

        return $this;
    }
}
