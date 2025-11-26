<?php
// src/Entity/Like.php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?CreationJournal $creationJournal = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Tutorial $tutorial = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters...
}