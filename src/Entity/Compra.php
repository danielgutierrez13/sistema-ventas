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

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\CompraRepository')]
#[HasLifecycleCallbacks]
class Compra
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 15)]
    private ?string $codigo = null;

    #[ManyToOne(targetEntity: Proveedor::class)]
    #[JoinColumn(nullable: false)]
    private ?Proveedor $proveedor = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $precio = null;

    #[OneToMany(mappedBy: 'compra', targetEntity: DetalleCompra::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $detalleCompras;

    public function __construct()
    {
        $this->detalleCompras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProveedor(): ?Proveedor
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedor $proveedor): self
    {
        $this->proveedor = $proveedor;

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

    /**
     * @return Collection<int, DetalleCompra>
     */
    public function getDetalleCompras(): Collection
    {
        return $this->detalleCompras;
    }

    public function addDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if (!$this->detalleCompras->contains($detalleCompra)) {
            $this->detalleCompras[] = $detalleCompra;
            $detalleCompra->setCompra($this);
        }

        return $this;
    }

    public function removeDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if ($this->detalleCompras->removeElement($detalleCompra)) {
            // set the owning side to null (unless already changed)
            if ($detalleCompra->getCompra() === $this) {
                $detalleCompra->setCompra(null);
            }
        }

        return $this;
    }

    public function clone(): self
    {
        $CompraCopia = new self();
        $CompraCopia->setCodigo($this->getCodigo());
        $CompraCopia->setProveedor($this->getProveedor());
        $CompraCopia->setPrecio($this->getPrecio());

        foreach ($this->getDetalleCompras() as $detalleCompra) {
            $detalleCopia = new DetalleCompra();
            $detalleCopia->setProducto($detalleCompra->getProducto());
            $detalleCopia->setPrecio($detalleCompra->getPrecio());
            $detalleCopia->setCantidad($detalleCompra->getCantidad());
            $CompraCopia->addDetalleCompra($detalleCopia);
        }

        return $CompraCopia;
    }
}
