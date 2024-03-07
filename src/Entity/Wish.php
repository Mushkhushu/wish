<?php

namespace App\Entity;

use App\Repository\WishRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
#[ORM\Table(name: 'wishes')]
#[ORM\HasLifecycleCallbacks()]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 250, nullable: false)]
    #[Assert\Length(max: 250, maxMessage: 'Le titre doit faire moins de {{ limit }} caractères.')]
    #[Assert\Length(min: 3, minMessage: 'Le titre doit faire plus de {{ limit }} caractères.')]
    private string $title;

    #[ORM\Column(name: 'description', type: Types::TEXT)]
    #[Assert\Length(min: 3, minMessage: 'La description doit faire plus de {{ limit }} caractères.')]
    private string $description;
    #[ORM\Column(name: 'author', type: Types::STRING, length: 50, nullable: false)]
    #[Assert\Length(max: 50, maxMessage: "L'auteur doit faire moins de {{ limit }} caractères.")]
    #[Assert\Length(min: 3, minMessage: "L'auteur doit faire plus de {{ limit }} caractères.")]
    private string $author;

    #[ORM\Column(name: 'is_published', type: Types::BOOLEAN, nullable: false)]
    private bool $isPublished;

    #[ORM\Column(name: 'date_created', type: Types::DATETIME_MUTABLE)]
    private DateTime $dateCreated;

    #[ORM\Column(name: 'picture', type: Types::STRING, length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    private ?Category $category = null;


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }


    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    #[ORM\PrePersist]
    public function setDateCreated(): static
    {
        $this->dateCreated = new \DateTime('now');
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }


}