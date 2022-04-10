<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\CategoriaRepository')]
#[HasLifecycleCallbacks]
class Categoria
{
    use EntityTrait;
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private $id;

    #[Column(type: 'string', length: 50)]
    private $descripcion;

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
}
