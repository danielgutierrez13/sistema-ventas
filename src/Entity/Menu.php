<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\MenuRepository;

#[Entity(repositoryClass: MenuRepository::class)]
#[HasLifecycleCallbacks]
class Menu
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 50)]
    private ?string $nombre;

    #[Column(type: 'string', length: 50, nullable: true)]
    private ?string $ruta = null;

    #[Column(type: 'string', length: 50, nullable: true)]
    private ?string $icono = null;

    #[Column(type: 'smallint')]
    private int $orden;

    #[ManyToOne(targetEntity: Menu::class)]
    private ?Menu $padre = null;

    public function __construct()
    {
        $this->orden = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(?string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getIcono(): ?string
    {
        return $this->icono;
    }

    public function setIcono(?string $icono): self
    {
        $this->icono = $icono;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): self
    {
        $this->orden = $orden;

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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'ruta' => $this->getRuta(),
            'icono' => $this->getIcono(),
            'orden' => $this->getOrden(),
//            'insignia' => $this->getInsignia(),
        ];
    }
}
