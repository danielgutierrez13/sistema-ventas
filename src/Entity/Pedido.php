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
use Doctrine\ORM\Mapping\OneToMany;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\PedidoRepository')]
#[HasLifecycleCallbacks]
class Pedido
{
    use EntityTrait;
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Vendedor::class)]
    #[JoinColumn(nullable: false)]
    private ?Vendedor $vendedor = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $precioFinal;

    #[Column(type: 'string', length: 15)]
    private ?string $codigo = null;

    #[Column(type: 'boolean', nullable: true)]
    private ?bool $estadoPago = null;

    #[ManyToOne(targetEntity: Cliente::class)]
    private ?Cliente $cliente = null;

    #[ManyToOne(targetEntity: TipoPago::class)]
    private ?TipoPago $tipoPago = null;

    #[ManyToOne(targetEntity: TipoMoneda::class)]
    private ?TipoMoneda $tipoMoneda = null;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $cantidadRecibida = null;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $cambio = null;

    #[OneToMany(mappedBy: 'pedido', targetEntity: DetallePedido::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $detallePedidos;

    public function __construct()
    {
        $this->detallePedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVendedor(): ?Vendedor
    {
        return $this->vendedor;
    }

    public function setVendedor(?Vendedor $vendedor): self
    {
        $this->vendedor = $vendedor;

        return $this;
    }

    public function getPrecioFinal(): ?string
    {
        return $this->precioFinal;
    }

    public function setPrecioFinal(string $precioFinal): self
    {
        $this->precioFinal = $precioFinal;

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
        return $this->getCodigo();
    }

    public function getEstadoPago(): ?bool
    {
        return $this->estadoPago;
    }

    public function setEstadoPago(?bool $estadoPago): self
    {
        $this->estadoPago = $estadoPago;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getTipoPago(): ?TipoPago
    {
        return $this->tipoPago;
    }

    public function setTipoPago(?TipoPago $tipoPago): self
    {
        $this->tipoPago = $tipoPago;

        return $this;
    }

    public function getTipoMoneda(): ?TipoMoneda
    {
        return $this->tipoMoneda;
    }

    public function setTipoMoneda(?TipoMoneda $tipoMoneda): self
    {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    public function getCantidadRecibida(): ?string
    {
        return $this->cantidadRecibida;
    }

    public function setCantidadRecibida(?string $cantidadRecibida): self
    {
        $this->cantidadRecibida = $cantidadRecibida;

        return $this;
    }

    public function getCambio(): ?string
    {
        return $this->cambio;
    }

    public function setCambio(?string $cambio): self
    {
        $this->cambio = $cambio;

        return $this;
    }

    /**
     * @return Collection<int, DetallePedido>
     */
    public function getDetallePedidos(): Collection
    {
        return $this->detallePedidos;
    }

    public function addDetallePedido(DetallePedido $detallePedido): self
    {
        if (!$this->detallePedidos->contains($detallePedido)) {
            $this->detallePedidos[] = $detallePedido;
            $detallePedido->setPedido($this);
        }

        return $this;
    }

    public function removeDetallePedido(DetallePedido $detallePedido): self
    {
        if ($this->detallePedidos->removeElement($detallePedido)) {
            // set the owning side to null (unless already changed)
            if ($detallePedido->getPedido() === $this) {
                $detallePedido->setPedido(null);
            }
        }

        return $this;
    }

    public function clone(): self
    {
        $PedidoCopia = new self();
        $PedidoCopia->setCodigo($this->getCodigo());
        $PedidoCopia->setVendedor($this->getVendedor());
        $PedidoCopia->setCliente($this->getCliente());
        $PedidoCopia->setTipoPago($this->getTipoPago());
        $PedidoCopia->setTipoMoneda($this->getTipoMoneda());
        $PedidoCopia->setCantidadRecibida($this->getCantidadRecibida());
        $PedidoCopia->setCambio($this->getCambio());
        $PedidoCopia->setEstadoPago($this->getEstadoPago());
        $PedidoCopia->setPrecioFinal($this->getPrecioFinal());

        foreach ($this->getDetallePedidos() as $detallePedidos) {
            $detalleCopia = new DetallePedido();
            $detalleCopia->setProducto($detallePedidos->getProducto());
            $detalleCopia->setCantidad($detallePedidos->getCantidad());
            $detalleCopia->setPrecio($detallePedidos->getPrecio());
            $detalleCopia->setDescuento($detallePedidos->getDescuento());
            $detalleCopia->setEstadoEntrega($detallePedidos->getEstadoEntrega());
            $PedidoCopia->addDetallePedido($detalleCopia);
        }

        return $PedidoCopia;
    }
}
