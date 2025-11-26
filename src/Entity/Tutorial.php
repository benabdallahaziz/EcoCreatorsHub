<?php
// src/Entity/Tutorial.php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(length: 50)]
    private ?string $level = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'tutorials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    #[ORM\ManyToMany(targetEntity: Material::class, inversedBy: 'tutorials')]
    #[ORM\JoinTable(name: 'tutorial_material')]
    private Collection $materials;

    #[ORM\OneToMany(mappedBy: 'tutorial', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'tutorial', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'tutorial', targetEntity: TutorialResource::class, cascade: ['persist', 'remove'])]
    private Collection $resources;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Getters et setters...
}