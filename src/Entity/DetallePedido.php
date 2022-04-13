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

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\DetallePedidoRepository')]
#[HasLifecycleCallbacks]
class DetallePedido
{
    use EntityTrait;
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private $id;

    #[ManyToOne(targetEntity: Producto::class)]
    #[JoinColumn(nullable: false)]
    private $producto;

    #[Column(type: 'integer')]
    private $cantidad;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private $precio;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private $descuento;

    #[ManyToOne(targetEntity: Pedido::class, inversedBy: 'detallePedidos')]
    #[JoinColumn(nullable: false)]
    private $pedido;

    #[Column(type: 'boolean', nullable: true)]
    private $estadoEntrega;

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

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

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

    public function getDescuento(): ?string
    {
        return $this->descuento;
    }

    public function setDescuento(string $descuento): self
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(?Pedido $pedido): self
    {
        $this->pedido = $pedido;

        return $this;
    }

    public function getEstadoEntrega(): ?bool
    {
        return $this->estadoEntrega;
    }

    public function setEstadoEntrega(?bool $estadoEntrega): self
    {
        $this->estadoEntrega = $estadoEntrega;

        return $this;
    }
}
