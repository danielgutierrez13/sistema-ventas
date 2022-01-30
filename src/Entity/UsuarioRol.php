<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\UsuarioRolRepository;

#[Entity(repositoryClass: UsuarioRolRepository::class)]
#[HasLifecycleCallbacks]
class UsuarioRol
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 50)]
    private ?string $nombre;

    #[Column(type: 'string', length: 30)]
    private ?string $rol;

    #[ManyToMany(targetEntity: Usuario::class, mappedBy: 'usuarioRoles')]
    private Collection $usuarios;

    #[ManyToMany(targetEntity: UsuarioPermiso::class, mappedBy: 'roles', cascade: ['persist', 'remove'])]
    private Collection $permisos;

//    #[ManyToOne(targetEntity: 'Pidia\Apps\Demo\Entity\Config')]
//    private $config;

    public function __construct()
    {
        $this->permisos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = trim($nombre);

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(?string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario)) {
            $this->usuarios->removeElement($usuario);
        }

        return $this;
    }

    /**
     * @return Collection|UsuarioPermiso[]
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(UsuarioPermiso $permiso): self
    {
        if (!$this->permisos->contains($permiso)) {
            $permiso->addRole($this);
            $this->permisos[] = $permiso;
        }

        return $this;
    }

    public function removePermiso(UsuarioPermiso $permiso): self
    {
        if ($this->permisos->contains($permiso)) {
            $this->permisos->removeElement($permiso);
        }

        return $this;
    }

//    public function getConfig(): ?Config
//    {
//        return $this->config;
//    }
//
//    public function setConfig(?Config $config): self
//    {
//        $this->config = $config;
//
//        return $this;
//    }

    public function __toString(): string
    {
        return $this->getNombre();
    }
}
