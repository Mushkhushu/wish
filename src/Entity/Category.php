<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Ce nom est déjà pris', errorPath: 'name')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: TYPES::INTEGER,nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 50, unique: true, nullable: true)]
    #[Assert\Length(min: 2, minMessage: 'Le nom doit faire au moins {{ limit }} caractères')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom doit faire au maximum {{ limit }} caractères')]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Wish::class, mappedBy: 'category')]
    private Collection $wishes;

    public function __construct()
    {
        $this->wishes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    /**
     * @return Collection<int, wish>
     */
    public function getWishes(): Collection
    {
        return $this->wishes;
    }

    public function addWish(wish $wish): static
    {
        if (!$this->wishes->contains($wish)) {
            $this->wishes->add($wish);
            $wish->setCategory($this);
        }

        return $this;
    }

    public function removeWish(wish $wish): static
    {
        if ($this->wishes->removeElement($wish)) {
            // set the owning side to null (unless already changed)
            if ($wish->getCategory() === $this) {
                $wish->setCategory(null);
            }
        }

        return $this;
    }
}
