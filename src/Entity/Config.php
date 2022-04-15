<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[Table(name: 'config')]
#[Entity(repositoryClass: 'Pidia\Apps\Demo\Repository\ConfigRepository')]
#[HasLifecycleCallbacks]
class Config
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 15)]
    private ?string $alias;

    #[Column(type: 'string', length: 255)]
    private ?string $nombre;

    #[Column(type: 'string', length: 30)]
    private ?string $nombreCorto;

    #[ManyToMany(targetEntity: ConfigMenu::class)]
    #[JoinTable(name: 'config_config_menu_menus')]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias; //mb_strtolower(Generator::withoutWhiteSpaces($alias));

        return $this;
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

    public function getNombreCorto(): ?string
    {
        return $this->nombreCorto;
    }

    public function setNombreCorto(string $nombreCorto): self
    {
        $this->nombreCorto = $nombreCorto;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombreCorto();
    }

    /**
     * @return Collection|ConfigMenu[]
     */
    public function getMenus(): Collection|array
    {
        return $this->menus;
    }

    public function addMenu(ConfigMenu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
        }

        return $this;
    }

    public function removeMenu(ConfigMenu $menu): self
    {
        if ($this->menus->contains($menu)) {
            $this->menus->removeElement($menu);
        }

        return $this;
    }
}
