<?php
// src/Entity/CreationJournal.php

namespace App\Entity;

use App\Repository\CreationJournalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreationJournalRepository::class)]
class CreationJournal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'json')]
    private array $images = [];

    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'creationJournals')]
    #[ORM\JoinColumn(nullable: true)]
    private ?JournalCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'creationJournals')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Artist $artist = null;

    #[ORM\OneToMany(mappedBy: 'creationJournal', targetEntity: CreationStep::class, cascade: ['persist', 'remove'])]
    private Collection $creationSteps;

    #[ORM\OneToMany(mappedBy: 'creationJournal', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'creationJournal', targetEntity: Like::class)]
    private Collection $likes;

    public function __construct()
    {
        $this->creationSteps = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->date = new \DateTime();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $dt): self
    {
        return $this->setDate($dt);
    }

    public function setUpdatedAt(\DateTimeInterface $dt): self
    {
        $this->date = $dt;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getSteps(): Collection
    {
        return $this->creationSteps;
    }

    public function getCreationSteps(): Collection
    {
        return $this->creationSteps;
    }

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getCategory(): ?JournalCategory
    {
        return $this->category;
    }

    public function setCategory(?JournalCategory $category): self
    {
        $this->category = $category;
        return $this;
    }
}