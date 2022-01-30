<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

trait EntityTrait
{
    #[ManyToOne(targetEntity: 'Pidia\Apps\Demo\Entity\Usuario')]
    #[JoinColumn(nullable: true)]
    private ?Usuario $propietario = null;

    #[ManyToOne(targetEntity: 'Pidia\Apps\Demo\Entity\Config')]
    #[JoinColumn(nullable: true)]
    private ?Config $config = null;

    #[Column(name: 'created_at', type: 'datetime')]
    protected ?DateTimeInterface $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    protected ?DateTimeInterface $updatedAt = null;

    #[Column(type: 'boolean')]
    #[Groups(groups: 'default')]
    private bool $activo;

    #[Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    public function activo(): bool
    {
        return $this->activo;
    }

    public function changeActivo(): bool
    {
        $state = $this->activo;
        $this->activo = !$state;

        return $state;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function propietario(): ?Usuario
    {
        return $this->propietario;
    }

    public function setPropietario(Usuario|UserInterface|null $propietario): self
    {
        if (null !== $propietario) {
            $this->propietario = $propietario;
            $this->setConfig($propietario->config());
        }

        return $this;
    }

    public function uuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function config(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    #[PrePersist]
    public function init()
    {
        $this->uuid = Uuid::v4();
        $this->activo = true;
    }

    #[PrePersist]
    #[PreUpdate]
    public function updatedDatetime(): void
    {
        $this->updatedAt = new DateTime();
        if (null === $this->createdAt) {
            $this->createdAt = new DateTime();
        }
    }
}
