<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use CarlosChininin\App\Domain\Model\AuthUser\AuthUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\UsuarioRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: UsuarioRepository::class)]
#[HasLifecycleCallbacks]
class Usuario extends AuthUser
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 50)]
    #[NotBlank]
    #[Length(min: 4, max: 50)]
    #[Groups(groups: 'main')]
    private string $username;

    #[Column(type: 'string', length: 100, unique: true)]
    #[Email]
    #[Groups(groups: 'main')]
    private ?string $email;

    #[Column(type: 'string', length: 100)]
    private ?string $password;

    #[ManyToMany(targetEntity: UsuarioRol::class, inversedBy: 'usuarios')]
    private Collection $usuarioRoles;

    #[Column(type: 'string', length: 100, nullable: true)]
    private ?string $fullName = null;

    private ?string $passwordActual;
    private ?string $passwordNuevo;

    public function __construct()
    {
        $this->usuarioRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username; // Generator::withoutWhiteSpaces($username);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email; // Generator::withoutWhiteSpaces($email);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password; // Generator::withoutWhiteSpaces($password);

        return $this;
    }

    public function __serialize(): array
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return [$this->id, $this->username, $this->password];
    }

    public function __unserialize(array $data): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = $data;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function addUsuarioRole(UsuarioRol $role): self
    {
        if (!$this->usuarioRoles->contains($role)) {
            $this->usuarioRoles[] = $role;
        }

        return $this;
    }

    public function removeUsuarioRole(UsuarioRol $role): self
    {
        if ($this->usuarioRoles->contains($role)) {
            $this->usuarioRoles->removeElement($role);
        }

        return $this;
    }

    public function getUsuarioRoles(): ?Collection
    {
        return $this->usuarioRoles;
    }

    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->usuarioRoles as $role) {
            $roles[] = $role->getRol();
        }

        return $roles;
    }

    public function passwordActual(): ?string
    {
        return $this->passwordActual;
    }

    public function setPasswordActual(?string $passwordActual): self
    {
        $this->passwordActual = $passwordActual;

        return $this;
    }

    public function passwordNuevo(): ?string
    {
        return $this->passwordNuevo;
    }

    public function setPasswordNuevo($passwordNuevo): void
    {
        $this->passwordNuevo = $passwordNuevo;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function authRoles(): Collection|array
    {
        return $this->getUsuarioRoles();
    }
}
