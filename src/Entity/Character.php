<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageThumb = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFull = null;

    #[ORM\Column]
    private ?bool $royal = null;

    /**
     * @var Collection<int, Actor>
     */
    #[ORM\OneToMany(targetEntity: Actor::class, mappedBy: 'character')]
    private Collection $actors;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'parents')]
    private ?self $children = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'children')]
    private Collection $parents;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $siblings;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'killedBy')]
    private Collection $killed;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'killed')]
    private Collection $killedBy;

    /**
     * @var Collection<int, House>
     */
    #[ORM\ManyToMany(targetEntity: House::class, inversedBy: 'characters')]
    private Collection $houses;

    public function __construct()
    {
        $this->actors = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->siblings = new ArrayCollection();
        $this->killed = new ArrayCollection();
        $this->killedBy = new ArrayCollection();
        $this->houses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getImageThumb(): ?string
    {
        return $this->imageThumb;
    }

    public function setImageThumb(?string $imageThumb): static
    {
        $this->imageThumb = $imageThumb;

        return $this;
    }

    public function getImageFull(): ?string
    {
        return $this->imageFull;
    }

    public function setImageFull(?string $imageFull): static
    {
        $this->imageFull = $imageFull;

        return $this;
    }

    public function isRoyal(): ?bool
    {
        return $this->royal;
    }

    public function setRoyal(bool $royal): static
    {
        $this->royal = $royal;

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): static
    {
        if (!$this->actors->contains($actor)) {
            $this->actors->add($actor);
            $actor->setCharacter($this);
        }

        return $this;
    }

    public function removeActor(Actor $actor): static
    {
        if ($this->actors->removeElement($actor)) {
            // set the owning side to null (unless already changed)
            if ($actor->getCharacter() === $this) {
                $actor->setCharacter(null);
            }
        }

        return $this;
    }

    public function getChildren(): ?self
    {
        return $this->children;
    }

    public function setChildren(?self $children): static
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(self $parent): static
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
            $parent->setChildren($this);
        }

        return $this;
    }

    public function removeParent(self $parent): static
    {
        if ($this->parents->removeElement($parent)) {
            // set the owning side to null (unless already changed)
            if ($parent->getChildren() === $this) {
                $parent->setChildren(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSiblings(): Collection
    {
        return $this->siblings;
    }

    public function addSibling(self $sibling): static
    {
        if (!$this->siblings->contains($sibling)) {
            $this->siblings->add($sibling);
        }

        return $this;
    }

    public function removeSibling(self $sibling): static
    {
        $this->siblings->removeElement($sibling);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getKilled(): Collection
    {
        return $this->killed;
    }

    public function addKilled(self $killed): static
    {
        if (!$this->killed->contains($killed)) {
            $this->killed->add($killed);
        }

        return $this;
    }

    public function removeKilled(self $killed): static
    {
        $this->killed->removeElement($killed);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getKilledBy(): Collection
    {
        return $this->killedBy;
    }

    public function addKilledBy(self $killedBy): static
    {
        if (!$this->killedBy->contains($killedBy)) {
            $this->killedBy->add($killedBy);
            $killedBy->addKilled($this);
        }

        return $this;
    }

    public function removeKilledBy(self $killedBy): static
    {
        if ($this->killedBy->removeElement($killedBy)) {
            $killedBy->removeKilled($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, House>
     */
    public function getHouses(): Collection
    {
        return $this->houses;
    }

    public function addHouse(House $house): static
    {
        if (!$this->houses->contains($house)) {
            $this->houses->add($house);
        }

        return $this;
    }

    public function removeHouse(House $house): static
    {
        $this->houses->removeElement($house);

        return $this;
    }
}
