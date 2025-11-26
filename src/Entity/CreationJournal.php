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

    #[ORM\ManyToOne(inversedBy: 'creationJournals')]
    #[ORM\JoinColumn(nullable: false)]
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

    // Getters et setters...
}